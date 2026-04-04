# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is the **EIES** WordPress site — a comprehensive education & e-learning platform. It combines an LMS (MasterStudy), e-commerce (WooCommerce), membership management (Paid Memberships Pro), and community features (BuddyPress).

## Local Development

- **Server:** Laravel Herd (serves from `/Users/kunle/Herd/eies/`)
- **Database:** `marceloeies_restore` on `localhost` (MySQL, user `root`, no password)
- **Table prefix:** `wp_`
- **Debug mode:** Off by default — set `WP_DEBUG` to `true` in `wp-config.php` when developing
- **URL:** Access via Herd's local domain (typically `eies.test`)
- **No build tools** at root level — theme assets are pre-compiled

## Theme Architecture

**Active theme:** `masterstudy` (parent) + `masterstudy-child` (child theme)

### Parent Theme (`wp-content/themes/masterstudy/`)
- `functions.php` — Defines `STM_*` constants, conditionally loads integrations (WooCommerce, Elementor, Visual Composer, BuddyPress, LMS). Contains custom payment gateway logic for multi-currency (BOB/Bolivianos triggers `livees-checkout` plugin).
- `inc/setup.php` — Theme setup and initialization
- `inc/custom.php` — Extensive output modifications, custom comment templates, SVG support, filters/hooks (~103KB)
- `inc/scripts_styles.php` — Asset enqueueing
- `inc/theme-config.php` — Theme option configuration
- `inc/woocommerce_setups.php` — WooCommerce customizations
- `inc/lms/main.php` — MasterStudy LMS theme customizations (only loads when `STM_LMS_URL` is defined)
- `inc/buddypress.php` — BuddyPress customizations
- `inc/elementor.php` — Elementor widget integration
- `inc/visual_composer.php` — WP Bakery/Visual Composer modules
- `inc/header.php`, `inc/footer.php` — Layout action hooks

### Child Theme (`wp-content/themes/masterstudy-child/`)
- `functions.php` — Enqueues Bootstrap, Font Awesome, icomoon, FancyBox, Select2, and multiple color skin CSS files
- `style.css` — Child theme overrides
- All custom work should go here to survive theme updates

## Key Plugins (45 active)

| Category | Plugin |
|----------|--------|
| **LMS** | masterstudy-lms-learning-management-system (v2.9.31) + Pro |
| **E-commerce** | WooCommerce (v9.0.4) |
| **Payments** | pymntpl-paypal-woocommerce, livees-checkout (BOB currency only) |
| **Currency** | woocommerce-currency-switcher (WOOCS global) |
| **Memberships** | paid-memberships-pro |
| **Page Builder** | Elementor (v3.29.2), js_composer (Visual Composer) |
| **Community** | BuddyPress |
| **SEO** | all-in-one-seo-pack |
| **Performance** | autoptimize, wp-fastest-cache, jetpack-boost |
| **Forms** | contact-form-7 |
| **Translation** | loco-translate |

Disabled/problematic plugins are in `wp-content/plugins-bad/`.

## Important Patterns

### Currency-Based Payment Gateway Switching
In `masterstudy/functions.php`, the `desactivar_metodo_pago_en_carrito` filter dynamically activates/deactivates the `livees-checkout` plugin at checkout based on the current WOOCS currency. BOB (Bolivianos) activates it; other currencies deactivate it and remove the gateway. Access the current currency via `$GLOBALS['WOOCS']->current_currency`.

### Custom Database Table
- `sys_evaluaciones` — Stores course evaluation results (custom table, not a standard WP table)

### Theme Constants
All defined in `masterstudy/functions.php`:
- `STM_THEME_VERSION`, `STM_THEME_NAME`, `STM_TEMPLATE_DIR`, `STM_TEMPLATE_URI`, `STM_INC_PATH`

## WP-CLI Commands (if available)

```bash
wp plugin list                    # List all plugins and status
wp theme list                     # List themes
wp db query "SELECT..."           # Run database queries
wp cache flush                    # Clear object cache
wp option get siteurl             # Get site URL
wp user list                      # List users
```

## Notes

- Spanish is the primary language — many comments and function names are in Spanish
- The site uses 148 language directories in `wp-content/languages/`
- Authentication keys/salts in `wp-config.php` are placeholder values (not yet configured)
- A 335MB database dump (`marceloeies_restore.sql`) exists in the root directory
