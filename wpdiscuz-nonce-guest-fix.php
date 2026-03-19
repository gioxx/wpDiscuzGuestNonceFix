<?php
/**
 * Plugin Name: wpDiscuz Guest Nonce Fix
 * Plugin URI: https://github.com/gioxx/wpDiscuzGuestNonceFix
 * Description: Applies a temporary workaround for wpDiscuz guest nonce validation issues, with updates delivered via Git Updater.
 * Version: 1.0.1
 * Author: Gioxx
 * Author URI: https://gioxx.org
 * License: MIT
 *
 * GitHub Plugin URI: gioxx/wpDiscuzGuestNonceFix
 * Primary Branch: main
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WPDISCUZ_GUEST_NONCE_FIX_NOTICE_META_KEY')) {
    define('WPDISCUZ_GUEST_NONCE_FIX_NOTICE_META_KEY', 'wpdiscuz_guest_nonce_fix_notice_dismissed');
}

/**
 * Quick toggle.
 * Set this to false to disable the workaround without removing the plugin.
 */
if (!defined('WPDISCUZ_GUEST_NONCE_FIX_ENABLED')) {
    define('WPDISCUZ_GUEST_NONCE_FIX_ENABLED', true);
}

/**
 * Apply the workaround only if enabled.
 */
function wpdiscuz_guest_nonce_fix_bootstrap(): void
{
    if (!WPDISCUZ_GUEST_NONCE_FIX_ENABLED) {
        return;
    }

    add_filter('wpdiscuz_validate_nonce_for_guests', '__return_false');
}
add_action('plugins_loaded', 'wpdiscuz_guest_nonce_fix_bootstrap', 20);

/**
 * Show an admin notice so the workaround remains visible over time.
 */
function wpdiscuz_guest_nonce_fix_admin_notice(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    if ((bool) get_user_meta(get_current_user_id(), WPDISCUZ_GUEST_NONCE_FIX_NOTICE_META_KEY, true)) {
        return;
    }

    if (!WPDISCUZ_GUEST_NONCE_FIX_ENABLED) {
        echo '<div class="notice notice-warning is-dismissible wpdiscuz-guest-nonce-fix-notice"><p><strong>wpDiscuz Guest Nonce Fix:</strong> The workaround plugin is installed but currently disabled.</p></div>';
        return;
    }

    echo '<div class="notice notice-info is-dismissible wpdiscuz-guest-nonce-fix-notice"><p><strong>wpDiscuz Guest Nonce Fix:</strong> The workaround for guest nonce validation is currently active. This should be considered temporary and reviewed after plugin/theme/cache updates.</p></div>';
}
add_action('admin_notices', 'wpdiscuz_guest_nonce_fix_admin_notice');

/**
 * Persist notice dismissal for the current admin user.
 */
function wpdiscuz_guest_nonce_fix_enqueue_admin_assets(string $hook_suffix): void
{
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    wp_enqueue_script('jquery');

    $dismiss_nonce = wp_create_nonce('wpdiscuz_guest_nonce_fix_dismiss_notice');
    $inline_script = <<<JS
jQuery(function ($) {
    $(document).on('click', '.wpdiscuz-guest-nonce-fix-notice .notice-dismiss', function () {
        $.post(ajaxurl, {
            action: 'wpdiscuz_guest_nonce_fix_dismiss_notice',
            nonce: '{$dismiss_nonce}'
        });
    });
});
JS;

    wp_add_inline_script('jquery', $inline_script, 'after');
}
add_action('admin_enqueue_scripts', 'wpdiscuz_guest_nonce_fix_enqueue_admin_assets');

/**
 * AJAX callback to store notice dismissal state.
 */
function wpdiscuz_guest_nonce_fix_dismiss_notice(): void
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Forbidden'], 403);
    }

    check_ajax_referer('wpdiscuz_guest_nonce_fix_dismiss_notice', 'nonce');
    update_user_meta(get_current_user_id(), WPDISCUZ_GUEST_NONCE_FIX_NOTICE_META_KEY, 1);

    wp_send_json_success();
}
add_action('wp_ajax_wpdiscuz_guest_nonce_fix_dismiss_notice', 'wpdiscuz_guest_nonce_fix_dismiss_notice');
