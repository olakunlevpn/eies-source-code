#!/bin/bash
set -e

# ============================================
# EIES Cutover Day: Refresh Source Databases
# ============================================
# Drops and reimports the Moodle + old WP source DBs from fresh dumps,
# then the existing migration plugin (idempotent via wp_eies_migration_map)
# picks up all new records created since the initial snapshot.
#
# Usage:
#   bash refresh-source-dumps.sh <moodle_dump.sql[.gz]> [old_wp_dump.sql[.gz]]
# ============================================

MOODLE_DB="marceloeies_moodle"
OLD_WP_DB="marceloeies_oldwp"
DB_USER="${DB_USER:-marceloeies_soporte}"
DB_PASS="${DB_PASS:-WXVCfhz(AOSp}"
SITE_URL="${SITE_URL:-https://testeoprevio.eies.com.bo}"

MOODLE_DUMP="$1"
OLDWP_DUMP="$2"

if [ -z "$MOODLE_DUMP" ]; then
    echo "ERROR: Moodle dump path required."
    echo "Usage: bash refresh-source-dumps.sh <moodle.sql[.gz]> [old_wp.sql[.gz]]"
    exit 1
fi

if [ ! -f "$MOODLE_DUMP" ]; then
    echo "ERROR: File not found: $MOODLE_DUMP"
    exit 1
fi

import_dump() {
    local db="$1"
    local dump="$2"

    echo "[*] Refreshing $db from $dump"
    mysql -u "$DB_USER" -p"$DB_PASS" -e "DROP DATABASE IF EXISTS ${db}; CREATE DATABASE ${db} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

    if [[ "$dump" == *.gz ]]; then
        gunzip -c "$dump" | mysql -u "$DB_USER" -p"$DB_PASS" "$db"
    else
        mysql -u "$DB_USER" -p"$DB_PASS" "$db" < "$dump"
    fi

    local rows
    rows=$(mysql -u "$DB_USER" -p"$DB_PASS" -Nse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${db}';")
    echo "    Done. $rows tables restored."
}

echo "=== EIES Source DB Refresh ==="
echo "Timestamp: $(date)"
echo ""

# Step 1: Moodle
import_dump "$MOODLE_DB" "$MOODLE_DUMP"

# Step 2: Old WP (optional)
if [ -n "$OLDWP_DUMP" ] && [ -f "$OLDWP_DUMP" ]; then
    import_dump "$OLD_WP_DB" "$OLDWP_DUMP"
fi

echo ""
echo "=== Source DBs Refreshed ==="
echo ""

# Quick audit
echo "Moodle:"
mysql -u "$DB_USER" -p"$DB_PASS" "$MOODLE_DB" -e "
SELECT 'users' AS tbl, COUNT(*) AS total FROM mdl_user WHERE deleted=0
UNION SELECT 'courses', COUNT(*) FROM mdl_course WHERE id > 1
UNION SELECT 'enrollments', COUNT(*) FROM mdl_user_enrolments
UNION SELECT 'certificates', COUNT(*) FROM mdl_customcert_issues;"

if [ -n "$OLDWP_DUMP" ] && [ -f "$OLDWP_DUMP" ]; then
    echo ""
    echo "Old WP:"
    mysql -u "$DB_USER" -p"$DB_PASS" "$OLD_WP_DB" -e "
SELECT 'users' AS tbl, COUNT(*) AS total FROM wp_users
UNION SELECT 'orders', COUNT(*) FROM wp_posts WHERE post_type='shop_order'
UNION SELECT 'products', COUNT(*) FROM wp_posts WHERE post_type='product' AND post_status='publish';"
fi

echo ""
echo "=== Next Steps ==="
echo ""
echo "1. Visit: ${SITE_URL}/wp-admin/tools.php?page=eies-migration"
echo "   Re-run steps 2, 6, 8, 10 (users, enrollments, WP users, orders)"
echo "   — safe to re-run, only new records will be inserted."
echo ""
echo "2. Visit: ${SITE_URL}/wp-admin/admin.php?page=eies-certificates"
echo "   Click 'Buscar nuevos certificados' to import new Moodle certificates."
echo ""
echo "3. Verify new record counts match expectations, then switch DNS."
echo ""
