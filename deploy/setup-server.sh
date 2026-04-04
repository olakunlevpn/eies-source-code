#!/bin/bash
# EIES WordPress Setup Script
# Run this on the server via cPanel Terminal
# Usage: bash setup-server.sh

set -e

SITE_DIR="/home/marceloeies/public_html/testeoprevio"
WP_VERSION="6.9.4"
DB_NAME="marceloeies_testeo"
DB_USER="marceloeies_soporte"
DB_PASS="WXVCfhz(AOSp)"
SITE_URL="https://testeoprevio.eies.com.bo"

echo "=== EIES WordPress Setup ==="
echo "Installing to: $SITE_DIR"
echo ""

# Step 1: Download and extract WordPress
echo "[1/7] Downloading WordPress ${WP_VERSION}..."
cd /tmp
curl -sL -o wordpress.zip "https://wordpress.org/wordpress-${WP_VERSION}.zip"
unzip -qo wordpress.zip
cp -R wordpress/* "$SITE_DIR/"
rm -rf wordpress wordpress.zip
echo "  Done."

# Step 2: Clone our custom code from GitHub
echo "[2/7] Cloning custom code from GitHub..."
cd /tmp
git clone --depth 1 https://github.com/olakunlevpn/eies-source-code.git eies-code
cp -R eies-code/wp-content/plugins/eies-migration "$SITE_DIR/wp-content/plugins/"
cp -R eies-code/wp-content/themes/masterstudy-child "$SITE_DIR/wp-content/themes/"
cp eies-code/docs/plan.md "$SITE_DIR/" 2>/dev/null || true
rm -rf eies-code
echo "  Done."

# Step 3: Create wp-config.php
echo "[3/7] Creating wp-config.php..."
cat > "$SITE_DIR/wp-config.php" << 'WPCONFIG'
<?php
define( 'WP_MEMORY_LIMIT', '512M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
define( 'DISABLE_WP_CRON', true );
define( 'WP_DEBUG', false );

define( 'DB_NAME', 'DB_NAME_PLACEHOLDER' );
define( 'DB_USER', 'DB_USER_PLACEHOLDER' );
define( 'DB_PASSWORD', 'DB_PASS_PLACEHOLDER' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wp_';

define( 'AUTH_KEY',         'GENERATE_NEW_KEY_1' );
define( 'SECURE_AUTH_KEY',  'GENERATE_NEW_KEY_2' );
define( 'LOGGED_IN_KEY',    'GENERATE_NEW_KEY_3' );
define( 'NONCE_KEY',        'GENERATE_NEW_KEY_4' );
define( 'AUTH_SALT',        'GENERATE_NEW_KEY_5' );
define( 'SECURE_AUTH_SALT', 'GENERATE_NEW_KEY_6' );
define( 'LOGGED_IN_SALT',   'GENERATE_NEW_KEY_7' );
define( 'NONCE_SALT',       'GENERATE_NEW_KEY_8' );

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

@ini_set( 'max_execution_time', 600 );
@ini_set( 'max_input_vars', 10000 );

require_once ABSPATH . 'wp-settings.php';
WPCONFIG

# Replace placeholders
sed -i "s/DB_NAME_PLACEHOLDER/${DB_NAME}/g" "$SITE_DIR/wp-config.php"
sed -i "s/DB_USER_PLACEHOLDER/${DB_USER}/g" "$SITE_DIR/wp-config.php"
sed -i "s/DB_PASS_PLACEHOLDER/${DB_PASS}/g" "$SITE_DIR/wp-config.php"

# Generate unique keys
KEYS=$(curl -sL https://api.wordpress.org/secret-key/1.1/salt/)
if [ -n "$KEYS" ]; then
    # Replace placeholder keys with real ones
    for i in 1 2 3 4 5 6 7 8; do
        KEY=$(echo "$KEYS" | sed -n "${i}p" | sed "s/define('//;s/'.*//")
        VALUE=$(echo "$KEYS" | sed -n "${i}p" | sed "s/.*'[^']*', *'//;s/').*//")
        sed -i "s/GENERATE_NEW_KEY_${i}/${VALUE}/g" "$SITE_DIR/wp-config.php" 2>/dev/null || true
    done
fi
echo "  Done."

