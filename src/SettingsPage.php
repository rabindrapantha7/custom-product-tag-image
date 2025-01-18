<?php

namespace CustomProductTagImage;

class SettingsPage {
    const OPTION_NAME = 'cpti_settings';

    /**
     * Registers the settings page under WooCommerce.
     */
    public static function registerSettingsPage() {
        add_submenu_page(
            'woocommerce', // Parent menu (WooCommerce)
            __('Product Tag Image', 'custom-product-tag-image'), // Page title
            __('Product Tag Image', 'custom-product-tag-image'), // Menu title
            'manage_options', // Capability
            'cpti-settings', // Menu slug
            [self::class, 'renderSettingsPage'], // Callback to render the settings page
            100 // Position: Added as the last menu item under WooCommerce
        );
    }

    /**
     * Registers plugin settings.
     */
    public static function registerSettings() {
        register_setting(self::OPTION_NAME, self::OPTION_NAME);

        add_settings_section(
            'cpti_general_section',
            __('General Settings', 'custom-product-tag-image'),
            null,
            'cpti-settings'
        );

        // Visibility toggle
        add_settings_field(
            'visibility',
            __('Show Tags on Product Pages', 'custom-product-tag-image'),
            [self::class, 'renderCheckbox'],
            'cpti-settings',
            'cpti_general_section',
            [
                'label_for' => 'visibility',
                'description' => 'Turn tags on/off for product pages',
            ]
        );

        // Display position
        add_settings_field(
            'display_position',
            __('Tag Display Position', 'custom-product-tag-image'),
            [self::class, 'renderSelect'],
            'cpti-settings',
            'cpti_general_section',
            [
                'label_for' => 'display_position',
                'options' => [
                    'before_description' => __('Before Description', 'custom-product-tag-image'),
                    'after_description' => __('After Description', 'custom-product-tag-image'),
                    'before_price' => __('Before Price', 'custom-product-tag-image'),
                    'after_price' => __('After Price', 'custom-product-tag-image'),
                    'before_cart' => __('Before Add to Cart', 'custom-product-tag-image'),
                    'after_cart' => __('After Add to Cart', 'custom-product-tag-image'),
                ],
            ]
        );

        // Display format
        add_settings_field(
            'display_format',
            __('Tag Display Format', 'custom-product-tag-image'),
            [self::class, 'renderSelect'],
            'cpti-settings',
            'cpti_general_section',
            [
                'label_for' => 'display_format',
                'options' => [
                    'text' => __('Text', 'custom-product-tag-image'),
                    'icon' => __('Icon', 'custom-product-tag-image'),
                    'both' => __('Both (Text and Icon)', 'custom-product-tag-image'),
                ],
            ]
        );

        // Tag shape
        add_settings_field(
            'tag_shape',
            __('Tag Shape', 'custom-product-tag-image'),
            [self::class, 'renderSelect'],
            'cpti-settings',
            'cpti_general_section',
            [
                'label_for' => 'tag_shape',
                'options' => [
                    'square' => __('Square', 'custom-product-tag-image'),
                    'round' => __('Round', 'custom-product-tag-image'),
                ],
            ]
        );

        // Font size and family
        add_settings_field(
            'font_size',
            __('Font Size', 'custom-product-tag-image'),
            [self::class, 'renderInput'],
            'cpti-settings',
            'cpti_general_section',
            [
                'label_for' => 'font_size',
                'type' => 'number',
                'description' => 'Set font size in pixels',
            ]
        );

        add_settings_field(
            'font_family',
            __('Font Family', 'custom-product-tag-image'),
            [self::class, 'renderInput'],
            'cpti-settings',
            'cpti_general_section',
            [
                'label_for' => 'font_family',
                'type' => 'text',
                'description' => 'Enter a font family (e.g., Arial, sans-serif)',
            ]
        );
    }

    /**
     * Renders the settings page HTML.
     */
    public static function renderSettingsPage() {
?>
        <div class="wrap">
            <h1><?php _e('Product Tag Image Settings', 'custom-product-tag-image'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields(self::OPTION_NAME);
                do_settings_sections('cpti-settings');
                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    /**
     * Renders a checkbox input field.
     */
    public static function renderCheckbox($args) {
        $options = get_option(self::OPTION_NAME);
        $value = $options[$args['label_for']] ?? '';
    ?>
        <input type="checkbox" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr(self::OPTION_NAME . '[' . $args['label_for'] . ']'); ?>" value="1" <?php checked($value, '1'); ?>>
        <p class="description"><?php echo esc_html($args['description']); ?></p>
    <?php
    }

    /**
     * Renders a select dropdown field.
     */
    public static function renderSelect($args) {
        $options = get_option(self::OPTION_NAME);
        $value = $options[$args['label_for']] ?? '';
    ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr(self::OPTION_NAME . '[' . $args['label_for'] . ']'); ?>">
            <?php foreach ($args['options'] as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    <?php
    }

    /**
     * Renders a text or number input field.
     */
    public static function renderInput($args) {
        $options = get_option(self::OPTION_NAME);
        $value = $options[$args['label_for']] ?? '';
    ?>
        <input type="<?php echo esc_attr($args['type']); ?>" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr(self::OPTION_NAME . '[' . $args['label_for'] . ']'); ?>" value="<?php echo esc_attr($value); ?>">
        <p class="description"><?php echo esc_html($args['description']); ?></p>
<?php
    }
}
