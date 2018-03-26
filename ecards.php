<?php
/*
Plugin Name: eCards
Plugin URI: https://getbutterfly.com/wordpress-plugins/wordpress-ecards-plugin/
Description: eCards is a plugin used to send electronic cards to friends. It can be implemented in a page, a post, a custom post or the sidebar. eCards makes it quick and easy for you to send an eCard in three steps. Just choose your favorite eCard, add your personal message and send it to any email address. Use preset images, upload your own or select from your Dropbox folder.
Author: Ciprian Popescu
Author URI: https://getbutterfly.com/
Version: 4.5.2
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: ecards

eCards
Copyright (C) 2011-2018 Ciprian Popescu (getbutterfly@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// plugin initialization
function ecards_init() {
	load_plugin_textdomain('ecards', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'ecards_init');

function ecards_setup() {
    add_image_size('ecard', 600, 9999, false);
}
add_action('after_setup_theme', 'ecards_setup');

include plugin_dir_path(__FILE__) . '/includes/functions.php';
include plugin_dir_path(__FILE__) . '/includes/page-options.php';

/*
 * Attach additional images to any post or page straight from Media Library
 */
require_once plugin_dir_path(__FILE__) . '/classes/eCards_Additional_Images.php';

eCards_Additional_Images::getInstance()->init();
//

// Debugging and fixes
$ecard_shortcode_fix = (string) get_option('ecard_shortcode_fix');
$ecard_html_fix = (string) get_option('ecard_html_fix');

if ($ecard_shortcode_fix === 'on') {
    add_action('init', 'ecards_shortcode_fix', 12);
}
if ($ecard_html_fix === 'on') {
    add_filter('wp_mail_content_type', 'ecards_set_content_type');
}
//

function eCardsInstall() {
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $tablename = $wpdb->prefix . 'ecards_stats';
    $sql = "DROP TABLE IF EXISTS `$tablename`;";
    dbDelta($sql);

    // Default options
    add_option('ecard_label_name_own', 'Your name');
    add_option('ecard_label_email_own', 'Your email address');
    add_option('ecard_label_email_friend', 'Your friend email address');
    add_option('ecard_label_message', 'eCard message');
    add_option('ecard_label_send_time', 'Schedule this eCard');
    add_option('ecard_label_cc', 'Send a copy to self');
    add_option('ecard_label_success', 'eCard sent successfully!');
    add_option('ecard_submit', 'Send eCard');

    add_option('ecard_label', 0);
    add_option('ecard_link_anchor', 'Click to see your eCard!');
    add_option('ecard_redirection', 0);
    add_option('ecard_page_thankyou', '');

    add_option('ecard_noreply', '');

    // email settings
    add_option('ecard_title', 'eCard!');
    add_option('ecard_body_toggle', 1);

    // members only settings
    add_option('ecard_restrictions', 0);
    add_option('ecard_restrictions_message', 'This section is restricted to members only!');

    // send all eCards to a universal email address
    add_option('ecard_send_behaviour', 1);
    add_option('ecard_hardcoded_email', '');

    add_option('ecard_send_later', 0);

    // PayPal settings
    add_option('p2v_paypal_sandbox', 0);
    add_option('p2v_who', 0);
    add_option('p2v_paypal_email', '');
    add_option('p2v_paypal_button', 'https://www.paypalobjects.com/webstatic/en_US/i/btn/png/gold-rect-paypalcheckout-34px.png');
    add_option('p2v_paypal_currency', 'EUR');
    add_option('p2v_paypal_default_amount', 5);

    //
    add_option('ecard_image_size', 'thumbnail');
    add_option('ecard_image_size_email', 'medium');
    add_option('ecard_shortcode_fix', 'off');
    add_option('ecard_html_fix', 'off');
    add_option('ecard_allow_cc', 'off');

    //
    add_option('ecard_use_akismet', 'false');
    add_option('ecard_user_create', 0);
    add_option('ecard_post_create_status', 'publish');

    // https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/
    add_option('p2v_ipn', '');
    add_option('p2v_continue', '');
    add_option('p2v_cancel', '');
    add_option('p2v_style', 'paypal'); 
    add_option('p2v_lc', 'US');
    add_option('p2v_shipping', 1);
    add_option('p2v_cbt', '');

    add_option('ecard_use_display', 'masonry');

    //
    add_role('ecards_sender', esc_html__('eCards Sender', 'ecards'), array('read' =>  false, 'edit_posts' => false, 'delete_posts' => false));

    //
    delete_option('ecard_include_content');
    delete_option('ecard_body_additional');
    delete_option('ecard_behaviour');
    delete_option('ecard_use_carousel');
    delete_option('ecard_use_masonry');
    delete_option('ecard_custom_style');
    delete_option('ecard_show_menu_ui');
    delete_option('ecard_counter');
}

