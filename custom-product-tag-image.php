<?php

/**
 * Plugin Name: Custom Product Tag Image
 * Description: WooCommerce extension to customize product tag images and display settings.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: custom-product-tag-image
 */

defined('ABSPATH') || exit;

// Define plugin constants
define('CPTI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CPTI_PLUGIN_URI', plugin_dir_url(__FILE__));
define('CPTI_PLUGIN_VERSION', '1.0.0');

// Load Composer autoload
require_once CPTI_PLUGIN_DIR . 'vendor/autoload.php';

use CustomProductTagImage\CustomProductTagImage;
use CustomProductTagImage\TagManager;

// Initialize the plugin after WooCommerce is loaded
add_action('woocommerce_init', function () {
    if (class_exists(CustomProductTagImage::class)) {
        $plugin = new CustomProductTagImage();
        $plugin->init();
    }
});

// Load into the wp hook
add_action('wp', function () {
    TagManager::hookDisplayTagsOnArchivePage();
    TagManager::hookDisplayTagsOnProductPage();
});
