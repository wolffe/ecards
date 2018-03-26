<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Register Custom Post Types
function ecard_cpt() {
	// eCard post type
    $labels = array(
		'name'                => _x('eCards', 'Post Type General Name', 'ecards'),
		'singular_name'       => _x('eCard', 'Post Type Singular Name', 'ecards'),
		'menu_name'           => esc_html__('eCards', 'ecards'),
		'name_admin_bar'      => esc_html__('eCard', 'ecards'),
		'parent_item_colon'   => esc_html__('Parent eCard:', 'ecards'),
		'all_items'           => esc_html__('All eCards', 'ecards'),
		'add_new_item'        => esc_html__('Add New eCard', 'ecards'),
		'add_new'             => esc_html__('Add New', 'ecards'),
		'new_item'            => esc_html__('New eCard', 'ecards'),
		'edit_item'           => esc_html__('Edit eCard', 'ecards'),
		'update_item'         => esc_html__('Update eCard', 'ecards'),
		'view_item'           => esc_html__('View eCard', 'ecards'),
		'search_items'        => esc_html__('Search eCard', 'ecards'),
		'not_found'           => esc_html__('Not found', 'ecards'),
		'not_found_in_trash'  => esc_html__('Not found in trash', 'ecards'),
	);
	$args = array(
		'label'               => esc_html__('eCard', 'ecards'),
		'description'         => esc_html__('eCard', 'ecards'),
		'labels'              => $labels,
		'supports'            => array('title', 'editor', 'author', 'thumbnail', 'custom-fields'),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-email-alt',
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,		
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'capabilities' => array(
			'create_posts' => 'do_not_allow', // Removes support for the "Add New" function
		),
		'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
	);
	register_post_type('ecard', $args);

	// eCard Mail Log post type
    $labels = array(
		'name'                => _x('eCard Logs', 'Post Type General Name', 'ecards'),
		'singular_name'       => _x('eCard Log', 'Post Type Singular Name', 'ecards'),
		'menu_name'           => esc_html__('eCard Logs', 'ecards'),
		'name_admin_bar'      => esc_html__('eCard Log', 'ecards'),
		'parent_item_colon'   => esc_html__('Parent eCard Log:', 'ecards'),
		'all_items'           => esc_html__('All eCard Logs', 'ecards'),
		'add_new_item'        => esc_html__('Add New eCard Log', 'ecards'),
		'add_new'             => esc_html__('Add New', 'ecards'),
		'new_item'            => esc_html__('New eCard Log', 'ecards'),
		'edit_item'           => esc_html__('Edit eCard Log', 'ecards'),
		'update_item'         => esc_html__('Update eCard Log', 'ecards'),
		'view_item'           => esc_html__('View eCard Log', 'ecards'),
		'search_items'        => esc_html__('Search eCard Log', 'ecards'),
		'not_found'           => esc_html__('Not found', 'ecards'),
		'not_found_in_trash'  => esc_html__('Not found in trash', 'ecards'),
	);
	$args = array(
		'label'               => esc_html__('eCard Log', 'ecards'),
		'description'         => esc_html__('eCard Log', 'ecards'),
		'labels'              => $labels,
		'supports'            => array('title', 'editor'),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-email-alt',
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => false,
		'has_archive'         => false,		
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'capabilities' => array(
			'create_posts' => 'do_not_allow', // Removes support for the "Add New" function
		),
		'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
	);
	register_post_type('ecard_log', $args);
}

add_action('init', 'ecard_cpt', 0);



function ecards_return_image_sizes() {
    global $_wp_additional_image_sizes;

    $image_sizes = array();
    foreach (get_intermediate_image_sizes() as $size) {
        $image_sizes[$size] = array(0, 0);
        if (in_array($size, array('thumbnail', 'medium', 'large'))) {
            $image_sizes[$size][0] = get_option($size . '_size_w');
            $image_sizes[$size][1] = get_option($size . '_size_h');
        }
        else 
            if (isset($_wp_additional_image_sizes) && isset($_wp_additional_image_sizes[$size]))
                $image_sizes[$size] = array($_wp_additional_image_sizes[$size]['width'], $_wp_additional_image_sizes[$size]['height']);
    }
    return $image_sizes;
}

function ecards_shortcode_fix() {
    add_filter('the_content', 'do_shortcode', 9);
}

function ecards_set_content_type($content_type) {
    return 'text/html';
}

function ecard_checkSpam($content) {
	// innocent until proven guilty
	$isSpam = FALSE;
	$content = (array)$content;

	if (function_exists('akismet_init') && get_option('ecard_use_akismet') == 'true') {
		$wpcom_api_key = get_option('wordpress_api_key');

		if(!empty($wpcom_api_key)) {
			// set remaining required values for akismet api
			$content['user_ip'] = preg_replace('/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR']);
			$content['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$content['referrer'] = $_SERVER['HTTP_REFERER'];
			$content['blog'] = get_option('home');

			if(empty($content['referrer'])) {
				$content['referrer'] = get_permalink();
			}

			$queryString = '';

			foreach($content as $key => $data) {
				if(!empty($data)) {
					$queryString .= $key . '=' . urlencode(stripslashes($data)) . '&';
				}
			}

			$response = Akismet::http_post($queryString, 'comment-check');

			if($response[1] == 'true') {
				update_option('akismet_spam_count', get_option('akismet_spam_count') + 1);
				$isSpam = TRUE;
			}
		}
	}
	return $isSpam;
}

