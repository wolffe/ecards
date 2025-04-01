=== eCards ===
Contributors: butterflymedia
Tags: ecard, electronic card, flash card, greeting card, postcard, akismet
License: GPLv3
Requires at least: 4.9
Tested up to: 6.7.2
Requires PHP: 7.0
Stable tag: 5.6.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

== Description ==
eCards is a WordPress plugin used to send electronic cards to friends. It can be implemented in a page, a post, a custom post or the sidebar.

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

= 5.6.0 =
* Add Akar icons locally, as unpkg is too slow
* Add CSV email address import
* Add basic statistics
* Update date functions to us gmdate()
* Change eCard form to use CSS Grid columns
* Allow administrators to delete scheduled eCards

= 5.5.1 =
* Add basic CAPTCHA feature
* Update contextual help and help strings
* Remove all traces of the Dropbox deprecated feature

= 5.5.0 =
* Fix issue with undefined/empty gallery object
* Add plugin updater

= 5.4.5 =
* Add wp_editor() functionality to the template editor
* Add ARIA accessibility labels
* Enhance eCard form security by adding nonces
* Improve Akismet spam detection
* Make sure eCards and eCard collections are invisible to Google, SEO plugins and public queries
* Remove reusable block/pattern functionality
* Remove "From" email header, as this should be controlled by an SMTP plugin
* Remove deprecated Dropbox integration

= 5.4.4 =
* FIX: Fix undefined variable
* UPDATE: Update WordPress compatibility

= 5.4.3 =
* UPDATE: Update WordPress compatibility

= 5.4.2 =
* FIX: Fix the Settings link on the Plugins page
* FEATURE: Add new Designer tag to show a direct link to the eCard

= 5.4.1 =
* FIX: Removed unused option, saving one database call
* FIX: Removed noselect ALT option, as it was not working properly
* UPDATE: Updated wording on the Email Designer tab
* UPDATE: Updated wording on the Settings tab
* UPDATE: Updated wording on the Email Options tab

= 5.4.0 =
* FEATURE: Added a better grid system
* FEATURE: Added design options (border radius, shadow, colour scheme, accent colour, etc.)
* FEATURE: Added a custom CSS section
* FIX: Improve loading speed for lots of eCard images
* UPDATE: Updated WordPress compatibility
* UPDATE: Updated WordPress requirements
* UPDATE: Updated "Reusable Blocks" to "Patterns" to match the WordPress 6.4+ terminology

= 5.3.2 =
* UPDATE: Removed all wp_editor() calls, as WordPress 6.3 is planning on removing them
* UPDATE: Updated WordPress compatibility

= 5.3.1 =
* UPDATE: Added native block columns instead of custom Flex CSS styles
* UPDATE: Removed option to exclude certain attached images by adding "noselect" as ALT text (too hacky and not SEO-friendly)
* UPDATE: Removed CSS stylesheet to save one HTTP request
* PERFORMANCE: Inlined Critical CSS for overall performance

= 5.3.0 =
* FIX: Fixed collections (gallery block parsing) for WordPress 5.9+
* UPDATE: Updated WordPress compatibility
* UPDATE: Updated WordPress requirements
* UPDATE: Updated copyright year
* UPDATE: Moved the **Reusable Block** option to the **Designer** tab
* UPDATE: Updated all code to adhere to WordPress Coding Standards
* PERFORMANCE: Removed the obsolete statistics (impressions and conversions)
* PERFORMANCE: Minified and inlined critical JavaScript

= 5.2.2 =
* FIX: Fixed wrong label option value
* UPDATE: Updated PHP compatibility

= 5.2.1 =
* UPDATE: Updated WordPress compatibility
* FEATURE: Implemented GDPR/privacy policy consent box

= 5.2.0 =
* FIX: Fixed block parsing undefined variables/indexes
* FIX: Fixed function naming according to WordPress Coding Standards
* FIX: Fixed select field height
* FEATURE: Implemented columns
* FEATURE: Changed eCard image to a block editor figure/image block

= 5.1.2 =
* FEATURE: Added eCard "Reply-To" header for author/sender reply

