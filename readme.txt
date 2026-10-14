=== eCards ===
Contributors: butterflymedia
Tags: ecard, electronic card, flash card, greeting card, postcard, akismet
License: GPLv3
Requires at least: 4.9
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 5.7.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://buymeacoffee.com/wolffe

== Description ==
eCards is a WordPress plugin used to send electronic cards to friends. It can be implemented in a page, a post, a custom post or the sidebar.

Browse our [WordPress Plugins](https://getbutterfly.com/wordpress-plugins/).

There are two ways you can use this plugin:

1. Add the `[ecard]` shortcode to a post or a page;
2. Call the function from a template file: `<?php if ( function_exists( 'wp_ecard_display_ecards' ) ) { echo wp_ecard_display_ecards(); } ?>;`

== Installation ==

1. Upload the wp-ecards folder to your '/wp-content/plugins/' directory
2. Activate the plugin via the Plugins menu in WordPress
3. Create and publish a new page/post and add this shortcode: `[ecard]`
4. A new eCards menu will appear in WordPress with options, and general help
5. A new eCard Collections menu will appear in WordPress

== Changelog ==

= 5.7.0 =
* SECURITY: User uploads are validated as images and stored through the Media Library (previously any file type could be uploaded to wp-content/uploads)
* SECURITY: Fix CAPTCHA bypass (an expired answer matched an empty one; guests shared one answer)
* SECURITY: Add nonces to all settings forms; escape values in the eCard details meta box; mask the Akismet API key; strip tags from custom CSS
* SECURITY: Sanitise the sender name used in Reply-To, the message HTML, the scheduling date and CSV uploads (checked by extension and size)
* FEATURE: Honeypot field and per-visitor rate limit (10 per hour, `ecard_rate_limit` filter)
* FEATURE: Plain-text alternative part in every email
* FEATURE: Seasonal email themes (Christmas, New Year, birthday, thank you), also per form with `[ecard theme="..."]`
* FEATURE: Shortcode generator with Media Library picker and drag-and-drop ordering
* FEATURE: Delete sent eCard records after a number of days (Email Options)
* FIX: A form rendered twice on one page (excerpts, SEO plugins) could send the same eCard twice
* FIX: Slashes before apostrophes in the email template, subject and labels
* FIX: Scheduled eCards to several recipients

See older changes on https://getbutterfly.com/wordpress-plugins/wordpress-ecards-plugin/
