<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function ecards_return_image_sizes() {
    global $_wp_additional_image_sizes;

    $image_sizes = [];
    foreach ( get_intermediate_image_sizes() as $size ) {
        $image_sizes[ $size ] = [ 0, 0 ];

        if ( in_array( $size, [ 'thumbnail', 'medium', 'large' ] ) ) {
            $image_sizes[ $size ][0] = get_option( $size . '_size_w' );
            $image_sizes[ $size ][1] = get_option( $size . '_size_h' );
        } else {
            if ( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $size ] ) ) {
                $image_sizes[ $size ] = [ $_wp_additional_image_sizes[ $size ]['width'], $_wp_additional_image_sizes[ $size ]['height'] ];
            }
        }
    }

    return $image_sizes;
}

function ecards_shortcode_fix() {
    add_filter( 'the_content', 'do_shortcode', 9 );
}

function ecards_set_content_type( $content_type ) {
    return 'text/html';
}

function ecard_check_spam( $content ) {
    // Innocent until proven guilty
    $is_spam = false;
    $content = (array) $content;

    if ( function_exists( 'akismet_init' ) && (string) get_option( 'ecard_use_akismet' ) === 'true' ) {
        $wpcom_api_key = get_option( 'wordpress_api_key' );

        if ( ! empty( $wpcom_api_key ) ) {
            // Set remaining required values for the Akismet API
            $content['user_ip']    = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
            $content['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $content['referrer']   = $_SERVER['HTTP_REFERER'];
            $content['blog']       = get_option( 'home' );

            if ( empty( $content['referrer'] ) ) {
                $content['referrer'] = get_permalink();
            }

            $query_string = '';

            foreach ( $content as $key => $data ) {
                if ( ! empty( $data ) ) {
                    $query_string .= $key . '=' . urlencode( stripslashes( $data ) ) . '&';
                }
            }

            $response = Akismet::http_post( $query_string, 'comment-check' );

            if ( $response[1] == 'true' ) {
                update_option( 'akismet_spam_count', get_option( 'akismet_spam_count' ) + 1 );

                $is_spam = true;
            }
        }
    }

    return $is_spam;
}

add_action( 'publish_ecard', 'ecard_send_later' );

function ecard_send_later( $ecard_id ) {
    $ecard_send_behaviour = get_option( 'ecard_send_behaviour' );

    // Send eCard
    if ( (int) $ecard_send_behaviour === 1 ) {
        $ecard_to = get_post_meta( $ecard_id, 'ecard_email_recipient', true );
    } elseif ( (int) $ecard_send_behaviour === 0 ) {
        $ecard_to = sanitize_email( get_option( 'ecard_hardcoded_email' ) );
    }

    // Check if <Mail From> fields are filled in
    $ecard_from       = get_post_meta( $ecard_id, 'ecard_name', true );
    $ecard_email_from = get_post_meta( $ecard_id, 'ecard_email_sender', true );

    // Email settings
    $subject = sanitize_text_field( get_option( 'ecard_title' ) );
    $subject = str_replace( '[name]', $ecard_from, $subject );
    $subject = str_replace( '[email]', $ecard_email_from, $subject );

    $ecard_object        = get_post( $ecard_id );
    $ecard_email_message = apply_filters( 'the_content', $ecard_object->post_content );

    $headers   = [];
    $headers[] = 'Content-Type: text/html;';
    $headers[] = 'X-Mailer: WordPress/eCards;';

    if ( (string) $ecard_to !== '' ) {
        wp_mail( $ecard_to, $subject, $ecard_email_message, $headers );
    }

    $ecard_email_cc = get_post_meta( $ecard_id, 'ecard_email_cc', true );
    if ( ! empty( $ecard_email_cc ) ) {
        wp_mail( $ecard_email_from, $subject, $ecard_email_message, $headers );
    }
}



/*
 * Attach additional images to any post or page straight from Media Library
 */
add_action( 'wp_ajax_ecards_detach', 'ecards_detach_callback' );
add_action( 'wp_ajax_nopriv_ecards_detach', 'ecards_detach_callback' );

function ecards_detach_callback() {
    $my_post = [
        'ID'          => $_POST['whatToDelete'],
        'post_parent' => 0,
    ];
    wp_update_post( $my_post );

    $selected_images   = get_post_meta( $_POST['whatPost'], '_ecards_additional_images', true );
    $additional_images = explode( '|', $selected_images );

    foreach ( array_keys( $additional_images, $_POST['whatToDelete'], true ) as $key ) {
        unset( $additional_images[ $key ] );
    }

    update_post_meta( $_POST['whatPost'], '_ecards_additional_images', array_values( $additional_images ) );

    exit;
}



function ecards_mail_from( $mail_from_email ) {
    $site_mail_from_email = sanitize_email( get_option( 'ecard_noreply' ) );

    if ( empty( $site_mail_from_email ) ) {
        return $mail_from_email;
    } else {
        return $site_mail_from_email;
    }
}

add_filter( 'wp_mail_from', 'ecards_mail_from', 1 );



function ecard_datetime_picker() {
    $day       = date( 'd' );
    $month     = date( 'm' );
    $startyear = date( 'Y' );
    $endyear   = date( 'Y' ) + 10;
    $months    = [ '', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ];

    $html = '<select name="ecard_send_time_month">';

    for ( $i = 1; $i <= 12; $i++ ) {
        if ( (int) $i === (int) $month ) {
            $html .= '<option selected value="' . str_pad( $i, 2, '0', STR_PAD_LEFT ) . '">' . $months[ $i ] . '</option>';
        } else {
            $html .= '<option value="' . str_pad( $i, 2, '0', STR_PAD_LEFT ) . '">' . $months[ $i ] . '</option>';
        }
    }

    $html .= '</select> <select name="ecard_send_time_day">';

    for ( $i = 1; $i <= 31; $i++ ) {
        if ( (int) $i === (int) $day ) {
            $html .= '<option selected value="' . str_pad( $i, 2, '0', STR_PAD_LEFT ) . '">' . $i . '</option>';
        } else {
            $html .= '<option value="' . str_pad( $i, 2, '0', STR_PAD_LEFT ) . '">' . $i . '</option>';
        }
    }

    $html .= '</select> <select name="ecard_send_time_year">';

    for ( $i = $startyear; $i <= $endyear; $i++ ) {
        $html .= '<option value="' . $i . '">' . $i . '</option>';
    }

    $html .= '</select> <select name="ecard_send_time_hour">';

    for ( $hours = 0; $hours < 24; $hours++ ) {
        for ( $mins = 0; $mins < 60; $mins += 30 ) {
            $html .= '<option>' . str_pad( $hours, 2, '0', STR_PAD_LEFT ) . ':' . str_pad( $mins, 2, '0', STR_PAD_LEFT ) . '</option>';
        }
    }

    $html .= '</select>';

    return $html;
}




/**
 * Register eCards meta box
 */
function ecards_add_meta_box() {
    add_meta_box( 'ecards_details_meta_box', __( 'eCards Details', 'ecards' ), 'ecards_details_metabox_callback', [ 'ecard' ] );
}
add_action( 'add_meta_boxes', 'ecards_add_meta_box' );

function ecards_details_metabox_callback( $post ) {
    $ecard_content         = get_post_meta( $post->ID, 'ecard_content', true );
    $ecard_email_recipient = get_post_meta( $post->ID, 'ecard_email_recipient', true );
    $ecard_email_sender    = get_post_meta( $post->ID, 'ecard_email_sender', true );
    $ecard_name            = get_post_meta( $post->ID, 'ecard_name', true );
    ?>

    <p>
        <input type="text" style="width: 100%;" value="<?php echo $ecard_name; ?>" readonly>
    </p>
    <p>
        <input type="email" style="width: 100%;" value="<?php echo $ecard_email_sender; ?>" readonly>
    </p>
    <p>
        <input type="email" style="width: 100%;" value="<?php echo $ecard_email_recipient; ?>" readonly>
    </p>
    <p>
        <textarea style="width: 100%;" rows="6" readonly><?php echo $ecard_content; ?></textarea>
    </p>

    <?php
}
