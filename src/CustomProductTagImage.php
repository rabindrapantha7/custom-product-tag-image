<?php

namespace CustomProductTagImage;

class CustomProductTagImage {
    public function init() {
        // Initialize settings page
        add_action('admin_menu', [SettingsPage::class, 'registerSettingsPage']);
        add_action('admin_init', [SettingsPage::class, 'registerSettings']);

        // Initialize admin features
        add_action('admin_init', [TagManager::class, 'initAdminFeatures']);

        // Register the shortcode
        ShortcodeHandler::registerShortcode();
    }
}
