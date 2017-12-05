<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Register Custom Post Type
function ecard_cpt() {
    $ecard_show_menu_ui = get_option('ecard_show_menu_ui');
    if (empty($ecard_show_menu_ui) || $ecard_show_menu_ui === 'off') {
        $show_in_menu = 'false';
    }
    if ($ecard_show_menu_ui === 'on') {
        $show_in_menu = true;
    }

    $labels = array(
		'name'                => _x( 'eCards', 'Post Type General Name', 'ecards' ),
		'singular_name'       => _x( 'eCard', 'Post Type Singular Name', 'ecards' ),
		'menu_name'           => esc_html__( 'eCards', 'ecards' ),
		'name_admin_bar'      => esc_html__( 'eCard', 'ecards' ),
		'parent_item_colon'   => esc_html__( 'Parent eCard:', 'ecards' ),
		'all_items'           => esc_html__( 'All eCards', 'ecards' ),
		'add_new_item'        => esc_html__( 'Add New eCard', 'ecards' ),
		'add_new'             => esc_html__( 'Add New', 'ecards' ),
		'new_item'            => esc_html__( 'New eCard', 'ecards' ),
		'edit_item'           => esc_html__( 'Edit eCard', 'ecards' ),
		'update_item'         => esc_html__( 'Update eCard', 'ecards' ),
		'view_item'           => esc_html__( 'View eCard', 'ecards' ),
		'search_items'        => esc_html__( 'Search eCard', 'ecards' ),
		'not_found'           => esc_html__( 'Not found', 'ecards' ),
		'not_found_in_trash'  => esc_html__( 'Not found in trash', 'ecards' ),
	);
	$args = array(
		'label'               => esc_html__( 'eCard', 'ecards' ),
		'description'         => esc_html__( 'eCard', 'ecards' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => $show_in_menu,
		'show_in_menu'        => $show_in_menu,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-format-gallery',
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,		
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'rewrite'             => false,
		'capability_type'     => 'post',
	);
	register_post_type( 'ecard', $args );

}
add_action( 'init', 'ecard_cpt', 0 );



function ecards_save() {
    global $wpdb;

    $ecards_stats_table = $wpdb->prefix . 'ecards_stats';
    $ecards_stats_now = date('Y-m-d');
    $cards_sent = 0;

    $wpdb->query("INSERT INTO $ecards_stats_table (date, sent) VALUES ('$ecards_stats_now', $cards_sent) ON DUPLICATE KEY UPDATE sent = sent + 1");

	$ecard_counter = get_option('ecard_counter');
    update_option('ecard_counter', ($ecard_counter + 1));
}

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

    ecards_save();
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
