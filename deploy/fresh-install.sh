#!/bin/bash
set -e

# ============================================
# EIES Fresh WordPress Installation Script
# Run: bash /home/marceloeies/public_html/testeoprevio/deploy/fresh-install.sh
# ============================================

SITE_DIR="/home/marceloeies/public_html/testeoprevio"
DB_NAME="marceloeies_testeo"
DB_USER="marceloeies_soporte"
DB_PASS='WXVCfhz(AOSp'
SITE_URL="https://testeoprevio.eies.com.bo"
REPO_URL="https://github.com/olakunlevpn/eies-source-code.git"
ADMIN_USER="mayahn"
ADMIN_PASS="Blackrap@12"
ADMIN_EMAIL="juankore@hotmail.com"

echo "=== EIES Fresh Install ==="
echo "Site: $SITE_URL"
echo "Path: $SITE_DIR"
echo ""

# Step 1: Clean directory (keep deploy script running)
echo "[1/9] Cleaning directory..."
find "$SITE_DIR" -mindepth 1 -not -name "deploy" -not -path "*/deploy/*" -delete 2>/dev/null || true
echo "  Done."

# Step 2: Download WordPress
echo "[2/9] Downloading WordPress 6.9.4..."
curl -sL -o /tmp/wp.zip https://wordpress.org/wordpress-6.9.4.zip
unzip -qo /tmp/wp.zip -d /tmp/
cp -R /tmp/wordpress/* "$SITE_DIR/"
rm -rf /tmp/wordpress /tmp/wp.zip
echo "  Done."

# Step 3: Clone and deploy our code
echo "[3/9] Deploying custom code from GitHub..."
rm -rf /tmp/eies-source-code
git clone --depth 1 "$REPO_URL" /tmp/eies-source-code
cp -R /tmp/eies-source-code/wp-content/plugins/* "$SITE_DIR/wp-content/plugins/"
cp -R /tmp/eies-source-code/wp-content/themes/* "$SITE_DIR/wp-content/themes/"
cp /tmp/eies-source-code/deploy/*.sh "$SITE_DIR/deploy/" 2>/dev/null || true
rm -rf /tmp/eies-source-code
echo "  Done."

# Step 4: Install free plugins
echo "[4/9] Installing free plugins from wordpress.org..."
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
for p in "${PLUGINS[@]}"; do
    curl -sL -o "/tmp/${p}.zip" "https://downloads.wordpress.org/plugin/${p}.latest-stable.zip"
    if [ -f "/tmp/${p}.zip" ] && file "/tmp/${p}.zip" | grep -q "Zip"; then
        unzip -qo "/tmp/${p}.zip" -d "$SITE_DIR/wp-content/plugins/"
        echo "  ✓ $p"
    else
        echo "  ✗ $p (download failed)"
    fi
    rm -f "/tmp/${p}.zip"
done
echo "  Done."

# Step 5: Copy livees-checkout from plugins-bad
echo "[5/9] Copying livees-checkout plugin..."
if [ -d "/home/marceloeies/public_html/wp-content/plugins-bad/livees-checkout" ]; then
    cp -R /home/marceloeies/public_html/wp-content/plugins-bad/livees-checkout "$SITE_DIR/wp-content/plugins/"
    echo "  ✓ livees-checkout copied"
elif [ -d "/home/marceloeies/public_html/plugins-bad/livees-checkout" ]; then
    cp -R /home/marceloeies/public_html/plugins-bad/livees-checkout "$SITE_DIR/wp-content/plugins/"
    echo "  ✓ livees-checkout copied"
else
    echo "  ✗ livees-checkout not found in plugins-bad"
fi
echo "  Done."

# Step 6: Create wp-config.php
echo "[6/9] Creating wp-config.php..."
cat > "$SITE_DIR/wp-config.php" << 'WPCONFIG'
<?php
define( 'WP_MEMORY_LIMIT', '1024M' );
define( 'WP_MAX_MEMORY_LIMIT', '1024M' );
define( 'WP_DEBUG', false );

define( 'DB_NAME', 'DB_NAME_PH' );
define( 'DB_USER', 'DB_USER_PH' );
define( 'DB_PASSWORD', 'DB_PASS_PH' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wp_';

define( 'AUTH_KEY',         'k8#mL3nP5qR7sU9vW0xZ1aB3dE5fG7iK' );
define( 'SECURE_AUTH_KEY',  'J2kL4mN6pQ8rT0uV1wY2zA4cE6fH8iK0' );
define( 'LOGGED_IN_KEY',    'lN3oP5qR7sU9vW0xZ1aB3dE5fG7hI9jL' );
define( 'NONCE_KEY',        'mO4pQ6rS8tV0wX1yA2bD4eF6hG8iK0lN' );
define( 'AUTH_SALT',        'nP5qR7sT9uW0xY1zA3cE5fG7hI9jL1mO' );
define( 'SECURE_AUTH_SALT', 'oQ6rS8tU0vX1yZ2aB4dF6gH8jK0lM2nP' );
define( 'LOGGED_IN_SALT',   'pR7sT9uV0wY1zA3bC5eG7hI9kL1mN3oQ' );
define( 'NONCE_SALT',       'qS8tU0vW1xZ2aB3cD5fH7iJ9kL1mN3oP' );

define( 'MOODLE_DB_HOST', 'localhost' );
define( 'MOODLE_DB_NAME', 'MOODLE_DB_PH' );
define( 'MOODLE_DB_USER', 'DB_USER_PH' );
define( 'MOODLE_DB_PASS', 'DB_PASS_PH' );
define( 'MOODLE_DATA_PATH', '/home/marceloeies/public_html/moodle-datos/filedir/' );
define( 'MOODLE_DB_PREFIX', 'mdl_' );

@ini_set( 'max_execution_time', 1800 );
@ini_set( 'max_input_vars', 10000 );
@ini_set( 'memory_limit', '1024M' );

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
WPCONFIG

# Replace placeholders
sed -i "s/DB_NAME_PH/${DB_NAME}/g" "$SITE_DIR/wp-config.php"
sed -i "s/DB_USER_PH/${DB_USER}/g" "$SITE_DIR/wp-config.php"
sed -i "s|DB_PASS_PH|${DB_PASS}|g" "$SITE_DIR/wp-config.php"
sed -i "s/MOODLE_DB_PH/marceloeies_moodle/g" "$SITE_DIR/wp-config.php"
echo "  Done."

# Step 7: Reset database
echo "[7/9] Resetting database..."
mysql -u "$DB_USER" -p"$DB_PASS" -e "DROP DATABASE IF EXISTS ${DB_NAME}; CREATE DATABASE ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
echo "  Done."

# Step 8: Set permissions
echo "[8/9] Setting permissions..."
find "$SITE_DIR" -type d -exec chmod 755 {} \;
find "$SITE_DIR" -type f -exec chmod 644 {} \;
chmod 600 "$SITE_DIR/wp-config.php"
mkdir -p "$SITE_DIR/wp-content/uploads"
chmod -R 777 "$SITE_DIR/wp-content/uploads"
echo "  Done."

# Step 9: Install WordPress via CLI
echo "[9/9] Installing WordPress..."
cd "$SITE_DIR"

# Use PHP to run the WP installer
php -r "
define('ABSPATH', '$SITE_DIR/');
define('WP_INSTALLING', true);
define('WP_ADMIN', true);

\$_SERVER['HTTP_HOST'] = 'testeoprevio.eies.com.bo';
\$_SERVER['REQUEST_URI'] = '/wp-admin/install.php';
\$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

require_once ABSPATH . 'wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

wp_install('EIES', '$ADMIN_USER', '$ADMIN_EMAIL', true, '', wp_slash('$ADMIN_PASS'));

// Set siteurl and home
update_option('siteurl', '$SITE_URL');
update_option('home', '$SITE_URL');

// Activate MasterStudy theme
switch_theme('masterstudy');

// Activate core plugins
\$plugins = array(
    'masterstudy-lms-learning-management-system/masterstudy-lms-learning-management-system.php',
    'masterstudy-lms-learning-management-system-pro/masterstudy-lms-learning-management-system-pro.php',
    'masterstudy-elementor-widgets/masterstudy-elementor-widgets.php',
    'stm-post-type/stm-post-type.php',
    'stm-gdpr-compliance/stm-gdpr-compliance.php',
    'elementor/elementor.php',
    'header-footer-elementor/header-footer-elementor.php',
    'js_composer/js_composer.php',
    'revslider/revslider.php',
    'woocommerce/woocommerce.php',
    'contact-form-7/wp-contact-form-7.php',
    'breadcrumb-navxt/breadcrumb-navxt.php',
    'buddypress/bp-loader.php',
    'paid-memberships-pro/paid-memberships-pro.php',
    'add-to-any/addtoany.php',
    'envato-market/envato-market.php',
    'eies-migration/eies-migration.php',
);

require_once ABSPATH . 'wp-admin/includes/plugin.php';
foreach (\$plugins as \$p) {
    if (file_exists(WP_PLUGIN_DIR . '/' . \$p)) {
        activate_plugin(\$p);
    }
}

// Create migration mapping table
global \$wpdb;
\$charset = \$wpdb->get_charset_collate();
\$table = \$wpdb->prefix . 'eies_migration_map';
\$wpdb->query(\"CREATE TABLE IF NOT EXISTS {\$table} (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    entity_type varchar(50) NOT NULL,
    moodle_id bigint(20) unsigned NOT NULL,
    wp_id bigint(20) unsigned NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY entity_moodle (entity_type, moodle_id),
    KEY wp_id (wp_id)
) {\$charset}\");

echo 'WordPress installed and configured!';
"

echo ""
echo "=== Fresh Install Complete ==="
echo ""
echo "Site: $SITE_URL"
echo "Admin: $SITE_URL/wp-admin/"
echo "User: $ADMIN_USER"
echo ""
echo "Next steps:"
echo "1. Visit $SITE_URL/wp-admin/ and verify theme is active"
echo "2. Go to STM LMS > Demo Import and import a demo design"
echo "3. Go to Tools > EIES Migration and run all 12 steps in order"
echo ""
