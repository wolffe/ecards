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
            $content['user_ip'] = isset( $_SERVER['REMOTE_ADDR'] ) ?
                filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP ) : '';

            $content['user_agent'] = isset( $_SERVER['HTTP_USER_AGENT'] ) ?
                sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

            $content['referrer'] = isset( $_SERVER['HTTP_REFERER'] ) ?
                esc_url_raw( $_SERVER['HTTP_REFERER'] ) : '';

            $content['blog'] = get_option( 'home' );

            if ( empty( $content['referrer'] ) ) {
                $content['referrer'] = get_permalink();
            }

            // Sanitize all content values
            $content = array_map( 'sanitize_text_field', $content );

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
    // Verify nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ecards_detach_nonce' ) ) {
        wp_send_json_error( 'Invalid nonce' );
    }

    // Verify user capabilities
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Insufficient permissions' );
    }

    // Sanitize and validate input
    $post_id   = isset( $_POST['whatPost'] ) ? absint( $_POST['whatPost'] ) : 0;
    $delete_id = isset( $_POST['whatToDelete'] ) ? absint( $_POST['whatToDelete'] ) : 0;

    if ( ! $post_id || ! $delete_id ) {
        wp_send_json_error( 'Invalid post IDs' );
    }

    $my_post = [
        'ID'          => $delete_id,
        'post_parent' => 0,
    ];
    wp_update_post( $my_post );

    $selected_images = get_post_meta( $post_id, '_ecards_additional_images', true );
    if ( ! empty( $selected_images ) ) {
        $additional_images = explode( '|', $selected_images );
        foreach ( array_keys( $additional_images, $delete_id, true ) as $key ) {
            unset( $additional_images[ $key ] );
        }
        update_post_meta( $post_id, '_ecards_additional_images', array_values( $additional_images ) );
    }

    wp_send_json_success();
}



function ecard_datetime_picker() {
    $day       = gmdate( 'd' );
    $month     = gmdate( 'm' );
    $startyear = gmdate( 'Y' );
    $endyear   = gmdate( 'Y' ) + 10;
    $months    = [ '', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ];

    $html = '<div class="ecard-datetime-picker" role="group" aria-label="' . esc_attr__( 'Select date and time to send the eCard', 'ecards' ) . '">';

    // Month selector
    $html .= '<div class="ecard-date-field">';
    $html .= '<label for="ecard_send_time_month" class="screen-reader-text">' . esc_html__( 'Month', 'ecards' ) . '</label>';
    $html .= '<select name="ecard_send_time_month" id="ecard_send_time_month" aria-label="' . esc_attr__( 'Select month', 'ecards' ) . '">';

    for ( $i = 1; $i <= 12; $i++ ) {
        $selected = ( (int) $i === (int) $month ) ? ' selected' : '';
        $html    .= sprintf(
            '<option value="%s"%s>%s</option>',
            str_pad( $i, 2, '0', STR_PAD_LEFT ),
            $selected,
            esc_html( $months[ $i ] )
        );
    }
    $html .= '</select>';

    // Day selector
    $html .= '<label for="ecard_send_time_day" class="screen-reader-text">' . esc_html__( 'Day', 'ecards' ) . '</label>';
    $html .= '<select name="ecard_send_time_day" id="ecard_send_time_day" aria-label="' . esc_attr__( 'Select day', 'ecards' ) . '">';

    for ( $i = 1; $i <= 31; $i++ ) {
        $selected = ( (int) $i === (int) $day ) ? ' selected' : '';
        $html    .= sprintf(
            '<option value="%s"%s>%d</option>',
            str_pad( $i, 2, '0', STR_PAD_LEFT ),
            $selected,
            $i
        );
    }
    $html .= '</select>';

    // Year selector
    $html .= '<label for="ecard_send_time_year" class="screen-reader-text">' . esc_html__( 'Year', 'ecards' ) . '</label>';
    $html .= '<select name="ecard_send_time_year" id="ecard_send_time_year" aria-label="' . esc_attr__( 'Select year', 'ecards' ) . '">';

    for ( $i = $startyear; $i <= $endyear; $i++ ) {
        $html .= sprintf(
            '<option value="%d">%d</option>',
            $i,
            $i
        );
    }
    $html .= '</select>';

    // Time selector
    $html .= '<label for="ecard_send_time_hour" class="screen-reader-text">' . esc_html__( 'Time', 'ecards' ) . '</label>';
    $html .= '<select name="ecard_send_time_hour" id="ecard_send_time_hour" aria-label="' . esc_attr__( 'Select time', 'ecards' ) . '">';

    for ( $hours = 0; $hours < 24; $hours++ ) {
        for ( $mins = 0; $mins < 60; $mins += 30 ) {
            $time  = sprintf(
                '%02d:%02d',
                $hours,
                $mins
            );
            $html .= sprintf(
                '<option value="%s">%s</option>',
                esc_attr( $time ),
                esc_html( $time )
            );
        }
    }
    $html .= '</select>';
    $html .= '</div>';

    // Add JavaScript for enhanced accessibility
    $html .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const dateFields = document.querySelectorAll(".ecard-date-field select");
            dateFields.forEach(field => {
                field.addEventListener("change", function() {
                    this.setAttribute("aria-invalid", "false");
                });
            });
        });
    </script>';

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