register_activation_hook(__FILE__, 'eCardsInstall');



function ecards_load_admin_style() {
    wp_enqueue_style('ecards', plugins_url('css/admin.css', __FILE__), false, eCardsGetVersion());
    wp_enqueue_script('ecards', plugins_url('js/jquery.ecards.js', __FILE__), array(), eCardsGetVersion());
}
add_action('admin_enqueue_scripts', 'ecards_load_admin_style');

function ecards_load_additional() {
    global $pagenow;

    if (is_admin() && strpos($pagenow, 'post') !== false) {
        wp_enqueue_media();

        wp_register_script('twp-attach-post-pages-js', plugins_url('application.js', __FILE__), array('jquery'));
        wp_enqueue_script('twp-attach-post-pages-js');

        wp_localize_script('twp-attach-post-pages-js', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
}
add_action('admin_enqueue_scripts', 'ecards_load_additional');



function ecard_get_attachments($ecid) {
    /*
     * Get all post attachments and exclude featured image
     */
    $output = '';

    $args = array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' => null,
        'post_parent' => $ecid,
        'post_mime_type' => 'image',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'exclude' => get_post_thumbnail_id($ecid),
    );
    $attachments = get_posts($args);

    $ecard_label = (int) get_option('ecard_label');
    $ecard_image_size = get_option('ecard_image_size');
    $ecardSizeEmail = get_option('ecard_image_size_email');

    $ecard_use_display = (string) get_option('ecard_use_display'); // Carousel or Masonry Grid

    $ecard_group_role = 'none';
    if ($ecard_use_display === 'carousel') {
        $ecard_group_role = 'ecard-carousel';
    } else if ($ecard_use_display === 'masonry') {
        $ecard_group_role = 'ecard-masonry';
    }

    if ($attachments) {
        if ($ecard_use_display === 'carousel') {
            $output .= '<div class="ecard_arrows"></div>';
        }

        $output .= '<div role="radiogroup" class="ecard-inner-container ' . $ecard_group_role . '">';
            foreach ($attachments as $a) {
                $alt = get_post_meta($a->ID, '_wp_attachment_image_alt', true);
                if ($alt != 'noselect') {
                    $output .= '<div class="ecard">';
                        $large = wp_get_attachment_image_src($a->ID, $ecardSizeEmail);
                        $thumb = wp_get_attachment_image($a->ID, $ecard_image_size);
                        if ($ecard_label === 0) {
                            $output .= '<a href="' . $large[0] . '" class="ecards">' . $thumb . '</a><br><input type="radio" name="ecard_pick_me" id="ecard' . $a->ID . '" value="' . $a->ID . '" checked><label for="ecard' . $a->ID . '"></label>';
                        } else if ($ecard_label === 1) {
                            $output .= '<label for="ecard' . $a->ID . '">' . $thumb . '<br><input type="radio" name="ecard_pick_me" id="ecard' . $a->ID . '" value="' . $a->ID . '" checked></label>';
                        }
                    $output .= '</div>';
                }
            }
        $output .= '</div>
        <div class="ecards-separator"></div>';
    }

    return $output;
}



// PayPal functions
function p2v_hide($atts, $content = null) {
	extract(shortcode_atts(array(
		'amount' => 5
	), $atts));

    $p2v_who = get_option('p2v_who');

    if ((int) $p2v_who === 0) {
        $p2v_capability = 0;
    } else if ((int) $p2v_who === 1) {
        $p2v_capability = 1;
    }

    if (isset($_GET['ecid']) && (null != $content && ('0' === $p2v_capability || ($_GET['ecid'] + 600) > time()))) {
		return do_shortcode($content);
	} else {
		// get all post attachments
		$output = ecard_get_attachments(get_the_ID());

		$output .= p2v_button($atts);

		return $output;
	}
}

function p2v_button($attributes) {
	if($attributes['amount'] === '' || !is_numeric($attributes['amount']))
		$attributes['amount'] = get_option('p2v_paypal_default_amount');

    $p2v_paypal_sandbox = get_option('p2v_paypal_sandbox');
    if($p2v_paypal_sandbox == 1)
        $button = '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">';
    else
        $button = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
    $button .= '<p>
			<input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="charset" value="utf-8">
            <input type="hidden" name="no_note" value="1">
			<input type="hidden" name="business" value="' . get_option('p2v_paypal_email') . '">
			<input type="hidden" name="currency_code" value="' . get_option('p2v_paypal_currency') . '">
			<input type="hidden" name="return" value="' . $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?ecid=' . time() . '">
			<input type="hidden" name="amount" value="' . $attributes['amount'] . '">
			<input type="hidden" name="item_name" value="' . get_the_title() . '">
			<input type="hidden" name="item_number" value="' . get_the_ID() . '">

			<input type="hidden" name="notify_url" value="' . get_option('p2v_ipn') . '">
			<input type="hidden" name="shopping_url" value="' . get_option('p2v_continue') . '">
			<input type="hidden" name="cancel_return" value="' . get_option('p2v_cancel') . '">
			<input type="hidden" name="page_style" value="' . get_option('p2v_style') . '">
			<input type="hidden" name="lc" value="' . get_option('p2v_lc') . '">
			<input type="hidden" name="no_shipping" value="' . get_option('p2v_shipping') . '">
			<input type="hidden" name="cbt" value="' . get_option('p2v_cbt') . '">

			<input type="image" src="' . get_option('p2v_paypal_button') . '" border="0" name="submit" alt="PayPal - The safer, easier way to pay online">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</p>
	</form>';

	return $button;
}

add_shortcode('paypal', 'p2v_hide');
// end PayPal

function display_ecardMe() {
    ecards_impression(get_the_ID());

    $ecard_submit = get_option('ecard_submit');

    $ecard_send_behaviour = get_option('ecard_send_behaviour');
    $ecard_link_anchor = get_option('ecard_link_anchor');

    $ecard_redirection = get_option('ecard_redirection');
    $ecard_page_thankyou = get_option('ecard_page_thankyou');

    // email settings
    $ecard_title = get_option('ecard_title');

    // eCard Designer
    $ecard_template = wpautop(get_option('ecard_template'));
    //

    $ecard_body_toggle = get_option('ecard_body_toggle');
    $ecardSizeEmail = get_option('ecard_image_size_email');

    // send eCard routine
    // since eCards 2.2.0
    if (isset($_POST['ecard_send'])) {
        // begin user attachment (if any)
        $no_attachments = 1;
        if (!empty($_FILES['file']['name'])) {
            $no_attachments = 0;
            move_uploaded_file($_FILES['file']['tmp_name'], WP_CONTENT_DIR . '/uploads/' . basename($_FILES['file']['name']));

            // attach user uploaded image to eCard custom post
            $filetype = wp_check_filetype(basename($_FILES['file']['name']), null);
            $attachment = array(
                'post_mime_type' => $filetype['type'],
                'post_title' => $_FILES['file']['name'],
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, WP_CONTENT_DIR . '/uploads/' . $_FILES['file']['name']);
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attach_data = wp_generate_attachment_metadata($attach_id, WP_CONTENT_DIR . '/uploads/' . $_FILES['file']['name']);
            wp_update_attachment_metadata($attach_id, $attach_data);
        }
        // end user attachment

        // gallery (attachments) mode
        $ecard_pick_me = '';
        if (isset($_POST['ecard_pick_me']) && $no_attachments === 1) {
            $ecard_pick_me = sanitize_text_field($_POST['ecard_pick_me']);
            $large = wp_get_attachment_image_src($ecard_pick_me, $ecardSizeEmail);
            $ecard_pick_me = '<img src="' . $large[0] . '" alt="" style="max-width: 100%;">';
        }
        //

        if ($ecard_send_behaviour === '1')
            $ecard_to = sanitize_email($_POST['ecard_to']);
        if ($ecard_send_behaviour === '0')
            $ecard_to = sanitize_email(get_option('ecard_hardcoded_email'));

        // check if <Mail From> fields are filled in
        $ecard_from = sanitize_text_field($_POST['ecard_from']);
        $ecard_email_from = sanitize_email($_POST['ecard_email_from']);
        $ecard_email_message = wpautop(stripslashes($_POST['ecard_message']));

        $ecard_referer = esc_url($_POST['ecard_referer']);
        if (isset($_POST['ecard_pick_me'])) {
            $ecardAttachment = wp_get_attachment_link(sanitize_text_field($_POST['ecard_pick_me']), $ecardSizeEmail, true, false, $ecard_link_anchor);
        } else {
            $ecardAttachment = '';
        }

        // Dropbox image
        if (!empty($_POST['selected-file'])) {
            $ecard_image = '<a href="' . esc_url($_POST['selected-file']) . '">' . esc_url($_POST['selected-file']) . '</a>';
        } else {
            // If there's no selected eCard (only user uploaded one)
            if (!empty($ecard_pick_me)) {
                $ecard_image = $ecard_pick_me;
            } else {
                $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $ecardSizeEmail);
                $ecard_image = '<img src="' . $image[0] . '" alt="" style="max-width: 100%;">';
            }
        }

        $ecard_content = sanitize_text_field($_POST['ecard_include_content']);

        // eCard Designer
        $ecard_template = str_replace('[name]', $ecard_from, $ecard_template);
        $ecard_template = str_replace('[email]', $ecard_email_from, $ecard_template);
        $ecard_template = str_replace('[image]', $ecard_image, $ecard_template);
        $ecard_template = str_replace('[ecard-message]', $ecard_email_message, $ecard_template);
        $ecard_template = str_replace('[ecard-link]', $ecardAttachment, $ecard_template);
        $ecard_template = str_replace('[ecard-content]', $ecard_content, $ecard_template);
        $ecard_template = str_replace('[ecard-referrer]', $ecard_referer, $ecard_template);
        //

        $subject = sanitize_text_field($ecard_title);
        $subject = str_replace('[name]', $ecard_from, $subject);
        $subject = str_replace('[email]', $ecard_email_from, $subject);

        $headers[] = "Content-Type: text/html;";

		// Akismet
		$content['comment_author'] = $ecard_from;
		$content['comment_author_email'] = $ecard_email_from;
		$content['comment_author_url'] = home_url();
		$content['comment_content'] = $ecard_email_message;

		if (ecard_checkSpam($content)) {
			echo '<p><strong>' . esc_html__('Akismet prevented sending of this eCard and marked it as spam!', 'ecards') . '</strong></p>';
		} else {
            // Add new user with "eCards Sender" role
            if ((int) get_option('ecard_user_create') === 1) {
                $user_id = username_exists($ecard_email_from);
                if (!$user_id and email_exists($ecard_email_from) == false) {
                    $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                    $user_id = wp_create_user($ecard_email_from, $random_password, $ecard_email_from);

                    $user = new WP_User($user_id);
                    $user->set_role('ecards_sender');
                } else {
                    $random_password = esc_html__('User already exists. Password inherited.', 'ecards');
                }
            }

            /**
             * Create eCard object (custom post type)
             */
            if (isset($_POST['ecard_send_time_enable']) && $_POST['ecard_send_time_enable'] == 1) {
                $ecard_send_time = strtotime($_POST['ecard_send_time']);
                $ecard_send_time = date('Y-m-d H:i:s', $ecard_send_time);

                $ecard_post = array(
                    'post_title'    => esc_html__('eCard', 'ecards') . ' (' . date('Y/m/d H:i:s') . ')',
                    'post_content'  => $ecard_template,
                    'post_status'   => 'future',
                    'post_type'     => 'ecard',
                    'post_author'   => 1,
                    'post_date'     => $ecard_send_time,
                );
            } else {
                $ecard_post_create_status = get_option('ecard_post_create_status');
                $ecard_post = array(
                    'post_title'    => esc_html__('eCard', 'ecards') . ' (' . date('Y/m/d H:i:s') . ')',
                    'post_content'  => $ecard_template,
                    'post_status'   => $ecard_post_create_status,
                    'post_type'     => 'ecard',
                    'post_author'   => 1,
                );
            }

            // Insert the eCard into the database
            $ecard_id = wp_insert_post($ecard_post);
    
            if (isset($_POST['ecard_pick_me'])) {
                // Add featured image to post
                $eCardPicked = (int) $_POST['ecard_pick_me'];

                set_post_thumbnail($ecard_id, $eCardPicked);
            }
            if (!empty($_FILES['file']['name'])) {
                set_post_thumbnail($ecard_id, $attach_id);
            }

            add_post_meta($ecard_id, 'ecard_name', $ecard_from, true);
            add_post_meta($ecard_id, 'ecard_email_sender', $ecard_email_from, true);
            add_post_meta($ecard_id, 'ecard_email_recipient', $ecard_to, true);
            if (isset($_POST['ecard_allow_cc'])) {
                add_post_meta($ecard_id, 'ecard_email_cc', $_POST['ecard_allow_cc'], true);
            }

            $ecard_content_converted = str_replace('[name]', $ecard_from, $_POST['ecard_include_content']);
            $ecard_content_converted = str_replace('[email]', $ecard_email_from, $ecard_content_converted);

            add_post_meta($ecard_id, 'ecard_content', sanitize_text_field($ecard_content_converted), true);

            if (!isset($_POST['ecard_send_time_enable'])) {
                // mail sending
                wp_mail($ecard_to, $subject, $ecard_template, $headers);

                if (isset($_POST['ecard_allow_cc'])) {
                    wp_mail($ecard_email_from, $subject, $ecard_template, $headers);
                }
            }


            // redirection
            if ($ecard_redirection === '1' && $ecard_page_thankyou != '') {
                ecards_conversion(get_the_ID());
                echo '<meta http-equiv="refresh" content="0;url=' . esc_url($ecard_page_thankyou) . '">';
                exit;
            }

            $ecard_label_success = get_option('ecard_label_success');
            echo '<p class="ecard-confirmation"><strong>' . esc_html($ecard_label_success) . '</strong></p>';
            ecards_conversion(get_the_ID());
        }
    }

    /*
     * Display eCard grid
     */
	$output = '<div class="ecard-container">';
		$output .= '<form action="#" method="post" enctype="multipart/form-data" id="eCardForm">';

            $output .= ecard_get_attachments(get_the_ID());

            if (get_option('ecard_dropbox_enable') === '1') {
                $output .= '<script src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="' . get_option('ecard_dropbox_private') . '"></script>
                <p id="droptarget"></p>
                <script>
                options = {
                    success: function(files) {
                        document.getElementById("selected-file").value = files[0].link;
                    },
                    extensions: ["images"]
                };
                var button = Dropbox.createChooseButton(options); document.getElementById("droptarget").appendChild(button);
                </script>
                <input type="hidden" id="selected-file" name="selected-file">';
            }

            if (get_option('ecard_user_enable') === '1') {
                $output .= '<p><input type="file" name="file" id="file"></p>';
            }

            $output .= '<p>
                <label for="ecard_from">' . get_option('ecard_label_name_own') . '</label><br>
                <input type="text" id="ecard_from" name="ecard_from" size="30" required>
            </p>
            <p>
                <label for="ecard_email_from">' . get_option('ecard_label_email_own') . '</label><br>
                <input type="email" id="ecard_email_from" name="ecard_email_from" size="30" required>
            </p>';

            if ($ecard_send_behaviour === '1') {
                $output .= '<p>
                    <label for="ecard_to">' . get_option('ecard_label_email_friend') . '</label><br>
                    <input type="email" id="ecard_to" name="ecard_to" size="30" required>
                </p>';
            }

            $ecard_send_later = get_option('ecard_send_later');
            if ((int) $ecard_send_later === 1) {
                $output .= '<p>
                    <input type="checkbox" name="ecard_send_time_enable" id="ecard_send_time_enable" value="1"> <label for="ecard_send_time_enable">' . get_option('ecard_label_send_time') . '</label> <input type="text" name="ecard_send_time" id="ecard_send_time" value="' . date('Y/m/d H:i') . '"> <a href="#" class="ecard-icon-calendar" onclick="NewCssCal(\'ecard_send_time\', \'yyyyMMdd\', \'arrow\', true, 24, false, \'future\'); return false;">&#128197;</a>
                </p>';
            }

            if ((int) $ecard_body_toggle === 1) {
                $output .= '<p><label for="ecard_message">' . get_option('ecard_label_message') . '</label><br><textarea id="ecard_message" name="ecard_message" rows="6" cols="60"></textarea></p>';
            } else if ((int) $ecard_body_toggle === 0) {
                $output .= '<input type="hidden" name="ecard_message">';
            }

            $output .= '<input type="hidden" name="ecard_include_content" value="' . strip_tags(strip_shortcodes(get_the_content())) . '">';

            if (get_option('ecard_allow_cc') === 'on') {
                $output .= '<p><input type="checkbox" name="ecard_allow_cc" id="ecard_allow_cc"> <label for="ecard_allow_cc">' . get_option('ecard_label_cc') . '</label></p>';
            }

            $output .= '<p>
                <input type="hidden" name="ecard_referer" value="' . get_permalink() . '">
                <input type="submit" id="ecard_send" name="ecard_send" value="' . $ecard_submit . '">
            </p>
        </form>
    </div>';

    if ((int) get_option('ecard_restrictions') === 0 || (int) get_option('ecard_restrictions') === 1 && is_user_logged_in()) {
        return $output;
    } else if ((int) get_option('ecard_restrictions') === 1 && !is_user_logged_in()) {
        $output = get_option('ecard_restrictions_message');
    }

    return $output;
}

