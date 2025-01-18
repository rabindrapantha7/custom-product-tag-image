<?php

namespace CustomProductTagImage;

class TagManager {
    const TAG_IMAGE_META_KEY = 'tag_icon';

    /**
     * Initialize admin features.
     */
    public static function initAdminFeatures() {
        add_action('product_tag_add_form_fields', [self::class, 'addTagImageField']);
        add_action('product_tag_edit_form_fields', [self::class, 'editTagImageField']);
        add_action('created_product_tag', [self::class, 'saveTagImage'], 10, 2);
        add_action('edited_product_tag', [self::class, 'saveTagImage'], 10, 2);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAdminScripts']);
    }

    /**
     * Add the tag image upload field on the product tag add form.
     */
    public static function addTagImageField() {
?>
        <div class="form-field">
            <label for="tag_icon"><?php _e('Tag Image', 'custom-product-tag-image'); ?></label>
            <input type="button" class="button tag-icon-upload" value="<?php _e('Upload Image', 'custom-product-tag-image'); ?>">
            <input type="hidden" id="tag_icon" name="tag_icon" value="">
            <div id="tag-icon-preview"></div>
        </div>
    <?php
    }

    /**
     * Edit the tag image field on the product tag edit form.
     */
    public static function editTagImageField($term) {
        $tagIcon = get_term_meta($term->term_id, self::TAG_IMAGE_META_KEY, true);
    ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="tag_icon"><?php _e('Tag Image', 'custom-product-tag-image'); ?></label>
            </th>
            <td>
                <input type="button" class="button tag-icon-upload" value="<?php _e('Upload Image', 'custom-product-tag-image'); ?>">
                <input type="hidden" id="tag_icon" name="tag_icon" value="<?php echo esc_attr($tagIcon); ?>">
                <div id="tag-icon-preview">
                    <?php if ($tagIcon): ?>
                        <img src="<?php echo esc_url($tagIcon); ?>" style="max-width: 100px; max-height: 100px;">
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php
    }

    /**
     * Save the tag image when a new tag is created or edited.
     */
    public static function saveTagImage($termId) {
        if (isset($_POST['tag_icon'])) {
            update_term_meta($termId, self::TAG_IMAGE_META_KEY, esc_url_raw($_POST['tag_icon']));
        }
    }

    /**
     * Enqueue admin scripts and styles for media uploader and plugin functionality.
     */
    public static function enqueueAdminScripts() {
        wp_enqueue_media();

        // Enqueue admin CSS
        wp_enqueue_style(
            'custom-tag-image-admin-style',
            CPTI_PLUGIN_URI . 'assets/css/admin.css',
            [],
            CPTI_PLUGIN_VERSION
        );

        // Enqueue admin JS
        wp_enqueue_script(
            'custom-tag-image-admin-script',
            CPTI_PLUGIN_URI . 'assets/js/admin.js',
            ['jquery'],
            CPTI_PLUGIN_VERSION,
            true
        );
    }

    /**
     * Get all product tags.
     */
    public static function getProductTags() {
        return get_terms([
            'taxonomy' => 'product_tag',
            'hide_empty' => false,
        ]);
    }

    /**
     * Render a product tag with image and name.
     */
    public static function renderTag($tag) {
        $icon = get_term_meta($tag->term_id, self::TAG_IMAGE_META_KEY, true);
        $settings = get_option('cpti_settings', []); // Assuming the settings are stored under 'cpti_settings'
        $displayFormat = isset($settings['display_format']) ? $settings['display_format'] : 'text';
        $tagShape = isset($settings['tag_shape']) ? $settings['tag_shape'] : 'square';
        $fontSize = isset($settings['font_size']) ? $settings['font_size'] . 'px' : '14px';
        $fontFamily = isset($settings['font_family']) ? $settings['font_family'] : 'inherit';

        // Start rendering the tag
        ob_start();
    ?>
        <div class="custom-product-tag" style="font-size: <?php echo esc_attr($fontSize); ?>; font-family: <?php echo esc_attr($fontFamily); ?>;">
            <?php if ($icon && ($displayFormat == 'icon' || $displayFormat == 'both')): ?>
                <img src="<?php echo esc_url($icon); ?>" alt="<?php echo esc_attr($tag->name); ?>" class="tag-icon" style="<?php echo $tagShape == 'round' ? 'border-radius: 50%;' : ''; ?>" />
            <?php endif; ?>
            <?php if ($displayFormat == 'text' || $displayFormat == 'both'): ?>
                <span class="tag-text"><?php echo esc_html($tag->name); ?></span>
            <?php endif; ?>
        </div>
<?php
        return ob_get_clean();
    }

    /**
     * Display product tags on the product page based on settings.
     */
    public static function displayProductTags($productId) {
        $settings = get_option('cpti_settings', []); // Assuming the settings are stored under 'cpti_settings'
        if (empty($settings['visibility'])) {
            return; // Do not display if visibility is off
        }

        $tags = wp_get_post_terms($productId, 'product_tag');
        if (empty($tags)) {
            return;
        }

        // Get the position where tags should be displayed
        $displayPosition = isset($settings['display_position']) ? $settings['display_position'] : 'after_price';

        // Determine the display position on the product page
        $hookMap = [
            'before_description' => ['hook' => 'woocommerce_single_product_summary', 'priority' => 5],
            'after_description'  => ['hook' => 'woocommerce_after_single_product_summary', 'priority' => 10],
            'before_price'       => ['hook' => 'woocommerce_single_product_summary', 'priority' => 15],
            'after_price'        => ['hook' => 'woocommerce_single_product_summary', 'priority' => 20],
            'before_cart'        => ['hook' => 'woocommerce_single_product_summary', 'priority' => 25],
            'after_cart'         => ['hook' => 'woocommerce_after_add_to_cart_button', 'priority' => 10],
        ];

        if (isset($hookMap[$displayPosition])) {
            $hook = $hookMap[$displayPosition]['hook'];
            $priority = $hookMap[$displayPosition]['priority'];
            add_action($hook, function () use ($tags) {
                echo '<div class="custom-product-tags-wrapper">';
                foreach ($tags as $tag) {
                    echo self::renderTag($tag);
                }
                echo '</div>';
            }, $priority);
        }
    }

    /**
     * Hook into product page to trigger tag display.
     */
    public static function hookDisplayTagsOnProductPage() {
        // Hook into the WooCommerce product page
        if (is_product()) {
            global $post;
            $product = wc_get_product($post);
            self::displayProductTags($product->get_id());
        }
    }

    /**
     * Hook into archive page to trigger tag display.
     */
    public static function hookDisplayTagsOnArchivePage() {
        // Hook into the WooCommerce archive pages
        if (is_product()) return; // Skip single product pages
        self::displayProductTags(get_the_ID());
    }
}
