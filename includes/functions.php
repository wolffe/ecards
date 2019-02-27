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
    $headers[] = "X-Mailer: WordPress/eCards;";

    wp_mail($ecard_to, $subject, $ecard_email_message, $headers);

    $ecard_email_cc = get_post_meta($ecard_id, 'ecard_email_cc', true);
    if (!empty($ecard_email_cc)) {
        wp_mail($ecard_email_from, $subject, $ecard_email_message, $headers);
    }

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



function ecard_datetime_picker() {
    $day = date('d');
    $month = date('m');
    $startyear = date('Y');
    $endyear = date('Y') + 10;
    $months = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

    $html = '<select name="ecard_send_time_month">';
        for ($i = 1; $i <= 12; $i++) {
            if ((int) $i === (int) $month) {
                $html .= '<option selected value="' . str_pad($i, 2, '0', STR_PAD_LEFT) . '">' . $months[$i] . '</option>';
            } else {
                $html .= '<option value="' . str_pad($i, 2, '0', STR_PAD_LEFT) . '">' . $months[$i] . '</option>';
            }
        }
    $html .= '</select> <select name="ecard_send_time_day">';
        for ($i = 1; $i <= 31; $i++) {
            if ((int) $i === (int) $day) {
                $html .= '<option selected value="' . str_pad($i, 2, '0', STR_PAD_LEFT) . '">' . $i . '</option>';
            } else {
                $html .= '<option value="' . str_pad($i, 2, '0', STR_PAD_LEFT) .'">' . $i . '</option>';
            }
        }
    $html .= '</select> <select name="ecard_send_time_year">';
        for ($i = $startyear; $i <= $endyear; $i++) {
            $html .= '<option value="' . $i . '">' . $i . '</option>';
        }
    $html .= '</select> <select name="ecard_send_time_hour">';
        for ($hours = 0; $hours < 24; $hours++) {
            for ($mins = 0; $mins < 60; $mins += 30) {
                $html .= '<option>' . str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT) . '</option>';
            }
        }
    $html .= '</select>';

    return $html;
}




function getImagesFromIds($ids, $size = 'thumbnail') {
        $images = array();

        foreach ($ids as $id) {
            $meta = wp_get_attachment_image_src($id, $size);

            $info = array();

            $info['id'] = $id;
            $info['url'] = $meta[0];
            $info['width'] = $meta[1];
            $info['height'] = $meta[2];
            $info['is_original'] = !$meta[3];

            $images[] = (object) $info;
        }

        return $images;
    }

/**
 * Register eCards meta box
 */
function ecards_add_meta_box() {
	add_meta_box('ecards_meta_box', __('eCards', 'ecards'), 'ecards_metabox_callback', array('post', 'page'));
	add_meta_box('ecards_details_meta_box', __('eCards Details', 'ecards'), 'ecards_details_metabox_callback', array('ecard'));
}
add_action('add_meta_boxes', 'ecards_add_meta_box');

/**
 * eCards meta box Callback
 */
function ecards_metabox_callback($post) {
    wp_nonce_field('ecards', 'ecards_additional_images_plugin_nonce');

    $images_arr = get_post_meta($post->ID, '_ecards_additional_images', true);
    $images_str = '';
    $images = array();

    if ($images_arr) {
        $images = getImagesFromIds($images_arr);
        $images_str = implode('|', $images_arr);
    }

    $params = array(
        'post_id' => $post->ID,
        'width' => 640,
        'height' => 557,
        'TB_iframe' => 1,
        'type' => 'image',
    );

    $href = admin_url('media-upload.php?' . http_build_query($params));
    ?>
    <p>Use this box to add images from your <strong>Media Library</strong>.</p>

    <?php echo ecard_get_admin_attachments($post->ID); ?>

    <p>
        <a href="<?php echo esc_url($href); ?>" id="twp-attach-post-images-uploader" class="button button-primary">Select image(s)</a>
    </p>
    <p>
        <small>Use <code class="codor">CTRL</code> key to select multiple images, attach them and update/publish your post/page. Note that attaching images may detach them from other posts or pages.</small>
    </p>
    <p>
        <input type="hidden" id="twp-attach-post-images-selected" name="selected_post_image" value="<?php echo esc_html($images_str); ?>">
    </p>

    <script type="text/html" id="twp-attach-post-images-list-item-tpl">
        <li><img src="{src}" alt=""><a href="javascript:void(0)" class="delete" data-id="{id}"><span class="dashicons dashicons-trash"></span></a></li>
    </script>

	<?php
}

/**
 * Save eCards meta box
 */
function ecards_save_postdata($post_id) {
    if (!isset($_POST['ecards_additional_images_plugin_nonce'])) {
        return $post_id;
    }

    $nonce = $_POST['ecards_additional_images_plugin_nonce'];

    if (!wp_verify_nonce($nonce, 'ecards')) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    $selected_images = sanitize_text_field($_POST['selected_post_image']);
    $image_ids = explode('|', $selected_images);
    foreach ($image_ids as $i => $id) {
        if ($id > 0) {
            $my_post = array(
                'ID' => $id,
                'post_parent' => $post_id,
            );
            wp_update_post($my_post);
        }
    }

    update_post_meta($post_id, '_ecards_additional_images', array_values($image_ids));
}
add_action('save_post', 'ecards_save_postdata');

function ecards_details_metabox_callback($post) {
    $ecardContent = get_post_meta($post->ID, 'ecard_content', true);
    $ecardEmailRecipient = get_post_meta($post->ID, 'ecard_email_recipient', true);
    $ecardEmailSender = get_post_meta($post->ID, 'ecard_email_sender', true);
    $ecardName = get_post_meta($post->ID, 'ecard_name', true);
    ?>

    <p>
        <input type="text" style="width: 100%;" value="<?php echo $ecardName; ?>" readonly>
    </p>
    <p>
        <input type="email" style="width: 100%;" value="<?php echo $ecardEmailSender; ?>" readonly>
    </p>
    <p>
        <input type="email" style="width: 100%;" value="<?php echo $ecardEmailRecipient; ?>" readonly>
    </p>
    <p>
        <textarea style="width: 100%;" rows="6" readonly><?php echo $ecardContent; ?></textarea>
    </p>

	<?php
}