= 5.1.1 =
* UPDATE: Updated WordPress compatibility
* UPDATE: Updated WordPress requirements

= 5.1 =
* FEATURE: Added eCard collections (gallery blocks)
* UPDATE: Updated eCard custom post type with the latest block editor labels
* UPDATE: Removed all old 4.x options
* UPDATE: Removed admin.css (improve back-end performance and reduce dependencies)
* UPDATE: Removed img/* (improve back-end performance, reduce dependencies and reduce overall plugin size)
* UI: Fixed/improved Flex columns
* FIX: Fixed eCard order when using IDs or collections

= 5.0.2 =
* FEATURE: Added ID parameter to allow custom images from Media Library

= 5.0.1 =
* UPDATE: Updated WordPress compatibility
* UPDATE: Remove attach-in-post feature (DRY, do not repeat functionality, use the native WordPress one)
* MAINTENANCE: Removed unused JS files for faster back-end loading
* MAINTENANCE: Remove all `_ecards_additional_images` meta keys from all posts and pages
* MAINTENANCE: Remove old README and LICENSE files

= 5.0 =
* FEATURE: Added a reusable block for the eCard email template
* UPDATE: Updated PHP requirements (7+)
* UPDATE: Updated WordPress compatibility
* UPDATE: (BREAKING) Removed PayPal functionality
* UPDATE: Updated plugin suggestions
* FIX: Removed unused JS file
* FIX: Added clean up routine (note that starting with 5.1, all old 4.x options will be removed)
* FIX: Fixed support/documentation links
* FIX: Fixed Dropbox button colour

= 4.8.1 =
* FIX: Fix incorrect function

= 4.8.0 =
* UPDATE: Added full Gutenberg compatibility
* UPDATE: Updated WordPress compatibility
* UPDATE: Refactored attached images
* UPDATE: Replaced deprecated grid with Flexbox

= 4.7.1 =
* FIX: Fixed Flexbox CSS Masonry

= 4.7.0 =
* PERFORMANCE: Replaced Masonry JS with Flexbox CSS
* PERFORMANCE: Removed jQuery dependency
* USABILITY: Replaced clunky JS calendar with HTML dropdowns
* UPDATE: Updated WordPress compatibility

= 4.6.0 =
* PRIVACY: All eCard (CPT) are no private and invisible to XML sitemaps
* PRIVACY: Removed all eCard logs
* PRIVACY: Removed all eCard senders
* PRIVACY: Removed custom eCard user roles
* PRIVACY: Remove all email addresses after a scheduled eCard is sent
* UPDATE: Updated WordPress compatibility

= 4.5.4 =
* FIX: Fix for user uploaded image
* UPDATE: Updated WordPress compatibility

= 4.5.3 =
* FIX: Fix for wp_mail() filter
* FIX: Removed unused JS variables

= 4.5.2 =
* UPDATE: Replaced date/time picker with vanilla JavaScript (+speed, -weight)
* UPDATE: Removed jQuery dependency from main eCards script (+speed)
* FEATURE: Added email log

= 4.5.1 =
* UPDATE: Removed getButterfly logo
* UPDATE: Removed wording from getButterfly ad box
* UPDATE: Tweaked plugin description

= 4.5.0 =
* FIX: Fixed CSS compatibility with IE
* FIX: Admin UI tweaks
* UPDATE: Added basic template for eCards grid
* UPDATE: Show eCards in WordPress Dashboard by default
* UPDATE: Removed new eCard CPT capability (eCards are read only)
* UPDATE: Removed SparklinesJS
* UPDATE: Removed old, generic stats
* UPDATE: Removed [ecard_counter] shortcode
* UPDATE: Removed jQuery dependency
* UPDATE: Removed WP Mail From II recommendation
* UPDATE: Updated WordPress requirements
* UPDATE: Updated PHP requirements
* UPDATE: Removed unused table and options
* FEATURE: Added basic stats

= 4.4.6 =
* FIX: Fixed wp_mail() headers compatibility with PHP 7+

= 4.4.5 =
* FIX: Consolidated wp_mail() headers
* FIX: Consolidated payment shortcode
* FIX: Fixed undefined variable
* FIX: Styling tweaks
* UPDATE: Removed eCards UI references
* UPDATE: Code quality updates

= 4.4.4 =
* FIX: Consolidated wp_mail() headers
* UPDATE: Updated WordPress compatibility

= 4.4.3 =
* FIX: Fixed SQL bug

= 4.4.2 =
* UPDATE: Updated documentation
* UPDATE: Relaunched eCards UI
* UPDATE: Code quality updates
* UPDATE: Code performance updates
* UPDATE: Code security updates
* UI: Added better contextual help for additional images

= 4.4.0 =
* FIX: Fixed incorrect email message being passed to Akismet
* FIX: Removed unsupported mail attachment argument
* UPDATE: Consolidated appearance settings to avoid overriding options
* UPDATE: Code performance updates
* UPDATE: Updated WordPress compatibility
* FEATURE: Added possibility to attach images from Media Library to posts/pages
* DOCUMENTATION: Added eCard samples (XML import)

= 4.3.0 =
* FIX: Fixed missing eCard (Designer) template (added WordPress notification)
* FIX: Fixed date/time picker
* FIX: Fixed Masonry loading behaviour

= 4.2.0 =
* FIX: Properly added mail headers to allow for secure sending and reply-to options
* FIX: Fixed carousel to work on more themes natively
* FIX: Fixed some issues with scheduled eCards
* UPDATE: Added ID to eCard form to allow tracking targeting
* FEATURE: Merged all email options into the new eCard Designer
* FEATURE: Added Masonry grid to eCards

= 4.1.0 =
* FIX: Fixed large image using the full size (instead of a custom one)
* UPDATE: Added optional carousel behaviour for eCards
* UPDATE: Moved Sparklines plugin to a local folder (instead of CDNJS)
* UPDATE: Refactored internal plugin URL and removed the constant
* UPDATE: Removed path and version constants and replaced them with native functions
* UPDATE: Removed forced hiding of CPT UI
* PERFORMANCE: Only load admin scripts and styles on the plugin page
* PERFORMANCE: Saved multiple database calls for multiple eCards collections

= 4.0.5 =
* FIX: Fixed large image size being hardcoded to thumbnail
* FIX: Code formatting fixes for PSR compliance
* UPDATE: Updated date/time picker library and used the full version

= 4.0.4 =
* UPDATE: Forced hiding of CPT UI to avoid confusion
* UPDATE: Grouped several developer-only settings

= 4.0.3 =
* FIX: Fixed checkboxes not being unchecked

= 4.0.2 =
* UPDATE: Added success message to labels list

= 4.0.1 =
* FIX: Fixed several PHP notices for unset checkboxes
* UPDATE: All emails now include the selected eCard as an image attachment

= 4.0.0 =
* FIX: Removed deprecated phrase from the Diagnostics tab
* FIX: Added HTML content type fix and grouped debugging options together
* FIX: Fixed formatting and line breaks for eCard content
* FIX: Fixed several issues with non-initialized options
* UPDATE: Synchronized with the free version

= 3.7.1 =
* UPDATE: Merged attachments grid with the PayPal one for consistency
* UPDATE: Updated thumbnail regeneration plugin recommendation
* UPDATE: Removed deprecated PayPal buttons link
* UPDATE: Updated PayPal button (more styles available at https://developer.paypal.com/docs/classic/api/buttons/)
* UI: Added labels for form fields and moved them above

= 3.7.0 =
* FIX: Fixed "Missing Attachment" string (again)
* UPDATE: Localisation improvements
* UPDATE: Updated option loading and changed several defaults
* UPDATE: Updated license details
* UPDATE: Updated security settings
* FEATURE: Added onboarding feature

= 3.6.4 =
* FIX: Do not send empty attachment string

= 3.6.3 =
* UPDATE: Removed several outdated external links
* UPDATE: Added link to plugin documentation
* FEATURE: Added shortcodes to email body

= 3.6.2 =
* UPDATE: Updated WordPress compatibility

= 3.6.1 =
* FIX: Fixed subject line escaping quotes

= 3.6.0 =
* FIX: Fixed image being sent even if user uploaded one

= 3.5.4 =
* FIX: Fixed image size not being applied to the <a> element
* FEATURE: Added original image size as an eCard size option
* FEATURE: Added shortcodes to email subject
* UPDATE: Updated wording for eCards UI theme

= 3.5.3 =
* FIX: Fixed individual image not being sent
* FIX: Fixed additional text not being formatted properly
* UPDATE: Checked latest WordPress compatibility
* UPDATE: Added timezone help for post scheduling
* UPDATE: Hidden eCard post type by default
* UPDATE: Changed default email behaviour to hide link to eCard

= 3.5.2 =
* FEATURE: Added option to mark eCard posts as private or published
* UPDATE: Added utf8mb4 database table conversion
* UPDATE: Added Dutch (nl_NL) translation
* FIX: Fixed inline image inclusion
* FIX: Added option autoloading for better performance
* FIX: Fixed a "headers already sent" error
* FIX: Removed EOLs for PHP files in order to fix some rare server behaviour

= 3.5.1 =
* COMPATIBILITY: Updated CSS styles for compatibility with older browsers
* FIX: Fixed quotes being escaped
* FIX: Fixed a rare "headers already sent" error
* UPDATE: Minified the date/time picker script

= 3.5.0 =
* FIX: Removed all wp_mail() customization, only send email from a dedicated email address
* FIX: Removed unused scripts and styles
* UPDATE: UI improvements and updates
* UPDATE: Performance improvements and styling updates

= 3.3.1 =
* FIX: Fixed yet another PHP 5.3 issue

= 3.3.0 =
* FEATURE: Added eCard scheduling

= 3.2.1 =
* FIX: Fixed several variables to bypass PHP 5.3 "Fatal error: Can't use function return value in write context"
* UPDATE: Updated plugin description
* UPDATE: Added back the chart for sent eCards
* UPDATE: Reorganized the Home/Dashboard section
* UPDATE: Only display the theme ad on the Dashboard section
* UPDATE: Added missing documentation PDF

= 3.2.0 =
* FEATURE: Added dedicated email address
* FEATURE: Added Reply-To header for emails
* FEATURE: Added new style ("Normalize")

= 3.1.0 =
* FIX: Fixed include() syntax
* FIX: Removed unused global variable
* FIX: Renamed main plugin file for WordPress actions compatibility
* IMPROVEMENT: Reduced number of requests by moving function calls to plugins_loaded
* IMPROVEMENT: Removed unused options deletion

= 3.0.3 =
* FIX: Fixed all external URIs
* FIX: Added URL protocol to PayPal return form to avoid issues with some hosts
* ENHANCEMENT: Added several PayPal options for a better user experience (optional)

= 3.0.2 =
* SECURITY: Sanitized all options

= 3.0.1 =
* IMPROVEMENT: Removed radio button if there is only one image

= 3.0.0 =
* FIX: Removed link from the labels tab
* FIX: Added missing update notice
* FIX: Fixed missing image size from PayPal shortcode
* FEATURE: Added PRO/LITE version

= 2.9.6 =
* FIX: Fixed user creation not being triggered
* FEATURE: Added labels directly to plugin settings (instead of PO)
* UPDATE: Removed label from the file upload button

= 2.9.5 =
* UPDATE: Updated return URL to match both HTTP and HTTPS protocols
* FIX: Added default option for user creation
* FIX: Fixed several wp_mail() rules

= 2.9.4 =
* UPDATE: Removed internal mail overrides (use a third-party plugin to accomplish this)
* UPDATE: Use sender's name and email address for sending eCards
* UPDATE: Added dismissible notices

= 2.9.3 =
* PERFORMANCE: Removed sparkline visualizer
* UPDATE: Updated WordPress compatibility
* FIX: Added more translatable strings

= 2.9.2 =
* IMPROVEMENT: Added option to disable Akismet
* IMPROVEMENT: Added option to save eCard senders to the users database
* FIX: Removed email filters for PHP lower than 5.3
* UPDATE: Added several translation strings to .po files

= 2.9.1 =
* FIX: Updated a deprecated Akismet call
* FIX: Removed PHP 5.3+ specific functions for mail filters

= 2.9 =
* IMPROVEMENT: Removed reCAPTCHA and implemented Akismet
* IMPROVEMENT: Cleaned up email sending process
* FIX: Fixed theme link (again)

= 2.8.1 =
* FIX: Fixed theme link

= 2.8.0 =
* FEATURE: Added option to choose between image source and label behaviour (click eCard thumbnail to select radio box)
* IMPROVEMENT: Merged some community changes to reCAPTCHA library

= 2.7.3 =
* FIX: Added PHP check before using filter functions
* FIX: Added UTF8 encoding

= 2.7.2 =
* FIX: Fixed custom PayPal amount
* FIX: Custom reCAPTCHA fixes (more to come from https://github.com/google/ReCAPTCHA)

= 2.7.1 =
* ENHANCEMENT: Updated reCAPTCHA to v2 (php_1.0)
* ENHANCEMENT: Removed featured image from eCards list
* ENHANCEMENT: Removed Flash cards
* ENHANCEMENT: Improved PayPal form
* FIX: Removed singular card display (all images now appear as selectables)
* FIX: Various code fixes and improvements

= 2.7.0 =
* ENHANCEMENT: Added internal wp_mail() filters (removed global ones)
* ENHANCEMENT: Removed support for multiple emails (due to abuse)
* ENHANCEMENT: Removed BCC header, Reply-To header and email trimming

= 2.6.1 =
* FIX: Renamed a function to avoid conflicts
* FIX: Renamed order_by to orderby to avoid conflicts
* FIX: Removed a redundant function - chip_magic_quotes()
* FIX: Defaulted 'ecard_shortcode_fix' ("Apply shortcode fix" option) to 'off'
* FEATURE: Bumped required version to 3.9

= 2.6.0 =
* FEATURE: Added option to allow sender to CC self
* ACCESSIBILITY: Removed custom image size and allowed for listing all registered image sizes (more image sizes can be added by using third-party plugins)
* ACCESSIBILITY: Removed wp_mail() filters from diagnostics tab as they were far from accurate
* FIX: Removed option to deactivate mail filters

= 2.5.0.4 =
* FEATURE: Added Reply-To header
* FEATURE: Added wp_mail() test function and SMTP plugin link
* FEATURE: Added settings link to plugins section
* FIX: Various fixes
* FIX: Moved plugin to Settings menu

= 2.5.0.3 =
* FEATURE: Added decimals to PayPal price section

= 2.5.0.2 =
* FIX: Fixed PayPal return page with no shortcode filter

= 2.5.0.1 =
* FEATURE: Added PayPal sandbox option
* IMPROVEMENT: Added .ecards (or .ecard a) as lightbox selector
* IMPROVEMENT: Renamed CSS name for enqueued styles
* HELP: Added contextual help for lightbox selectors
* HELP: Fixed typo in readme.txt file

= 2.5.0 =
* UPDATE: Added internal mail parameters (with options)
* UPDATE: Changed user upload behaviour
* UPDATE: Updated Dropbox Chooser to version 2
* UPDATE: Updated styles
* IMPROVEMENT: Fixed stylesheet enqueue mode
* IMPROVEMENT: General clean-up

= 2.4.9 =
* SECURITY: Removed Google Charts API and replaced it with a simple sparkline chart
* CHECK: Fixed a conflict on the demo page

= 2.4.8 =
* FIX: Added function check for reCAPTCHA conflict
* IMPROVEMENT: Switched to strict comparison for faster PHP code execution
* UPDATE: Updated compatibility with WordPress 4.0

= 2.4.7 =
* FIX: Reversed order of email elements, due to spam filters

= 2.4.6 =
* FIX: Fixed eCards chart when the plugin is first installed (no statistics)
* FEATURE: eCards can now be reordered from the Gallery screen

= 2.4.5 =
* UPDATE: Changed menu icon to dashicon
* UPDATE: Clarified eCard image size options
* UPDATE: Merged eCard internal counter functions
* UPDATE: Moved statistics chart to plugin's home

= 2.4.4 =
* FIX: Fixed single eCard not showing (revert 2.4.3 input checking fix)
* FIX: Frontend form appearance improvements
* UPDATE: Updated all translation files with correct plural forms
* IMPROVEMENT: Admin UI performance improvements

= 2.4.3 =
* FIX: Removed the "Missing attachment" text if user uploaded own image
* FIX: Removed all checked attributes from eCards
* FIX: Fixed both selected image and attachment being sent
* FIX: Fixed div nesting inside paragraph element
* FIX: Removed unused option both from code and options table
* FIX: Renamed print_filters_for() function for better compatibility with other plugins
* FIX: Fixed diagnostics tab for WordPress multisite
* IMPROVEMENT: Moved eCard message and content below the image (inside email)
* IMPROVEMENT: Added radiogroup role to the list of radio inputs
* IMPROVEMENT: Removed featured image from list of available eCards
* DOCUMENTATION: Added new link for Gmail authentication

= 2.4.2 =
* VERSION: Added WordPress 3.9 compatibility
* FIX: Fixed unclosed paragraphs in dashboard help section 
* IMPROVEMENT: Better formatting for the dashboard help section

= 2.4.1 =
* FEATURE: Added custom eCard thumbnail (also added recommendations)

= 2.4.0 =
* FIX: Removed mysql_query
* FIX: Fixed statistics chart not working on multisite
* IMPROVEMENT: Removed MAIL FROM filters as dedicated plugins do it for free (also added recommendations)
* IMPROVEMENT: wp_mail() headers are now an array instead of a string

= 2.3.2 =
* FIX: Reverted a change from 2.3.1 preventing the payment shortcode

= 2.3.1 =
* FEATURE: Added Flash cards (use [fcard] shortcode)
* IMPROVEMENT: Removed "autofocus" attribute for email redirection (page was already scrolled)

= 2.3.0 =
* FIX: Forced correct appearance for radio input
* IMPROVEMENT: Added optional content shortcode fix
* IMPROVEMENT: Added eCard size inside shortcode (thumbnail, medium, large, full)
* IMPROVEMENT: Removed a redundant option and combined the upload functions

= 2.2.0 =
* FEATURE: Added statistics
* FEATURE: Added diagnostics
* FEATURE: Added payment restrictions
* IMPROVEMENT: Streamlined template options
* IMPROVEMENT: Removed classic CAPTCHA
* IMPROVEMENT: Moved options initialization to a hook
* IMPROVEMENT: Lots of code overhauling and simplification

= 2.1.0 =
* FEATURE: Added theme selector for reCAPTCHA
* IMPROVEMENT: Added multiple emails as BCC
* IMPROVEMENT: Added noreply@domain.ext as sender
* LOCALIZATION: Added Czech (cs_CZ) translation (thanks Radoslav Polasek)

= 2.0.7 =
* FIX: Hardcoded email was not working (due to _POST[] reusal)

= 2.0.6 =
* FEATURE: Added option to include post/page content (thanks Gary)
* FEATURE: Added option to hide all attached images

= 2.0.5 =
* FIX: Fixed an unquoted server constant
* FIX: Fixed display of attached eCards when PayPal payment was activated
* FEATURE: Added option to modify PayPal button image

= 2.0.4 =
* UI: Fixed an administration label
* GENERAL: Fixed user upload option

= 2.0.3 =
* UI: Fixed a (too) long input field
* UI: More integration with MP6 plugin and WordPress 3.6-beta2

= 2.0.2 =
* Added option to exclude certain attached images by adding "noselect" as ALT text
* Fixed German (de_DE) translation

= 2.0.1 =
* Fixed missing labels
* Fixed contextual label help

= 2.0.0 =
* Redesigned plugin administration for better accessibility
* Added more contextual help
* Added missing symbols

= 1.7.0 =
* If no <Mail From> fields are filled in, the eCard will use the original sender (both name and email address)
* Added Danish translation (da_DK)
* Added Finnish translation (fi)
* Added option to toggle (show/hide) the message area ("Tip a friend" type email message)
* Modified name to be closer to the top in the Plugins section

= 1.7.0-beta =
* Added paragraph formatting for email template (wpautop)
* Added email name/address override (plugin Mail From II is no longer necessary)
* Added payment option (PayPal)
* Fixed reCAPTCHA inclusion conflict

= 1.6.8 =
* Added option to show single image (if only one image is attached, the radio button is hidden)
* Fixed subject line misbehaving when using single quotes

= 1.6.7 =
* Removed several links from eCard email
* Added license declaration and license.txt (GPL v3)
* Removed the donation link and improved the readme.txt file
* Tested the plugin with latest 3.5.1-beta1 and 3.6-alpha

= 1.6.6 =
* Allow user to upload image from Dropbox (Chooser)
* More code cleanup

= 1.6.5 =
* Clarified eCard behaviour (show/hide image or link)
* Code cleanup

= 1.6.4 =
* Added German (de_DE) translation (thanks to @Adrian)
* Fixed an English string (thanks to @Janaki)
* Fixed a link anchor being incorrectly displayed
* Removed email tweaks to get rid of spam detection

= 1.6.3 =
* Added small tweaks to eCard form
* Added CSS classes for message styling
* Added improved documentation
* Fixed counter update for "thank you" pages (thanks to @bad_designer)
* Removed unused styles and fonts

= 1.6.2 =
* Added link to attachment page inside email
* Added link to eCard page inside email
* Removed a useless hidden field

= 1.6.1 =
* Added back eCard behaviour (hide eCard, show link, show eCard or hide both)
* Added backlink to single attachment (now working as expected)
* Added Spanish locale (es_ES) (thanks to @render)

= 1.6 =
* Added possibility of adding more attachments to a single post/page
* Fixed a relative file inclusion error

= 1.5.11 =
* Added a magic quotes circumventing function to deal with `stripslashes_deep`
* Added additional headers for wp_mail function - mainly to deal with SPAM
* Added option to hide the recipient email address and send all eCards to a universal email address (thanks to @Sabrina for the suggestion)
* Tested with WordPress 3.5-beta3

= 1.5.10 =
* Added restriction for members only
* Added restriction message
* Fixed a typo
* Small UI changes
* Tested with WordPress 3.5-beta-1

= 1.5.9 =
* Added option to redirect to a "Thank You" page upon success
* Removed some unused code from a previous update

= 1.5.8 =
* Removed custom font for CTA, as it didn't render diacritics
* Removed size style for CTA, as headers should be styled by theme
* Removed width and height restrictions - send multiple dimensions eCards
* Fixed an error with thumb generator script, where a wrong parameter was passed to the function
* Fixed an old typo in the documentation
* Added style selector
* Added two new styles - Metro Light and Metro Dark
* Added charset information next to version line
* Added option to rename the submit button
* Updated all language files (removed one string)

= 1.5.7 =
* Added reCAPTCHA library
* Added French translation
* Added en_GB variant translation

= 1.5.6 =
* Added HTML validation switch for email (send to multiple recipients!)
* Tested and confirmed compatibility with WordPress 3.4

= 1.5.5 =
* Fixed a typo (eCard instead of Ecard)
* Added a new option to hide eCard and display a link
* Added a new shortcode to display number of eCards sent
* Changed plugin behaviour to not require a hardcoded folder name

= 1.5.2 =
* Fixed a warning for the main function (thanks to Zirta)

= 1.5.1 =
* Added editor buttons to email template
* Added sample email template, with tags explanation
* Added HTML5 form input types (when viewed in a browser that does not support them, these input types fall back to text input)
* Added HTML5 (required) attributes (form validation)

= 1.5.0 =
* Fixed the eCard display function
* Fixed 2 typos
* Fixed slashes appearing inside email message body
* Changed CAPTCHA function with a PHP text-based one to avoid path issues
* Changed some backend styles

= 1.4.0 =
* Added optional CAPTCHA
* Fixed a deprecated function
* Fixed a conflict with ABSPATH
* Removed email class filter
* Removed editor button

= 1.3.0 =
* Added mathematical CAPTCHA
* Fixed email template editing
* Updated translations

= 1.2.0 =
* Added translatable strings
* Added email template
* Cleaned up source

= 1.0.0 =
* First public release