# Step 4: Install MasterStudy theme
echo "[4/7] Downloading MasterStudy theme and bundled plugins..."
# Note: Theme needs to be uploaded manually from the masterstudy_v4.8.139_package.zip
# For now, create placeholder directories
mkdir -p "$SITE_DIR/wp-content/themes/masterstudy"
echo "  Theme needs manual upload from masterstudy_v4.8.139_package.zip"

# Step 5: Install free plugins from WordPress.org
echo "[5/7] Installing free plugins..."
PLUGINS=(
    "elementor"
    "header-footer-elementor"
    "woocommerce"
    "contact-form-7"
    "breadcrumb-navxt"
    "buddypress"
    "paid-memberships-pro"
    "add-to-any"
)

for plugin in "${PLUGINS[@]}"; do
    echo "  Installing $plugin..."
    curl -sL -o "/tmp/${plugin}.zip" "https://downloads.wordpress.org/plugin/${plugin}.latest-stable.zip"
    if [ -f "/tmp/${plugin}.zip" ] && file "/tmp/${plugin}.zip" | grep -q "Zip"; then
        unzip -qo "/tmp/${plugin}.zip" -d "$SITE_DIR/wp-content/plugins/"
        echo "    ✓ $plugin"
    else
        echo "    ✗ $plugin (download failed)"
    fi
    rm -f "/tmp/${plugin}.zip"
done
echo "  Done."

# Step 6: Install MasterStudy LMS Pro via Composer
echo "[6/7] Installing MasterStudy LMS Pro via Composer..."
cd "$SITE_DIR"
cat > composer.json << 'COMPOSER'
{
    "name": "eies/wordpress",
    "require": {
        "freemius/masterstudy-lms-learning-management-system-pro": "4.8.14"
    },
    "repositories": [{
        "type": "composer",
        "url": "https://composer.freemius.com/packages.json"
    }],
    "config": {
        "allow-plugins": {
            "composer/installers": true
        }
    }
}
COMPOSER

cat > auth.json << 'AUTH'
{
    "http-basic": {
        "composer.freemius.com": {
            "username": "chelo.mqm@gmail.com",
            "password": "pGyC4AgC4uhqOfMao65yvG7A3Lu0g9sKFko0iZx-XDE"
        }
    }
}
AUTH

if command -v composer &> /dev/null; then
    composer install --no-dev --no-interaction 2>/dev/null
    echo "  ✓ LMS Pro installed via Composer"
else
    echo "  ✗ Composer not found - install LMS Pro manually"
fi
echo "  Done."

# Step 7: Install MasterStudy LMS free
echo "[7/7] Installing MasterStudy LMS free..."
curl -sL -o "/tmp/ms-lms.zip" "https://downloads.wordpress.org/plugin/masterstudy-lms-learning-management-system.latest-stable.zip"
unzip -qo "/tmp/ms-lms.zip" -d "$SITE_DIR/wp-content/plugins/"
rm -f /tmp/ms-lms.zip
echo "  Done."

# Set permissions
echo ""
echo "Setting permissions..."
find "$SITE_DIR" -type d -exec chmod 755 {} \;
find "$SITE_DIR" -type f -exec chmod 644 {} \;
chmod 600 "$SITE_DIR/wp-config.php"
chmod 600 "$SITE_DIR/auth.json" 2>/dev/null

echo ""
echo "=== Setup Complete ==="
echo ""
echo "Next steps:"
echo "1. Create database '${DB_NAME}' in cPanel MySQL Databases"
echo "2. Upload masterstudy_v4.8.139_package.zip theme via WP Admin"
echo "3. Upload bundled plugins (js_composer, revslider, stm-post-type, stm-gdpr-compliance)"
echo "4. Visit ${SITE_URL}/wp-admin/install.php to run WordPress installer"
echo "5. Activate theme and plugins"
echo "6. Go to Tools > EIES Migration to run the data import"
echo ""
echo "Migration plugin DB config to update in:"
echo "  ${SITE_DIR}/wp-content/plugins/eies-migration/eies-migration.php"
echo "  Change MOODLE_DB_NAME to 'marceloeies_moodle'"
echo "  Change MOODLE_DB_USER to 'marceloeies_soporte'"
echo "  Change MOODLE_DB_PASS to the Moodle password"