add_shortcode('ecard', 'display_ecardMe');

add_action('wp_enqueue_scripts', 'ecard_enqueue_scripts');
function ecard_enqueue_scripts() {
    wp_enqueue_style('ecards', plugins_url('css/vintage.css', __FILE__));

    if ((string) get_option('ecard_use_display') === 'carousel') {
        wp_enqueue_style('flickity', 'https://unpkg.com/flickity@2/dist/flickity.min.css');
        wp_enqueue_script('flickity', 'https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js', array('jquery'), '2.1.0', true);
        wp_enqueue_script('ecards-functions', plugins_url('js/jquery.functions.js', __FILE__), array('jquery', 'flickity'), '4.4.7', true);
    }
    if ((string) get_option('ecard_use_display') === 'masonry') {
        wp_enqueue_style('masonry');
        wp_enqueue_script('ecards-functions', plugins_url('js/jquery.functions.js', __FILE__), array('jquery', 'masonry'), '4.4.7', true);
    }
}

// Displays options menu
function ecard_add_option_page() {
    add_submenu_page('edit.php?post_type=ecard', esc_html__('eCards Settings', 'ecards'), esc_html__('eCards Settings', 'ecards'), 'manage_options', 'ecard_options_page', 'ecard_options_page');
}

add_action('admin_menu', 'ecard_add_option_page');

// custom settings link inside Plugins section
function ecards_settings_link($links) { 
	$settings_link = '<a href="options-general.php?page=ecards">' . esc_html__('Settings', 'ecards') . '</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'ecards_settings_link');