//add_action('plugins_loaded', 'ecard_send_later');
add_action('publish_ecard', 'ecard_send_later');

function ecard_send_later($ecard_id) {
	$ecard_send_behaviour = get_option('ecard_send_behaviour');

    // send eCard
    if ($ecard_send_behaviour === '1') {
        $ecard_to = get_post_meta($ecard_id, 'ecard_email_recipient', true);
    }
    if ($ecard_send_behaviour === '0') {
        $ecard_to = sanitize_email(get_option('ecard_hardcoded_email'));
    }

	// check if <Mail From> fields are filled in
	$ecard_from = get_post_meta($ecard_id, 'ecard_name', true);
	$ecard_email_from = get_post_meta($ecard_id, 'ecard_email_sender', true);

	// email settings
	$subject = sanitize_text_field(get_option('ecard_title'));
    $subject = str_replace('[name]', $ecard_from, $subject);
    $subject = str_replace('[email]', $ecard_email_from, $subject);

    $ecard_object = get_post($ecard_id);
	$ecard_email_message = apply_filters('the_content', $ecard_object->post_content);

    $ecard_noreply = get_option('ecard_noreply');

    $headers[] = "Content-Type: text/html;";
    $headers[] = "From: $ecard_from <$ecard_noreply>";
    $headers[] = "Reply-To: $ecard_email_from";

    wp_mail($ecard_to, $subject, $ecard_email_message, $headers);

    $ecard_email_cc = get_post_meta($ecard_id, 'ecard_email_cc', true);
    if (!empty($ecard_email_cc))
        wp_mail($ecard_email_from, $subject, $ecard_email_message, $headers);

    ecards_conversion(get_the_ID());
}

function eCardsGetVersion() {
    $eCardsPluginPath = plugin_dir_path(__DIR__);
    $eCardsData = get_plugin_data($eCardsPluginPath . 'ecards.php');

    return (string) $eCardsData['Version'];
}



/*
 * Attach additional images to any post or page straight from Media Library
 */
add_action('wp_ajax_ecards_detach', 'ecards_detach_callback');
add_action('wp_ajax_nopriv_ecards_detach', 'ecards_detach_callback');

function ecards_detach_callback() {
    $my_post = array(
        'ID' => $_POST['whatToDelete'],
        'post_parent' => 0,
    );
    wp_update_post($my_post);

    $selected_images = get_post_meta($_POST['whatPost'], '_ecards_additional_images', true);
    $additionalImages = explode('|', $selected_images);

    foreach (array_keys($additionalImages, $_POST['whatToDelete'], true) as $key) {
        unset($additionalImages[$key]);
    }

    update_post_meta($_POST['whatPost'], '_ecards_additional_images', array_values($additionalImages));

    exit;
}

function ecards_impression($id, $count = true) {
    $impressionCount = get_post_meta($id, '_ecards_impressions', true);

    if ($impressionCount == '') {
        $impressionCount = 0;
    }

    if ($count === true) {
        $impressionCount++;
        update_post_meta($id, '_ecards_impressions', $impressionCount);
    }

    return $impressionCount;
}

function ecards_conversion($id, $count = true) {
    $conversionCount = get_post_meta($id, '_ecards_conversions', true);

    if ($conversionCount == '') {
        $conversionCount = 0;
    }

    if ($count === true) {
        $conversionCount++;
        update_post_meta($id, '_ecards_conversions', $conversionCount);
    }

    return $conversionCount;
}

function ecards_mail_from($mail_from_email) {
	$site_mail_from_email = sanitize_email(get_option('ecard_noreply'));

	if (empty($site_mail_from_email)) {
		return $mail_from_email;
	} else {
		return $site_mail_from_email;
	}
}

add_filter('wp_mail_from', 'ecards_mail_from', 1);

/**
 * Save email to eCards mail log
 */
function ecards_mail_log($args) {
	$to = $args['to'];
	$subject = $args['subject'];
	$message = $args['message'];
	$headers = $args['headers'];

	$ecard_mail_log_template = '<p>New email sent to <strong>' . $to . '</strong>.</p>
	<h3>' . $subject . '</h3>
	' . $message . '
	<p>Date: <code>' . date('Y/m/d H:i:s') . '</code></p>';

    foreach ($headers as $header) {
        if (strpos($header, 'eCards') !== false) {
            // record log

			$ecard_mail_log = array(
				'post_title' => esc_html__('eCard (Mail Log)', 'ecards') . ' (' . date('Y/m/d H:i:s') . ')',
				'post_content' => $ecard_mail_log_template,
				'post_status' => 'private',
				'post_type' => 'ecard_log',
				'post_author' => 1,
				'post_date' => date('Y/m/d H:i:s'),
			);
			$ecard_mail_log_id = wp_insert_post($ecard_mail_log);
		}
	}

	return $args;
}
