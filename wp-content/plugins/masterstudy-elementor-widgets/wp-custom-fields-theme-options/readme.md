# WordPress Custom Fields & Theme Options
[![Latest Version](https://img.shields.io/badge/release-v1.0.0-blue?style=flat-square)](https://github.com/StylemixThemes/wp-custom-fields-theme-options/releases)
[![GNU Licensed](https://img.shields.io/badge/license-GNU%20v3.0-brightgreen)](https://github.com/StylemixThemes/wp-custom-fields-theme-options/blob/master/LICENSE)

We love Vue.js and WordPress so we tried to combine this two awesome techniques to build something to make development easier and faster.
Every field is a Vue Component, which saves in global data via WordPress default functions. Framework positioned as developer helper to build complicated options with extra logic, encapsulated from main Application.
    

## Three-in-one

Today we have a lot of frameworks to add an options page and custom metaboxes to post or custom post type in WordPress.
Yet, we wanted to create a framework where you can create fields to use in options page, post types and even in categories or taxonomies.
So, in one framework you can create all options-related stuff in different plugins/themes.

## Documentation

### Installation

Move downloaded folder `wp-custom-fields-theme-options` into your plugin and include it:

```php
require_once(dirname(__FILE__) . '/wp-custom-fields-theme-options/WPCFTO.php');
```

### Theme Options

The framework will create a menu or submenu item in the WordPress menu. The script will auto-load itself on the correct admin hooks. You need to add a filter with next parameters:

```php
add_filter('wpcfto_options_page_setup', function ($setups) {
    $setup[] = array(
        /*
         * Here we specify option name. It will be a key for storing in wp_options table
         */
        'option_name' => 'my_awesome_settings',

        /*
         * Next we add a page to display our awesome settings.
         * All parameters are required and same as WordPress add_menu_page.
         */
        'page' => array(
            'page_title' => 'Awesome Settings',
            'menu_title' => 'Settings',
            'menu_slug' => 'my_awesome_settings',
            'icon' => 'dashicons-editor-unlink',
            'position' => 40,
        ),

        /*
         * And Our fields to display on a page. We use tabs to separate settings on groups.
         */
        'fields' => array(
            // Even single tab should be specified
            'tab_1' => array(
                // And its name obviously
                'name' => esc_html__('Tab 1', 'my-domain'),
                'fields' => array(
                    // Field key and its settings. Full info about fields read in documentation.
                    'awesome_1' => array(
                        'type' => 'text',
                        'label' => esc_html__('Awesome Field label', 'my-domain'),
                        'value' => 'Awesome default value',
                    ),
                    'awesome_2' => array(
                        'type' => 'text',
                        'label' => esc_html__('Awesome Field label 2', 'my-domain'),
                        'value' => 'Awesome default value 2',
                    ),
                )
            ),

           /*
            * Other tabs you can add below
            */
            ....
        )
    );

    return $setup;
});
```

And in your template, you can get your option as:

```php
$my_awesome_options = get_option('my_awesome_settings', array());
/*
 * Where 'my_awesome_settings' is the same setup option name.
 */
```

### Custom Fields

WordPress powered with Post Type meta boxes. Add Meta Box to the desired post type with a unique ID.

```php
add_filter('stm_wpcfto_boxes', function ($boxes) {

    $boxes['my_metabox'] = array(
        'post_type' => array('post', 'my-custom-post-type'),
        'label' => esc_html__('Post settings', 'my-domain'),
    );

    return $boxes;
});
```

And we need to add fields to this Meta Box:

```php
add_filter('stm_wpcfto_fields', function ($fields) {

    $fields['my_metabox'] = array(

        'tab_1' => array(
            'name' => esc_html__('Field Settings', 'my-domain'),
            'fields' => array(
                'field_1' => array(
                    'type' => 'text',
                    'label' => esc_html__('Field text', 'my-domain'),
                ),
            )
        ),

    );

    return $fields;
});
```

### Custom Fields

You can easily use default WordPress functions such as `get_post_meta()`, `get_option()` or `get_term_meta()`. But for the option we built-in function to get any param you want:

```php
$options = stm_wpcfto_get_options('my_awesome_settings');
```

## [Full Documentation](https://support.stylemixthemes.com/manuals/wp-custom-fields-theme-options/)

- [Installation](https://support.stylemixthemes.com/manuals/wp-custom-fields-theme-options/#installation)
- [Theme Options page](https://support.stylemixthemes.com/manuals/wp-custom-fields-theme-options/#theme-options-page)
- [Custom Fields](#)
	- [Post type Custom Fields](https://support.stylemixthemes.com/manuals/wp-custom-fields-theme-options/#post-type-custom-fields)
	- [Category Custom Fields](https://support.stylemixthemes.com/manuals/wp-custom-fields-theme-options/#category-custom-fields)
- [Getting Fields](https://support.stylemixthemes.com/manuals/wp-custom-fields-theme-options/#getting-fields)
- [Available Fields](https://support.stylemixthemes.com/manuals/wp-custom-fields-theme-options/#available-fields)
- [Adding own Custom Field](https://support.stylemixthemes.com/manuals/wp-custom-fields-theme-options/#adding-own-custom-field)