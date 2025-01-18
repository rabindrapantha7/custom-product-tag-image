<?php

namespace CustomProductTagImage;

class ShortcodeHandler {

    /**
     * Register the [ka_tag_display] shortcode.
     */
    public static function registerShortcode() {
        add_shortcode('ka_tag_display', [self::class, 'displayTagsShortcode']);
    }

    /**
     * Display product tags via shortcode.
     */
    public static function displayTagsShortcode($atts) {
        global $post;

        // Ensure we're on a single product page or archive
        if (!is_singular('product') && !is_product_category()) {
            return ''; // Return empty if not on a product detail page or archive
        }

        // Use TagManager to get and display tags
        return TagManager::displayProductTags($post->ID);
    }
}
