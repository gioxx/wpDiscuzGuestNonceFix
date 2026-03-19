# wpDiscuz Guest Nonce Fix

Temporary workaround plugin for WordPress that disables guest nonce validation in `wpDiscuz` when that check causes comment submission issues for non-logged-in users.

The plugin is intentionally small and focused:

- applies the filter `wpdiscuz_validate_nonce_for_guests` and forces it to `false`
- shows an admin notice to remind you that the workaround is active
- supports updates from GitHub via [Git Updater](https://git-updater.com/)

Repository: <https://github.com/gioxx/wpDiscuzGuestNonceFix>

## Background

This plugin was created as a practical workaround after investigating reports similar to these support threads:

- <https://wpdiscuz.com/community/troubleshooting/issues-with-nonce/>
- <https://wordpress.org/support/topic/error-nonce-is-invalid-when-trying-to-post-comments/>

## What It Does

When enabled, the plugin hooks into WordPress after `plugins_loaded` and applies:

```php
add_filter('wpdiscuz_validate_nonce_for_guests', '__return_false');
```

This is meant as a temporary mitigation while investigating or waiting for a fix in the surrounding stack:

- `wpDiscuz`
- theme integrations
- cache layers
- optimization plugins
- security middleware

## Requirements

- WordPress
- `wpDiscuz` installed and active
- administrator access if you want to see or dismiss the dashboard notice

## Installation

### Option 1: Manual Installation

1. Download the latest release or clone the repository:
   `<https://github.com/gioxx/wpDiscuzGuestNonceFix>`
2. Copy the plugin folder into:
   `wp-content/plugins/wpDiscuzGuestNonceFix`
3. Make sure the main plugin file is:
   `wp-content/plugins/wpDiscuzGuestNonceFix/wpdiscuz-nonce-guest-fix.php`
4. Activate the plugin from `Plugins` in the WordPress admin area.

### Option 2: Install and Update with Git Updater

If you already use the plugin `Git Updater`, you can install this plugin directly from the GitHub repository and keep it updated from there.

1. Install and activate `Git Updater`.
2. Open the WordPress admin page:
   `wp-admin/options-general.php?page=git-updater&tab=git_updater_install_plugin`
3. In the repository field, enter:
   `https://github.com/gioxx/wpDiscuzGuestNonceFix`
4. Start the installation from the Git Updater interface.
5. Once installed, activate the plugin if it is not activated automatically.

The plugin header already includes the metadata required by Git Updater:

- `GitHub Plugin URI: gioxx/wpDiscuzGuestNonceFix`
- `Primary Branch: main`

## Usage

Activate the plugin and test guest comment submission again.

If the workaround is active, an admin notice will appear in the dashboard. The notice is dismissible and the dismissal is stored per user.

If you need to disable the workaround without removing the plugin, set:

```php
define('WPDISCUZ_GUEST_NONCE_FIX_ENABLED', false);
```

before the plugin loads, for example in `wp-config.php` or another must-use bootstrap you control.

## Notes

- This plugin is a workaround, not a root-cause fix.
- If the issue disappears after updates to `wpDiscuz`, your theme, or caching/security layers, you should review whether this plugin is still needed.
- Disabling nonce validation for guests changes the normal validation flow, so it should only stay enabled for as long as necessary.

## License

MIT. See [LICENSE](LICENSE).
