<?php
/*
Plugin Name: eCards
Plugin URI: https://getbutterfly.com/wordpress-plugins/wordpress-ecards-plugin/
Description: eCards is a plugin used to send electronic cards to friends. It can be implemented in a page, a post, a custom post or the sidebar. eCards makes it quick and easy for you to send an eCard in three steps. Just choose your favorite eCard, add your personal message and send it to any email address. Use preset images or upload your own.
Author: Ciprian Popescu
Author URI: https://getbutterfly.com/
Version: 5.5.1
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: ecards

eCards
Copyright (C) 2011-2025 Ciprian Popescu (getbutterfly@gmail.com)

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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ECARDS_VERSION', '5.5.1' );

require plugin_dir_path( __FILE__ ) . '/includes/updater.php';

/**
 * Plugin initialization
 */
function ecards_init() {
    load_plugin_textdomain( 'ecards', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'ecards_init' );

function ecards_setup() {
    add_image_size( 'ecard', 600, 9999, false );
}
add_action( 'after_setup_theme', 'ecards_setup' );

require plugin_dir_path( __FILE__ ) . '/includes/cpt-ecard.php';
require plugin_dir_path( __FILE__ ) . '/includes/cpt-ecard-collection.php';
require plugin_dir_path( __FILE__ ) . '/includes/functions.php';
require plugin_dir_path( __FILE__ ) . '/includes/page-options.php';



/**
 * Debugging and legacy features
 */
if ( (string) get_option( 'ecard_shortcode_fix' ) === 'on' ) {
    add_action( 'init', 'ecards_shortcode_fix', 12 );
}
if ( (string) get_option( 'ecard_html_fix' ) === 'on' ) {
    add_filter( 'wp_mail_content_type', 'ecards_set_content_type' );
}



function ecards_install() {
    add_option( 'ecard_label_name_own', 'Your name' );
    add_option( 'ecard_label_email_own', 'Your email address' );
    add_option( 'ecard_label_email_friend', 'Recipient Email' );
    add_option( 'ecard_label_message', 'eCard message' );
    add_option( 'ecard_label_send_time', 'Schedule this eCard' );
    add_option( 'ecard_label_cc', 'Send a copy to self' );
    add_option( 'ecard_label_success', 'eCard sent successfully!' );
    add_option( 'ecard_label_gdpr_privacy_policy_page', 'Check here to indicate that you have read and agree to our terms and conditions' );
    add_option( 'ecard_gdpr_privacy_policy_page', 0 );
    add_option( 'ecard_submit', 'Send eCard' );
    add_option( 'ecard_label', 1 );
    add_option( 'ecard_link_anchor', 'Click to see your eCard!' );
    add_option( 'ecard_redirection', 0 );
    add_option( 'ecard_page_thankyou', '' );
    add_option( 'ecard_title', 'eCard!' );
    add_option( 'ecard_body_toggle', 1 );
    add_option( 'ecard_restrictions', 0 );
    add_option( 'ecard_restrictions_message', 'This section is restricted to members only!' );
    add_option( 'ecard_send_behaviour', 1 );
    add_option( 'ecard_hardcoded_email', '' );
    add_option( 'ecard_send_later', 0 );
    add_option( 'ecard_image_size', 'thumbnail' );
    add_option( 'ecard_image_size_email', 'medium' );
    add_option( 'ecard_shortcode_fix', 'off' );
    add_option( 'ecard_html_fix', 'off' );
    add_option( 'ecard_allow_cc', 'off' );
    add_option( 'ecard_use_akismet', 'false' );
    add_option( 'ecard_columns', 3 );
    add_option( 'ecard_captcha', 0 );

    add_option( 'ecard_allow_csv', 0 );
    add_option( 'ecard_captcha', 0 );

    add_option( 'ecard_use_shadow', 1 );
    add_option( 'ecard_use_highlight', 1 );
    add_option( 'ecard_use_radius', 4 );
    add_option( 'ecard_color_scheme', 'light' );
    add_option( 'ecard_color_accent', '#3742fa' );
    add_option( 'ecard_button_background', '#3742fa' );
    add_option( 'ecard_button_color', '#ffffff' );
}

register_activation_hook( __FILE__, 'ecards_install' );



function ecards_enqueue_scripts() {
    wp_register_style( 'akar-icons', plugins_url( '/assets/icons/akar-icons.css', __FILE__ ), [], '1.1.22' );
    wp_register_style( 'ecards-ui', plugins_url( '/css/ui.css', __FILE__ ), [], ECARDS_VERSION );
}
add_action( 'wp_enqueue_scripts', 'ecards_enqueue_scripts' );




function ecard_get_attachments( $ecid, $id_array ) {
    wp_enqueue_style( 'ecards-ui' );

    /**
     * Get all post attachments and exclude featured image
     */
    $output = '';

    $args = [
        'post_type'      => 'attachment',
        'numberposts'    => -1,
        'post_status'    => null,
        'post_mime_type' => 'image',
        'orderby'        => 'post__in',
        'order'          => 'ASC',
        'exclude'        => get_post_thumbnail_id( $ecid ),
    ];

    if ( count( (array) $id_array ) > 0 ) {
        $args['post__in'] = $id_array;
    } else {
        $args['post_parent'] = $ecid;
    }

    $attachments = get_posts( $args );

    $ecard_label            = (int) get_option( 'ecard_label' );
    $ecard_image_size       = get_option( 'ecard_image_size' );
    $ecard_image_size_email = get_option( 'ecard_image_size_email' );
    $ecard_columns          = (int) get_option( 'ecard_columns' );
    $ecard_columns          = ( $ecard_columns > 0 ) ? $ecard_columns : 3;

    $ecard_use_shadow    = (int) get_option( 'ecard_use_shadow' ) === 1 ? '0.25em 0.25em 2em rgba(0, 0, 0, 0.25)' : 'none';
    $ecard_use_radius    = (int) get_option( 'ecard_use_radius' );
    $ecard_use_highlight = (int) get_option( 'ecard_use_highlight' ) === 1 ? 'highlight' : 'no-highlight';
    $ecard_color_scheme  = get_option( 'ecard_color_scheme', 'light' );
    $ecard_color_accent  = get_option( 'ecard_color_accent', '#0000ff' );

    if ( $attachments ) {
        $output .= '<style>
        .ecard-grid-container {
            --ecard-shadow: ' . $ecard_use_shadow . ';
            --ecard-radius: ' . $ecard_use_radius . 'px;
        }
        </style>

        <div role="radiogroup" class="ecard-grid-container ecard-use-' . $ecard_use_highlight . '" style="--ecard-columns: ' . $ecard_columns . '; --ecard-color-scheme: ' . $ecard_color_scheme . '; --ecard-color-accent: ' . $ecard_color_accent . ';">';

        foreach ( $attachments as $a ) {
            $output .= '<div class="ecard-grid-item">';

                $large = wp_get_attachment_image_src( $a->ID, $ecard_image_size_email );
                $thumb = wp_get_attachment_image( $a->ID, $ecard_image_size );

                $ecard_image_element = '<figure class="wp-block-image size-large">
                    ' . $thumb . '
                </figure>';

            if ( $ecard_label === 0 ) {
                $output .= '<a href="' . $large[0] . '" class="ecards">' . $ecard_image_element . '</a><input type="radio" name="ecard_pick_me" id="ecard' . $a->ID . '" value="' . $a->ID . '" checked><label for="ecard' . $a->ID . '"></label>';
            } elseif ( $ecard_label === 1 ) {
                $output .= '<label for="ecard' . $a->ID . '">' . $ecard_image_element . '<input type="radio" name="ecard_pick_me" id="ecard' . $a->ID . '" value="' . $a->ID . '" checked></label>';
            }

            $output .= '</div>';
        }

        $output .= '</div>';
    }

    return $output;
}



function wp_ecard_display_ecards( $atts ) {
    wp_enqueue_style( 'akar-icons' );

    $attributes = shortcode_atts(
        [
            'id'         => '',
            'collection' => 0,
        ],
        $atts
    );

    $send_success = 0;

    $id_array = [];

    if ( (int) $attributes['collection'] > 0 ) {
        $collection_id = (int) $attributes['collection'];

        $collection_content = get_post_field( 'post_content', $collection_id );

        if ( has_block( 'gallery', $collection_content ) ) {
            // If there is a gallery block (block editor)
            $post_blocks = parse_blocks( $collection_content );

            $post_blocks_array = $post_blocks[0]['innerBlocks'];

            foreach ( $post_blocks_array as $post_block ) {
                $id_array[] = $post_block['attrs']['id'];
            }
        } else {
            // If there is no gallery block (classic editor)

            // Get the gallery info
            $gallery = get_post_gallery( $collection_id, false );

            if ( isset( $gallery ) && isset( $gallery['ids'] ) && is_array( $gallery['ids'] ) && count( $gallery['ids'] ) > 0 ) {
                // Make an array of image IDs
                $id_array = explode( ',', $gallery['ids'] );
            }
        }
    }

    if ( (string) $attributes['id'] !== '' ) {
        $id_array = array_map( 'trim', explode( ',', $attributes['id'] ) );
        $id_array = array_filter( $id_array );
    }

    $ecard_submit = get_option( 'ecard_submit' );

    $ecard_send_behaviour = (int) get_option( 'ecard_send_behaviour' );
    $ecard_redirection    = (int) get_option( 'ecard_redirection' );
    $ecard_link_anchor    = get_option( 'ecard_link_anchor' );
    $ecard_page_thankyou  = get_option( 'ecard_page_thankyou' );
    $ecard_title          = get_option( 'ecard_title' );

    // Get eCard template
    $ecard_template = wpautop( get_option( 'ecard_template' ) );

    $ecard_body_toggle      = (int) get_option( 'ecard_body_toggle' );
    $ecard_image_size_email = get_option( 'ecard_image_size_email' );

    // send eCard routine
    // since eCards 2.2.0
    if ( isset( $_POST['ecard_send'] ) ) {
        // Verify nonce
        if ( ! isset( $_POST['ecard_nonce'] ) || ! wp_verify_nonce( $_POST['ecard_nonce'], 'ecard_send_nonce' ) ) {
            echo '<div class="ecard-error">' . __( 'Security check failed. Please try again.', 'ecards' ) . '</div>';
            return $output;
        }

        // Validate CAPTCHA if enabled
        if ( (int) get_option( 'ecard_captcha' ) === 1 ) {
            if ( ! isset( $_POST['ecard_captcha_answer'] ) || ! ecard_validate_captcha( $_POST['ecard_captcha_answer'] ) ) {
                echo '<div class="ecard-error">' . __( 'Invalid CAPTCHA answer. Please try again.', 'ecards' ) . '</div>';
                return $output;
            }
        }

        // begin user attachment (if any)
        $no_attachments = 1;

        if ( ! empty( $_FILES['file']['name'] ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $no_attachments = 0;
            move_uploaded_file( $_FILES['file']['tmp_name'], WP_CONTENT_DIR . '/uploads/' . basename( $_FILES['file']['name'] ) );

            // attach user uploaded image to eCard custom post
            $filetype = wp_check_filetype( basename( $_FILES['file']['name'] ), null );

            $attachment = [
                'post_mime_type' => $filetype['type'],
                'post_title'     => $_FILES['file']['name'],
                'post_content'   => '',
                'post_status'    => 'inherit',
            ];

            $attach_id   = wp_insert_attachment( $attachment, WP_CONTENT_DIR . '/uploads/' . $_FILES['file']['name'] );
            $attach_data = wp_generate_attachment_metadata( $attach_id, WP_CONTENT_DIR . '/uploads/' . $_FILES['file']['name'] );

            wp_update_attachment_metadata( $attach_id, $attach_data );
        }
        // end user attachment

        // gallery (attachments) mode
        $ecard_pick_me = '';
        if ( isset( $_POST['ecard_pick_me'] ) && $no_attachments === 1 ) {
            $ecard_pick_me = sanitize_text_field( $_POST['ecard_pick_me'] );
            $large         = wp_get_attachment_image_src( $ecard_pick_me, $ecard_image_size_email );
            $ecard_pick_me = '<img src="' . $large[0] . '" alt="" style="max-width: 100%;">';
        }
        //

        if ( $ecard_send_behaviour === 1 ) {
            $recipient_emails = [];

            // Handle CSV file upload
            if ( ! empty( $_FILES['email_csv']['name'] ) ) {
                $file = $_FILES['email_csv'];
                if ( $file['type'] === 'text/csv' || $file['type'] === 'application/vnd.ms-excel' ) {
                    $recipient_emails = ecard_process_csv_file( $file );
                }
            }

            // Handle email input field
            if ( ! empty( $_POST['ecard_to'] ) ) {
                $email_list = explode( ',', $_POST['ecard_to'] );
                foreach ( $email_list as $email ) {
                    $email = sanitize_email( trim( $email ) );
                    if ( is_email( $email ) ) {
                        $recipient_emails[] = $email;
                    }
                }
            }

            // Remove duplicates and empty values
            $recipient_emails = array_unique( array_filter( $recipient_emails ) );

            if ( empty( $recipient_emails ) ) {
                echo '<div class="ecard-error">' . __( 'Please provide at least one valid email address.', 'ecards' ) . '</div>';
                return $output;
            }

            $ecard_to = $recipient_emails;
        } elseif ( $ecard_send_behaviour === 0 ) {
            $ecard_to = sanitize_email( get_option( 'ecard_hardcoded_email' ) );
        }

        // check if <Mail From> fields are filled in
        $ecard_from          = sanitize_text_field( $_POST['ecard_from'] );
        $ecard_email_from    = sanitize_email( $_POST['ecard_email_from'] );
        $ecard_email_message = wpautop( stripslashes( $_POST['ecard_message'] ) );

        $ecard_referer = esc_url( $_POST['ecard_referer'] );
        if ( isset( $_POST['ecard_pick_me'] ) ) {
            $ecard_attachment = wp_get_attachment_link( sanitize_text_field( $_POST['ecard_pick_me'] ), $ecard_image_size_email, true, false, $ecard_link_anchor );
        } else {
            $ecard_attachment = '';
        }

        // eCard direct URL
        $ecard_url = '';

        if ( isset( $_POST['ecard_pick_me'] ) ) {
            $ecard_attachment_id  = sanitize_text_field( $_POST['ecard_pick_me'] );
            $ecard_attachment_url = wp_get_attachment_url( $ecard_attachment_id );
            $ecard_url            = '<a href="' . $ecard_attachment_url . '">' . $ecard_link_anchor . '</a>';
        }
        //

        // Set the eCard image
        if ( ! empty( $ecard_pick_me ) ) {
            $ecard_image = $ecard_pick_me;
        } else {
            $image       = wp_get_attachment_image_src( $attach_id, $ecard_image_size_email );
            $ecard_image = '<img src="' . $image[0] . '" alt="" style="max-width: 100%;">';
        }

        $ecard_content = sanitize_text_field( $_POST['ecard_include_content'] );

        // eCard Designer
        $ecard_template = str_replace( '[name]', $ecard_from, $ecard_template );
        $ecard_template = str_replace( '[email]', $ecard_email_from, $ecard_template );
        $ecard_template = str_replace( '[image]', $ecard_image, $ecard_template );
        $ecard_template = str_replace( '[ecard-message]', $ecard_email_message, $ecard_template );
        $ecard_template = str_replace( '[ecard-link]', $ecard_attachment, $ecard_template );
        $ecard_template = str_replace( '[ecard-url]', $ecard_url, $ecard_template );
        $ecard_template = str_replace( '[ecard-content]', $ecard_content, $ecard_template );
        $ecard_template = str_replace( '[ecard-referrer]', $ecard_referer, $ecard_template );
        //

        $subject = sanitize_text_field( $ecard_title );
        $subject = str_replace( '[name]', $ecard_from, $subject );
        $subject = str_replace( '[email]', $ecard_email_from, $subject );

        $headers[] = "Reply-To: $ecard_from <$ecard_email_from>";
        $headers[] = 'Content-Type: text/html;';
        $headers[] = 'X-Mailer: WordPress/eCards;';

        // Akismet
        $content['comment_author']       = $ecard_from;
        $content['comment_author_email'] = $ecard_email_from;
        $content['comment_author_url']   = home_url();
        $content['comment_content']      = $ecard_email_message;

        if ( ecard_check_spam( $content ) ) {
            echo '<p><strong>' . __( 'Akismet prevented sending of this eCard and marked it as spam!', 'ecards' ) . '</strong></p>';
        } else {
            /**
             * Create eCard object (custom post type)
             */
            if ( isset( $_POST['ecard_send_time_enable'] ) && (int) $_POST['ecard_send_time_enable'] === 1 ) {
                $current_datetime = current_datetime();
                $current_datetime = $current_datetime->format( 'Y/m/d H:i:s' );

                $ecard_built_date = $_POST['ecard_send_time_year'] . '-' . $_POST['ecard_send_time_month'] . '-' . $_POST['ecard_send_time_day'] . ' ' . $_POST['ecard_send_time_hour'] . ':00';
                $ecard_send_time  = strtotime( $ecard_built_date );
                $ecard_send_time  = gmdate( 'Y-m-d H:i:s', $ecard_send_time );

                $ecard_post = [
                    'post_title'   => __( 'eCard', 'ecards' ) . ' (' . $current_datetime . ')',
                    'post_content' => $ecard_template,
                    'post_status'  => 'future',
                    'post_type'    => 'ecard',
                    'post_author'  => 1,
                    'post_date'    => $ecard_send_time,
                ];
            } else {
                $current_datetime = current_datetime();
                $current_datetime = $current_datetime->format( 'Y/m/d H:i:s' );

                $ecard_post = [
                    'post_title'   => __( 'eCard', 'ecards' ) . ' (' . $current_datetime . ')',
                    'post_content' => $ecard_template,
                    'post_status'  => 'private',
                    'post_type'    => 'ecard',
                    'post_author'  => 1,
                ];
            }

            // Insert the eCard into the database
            $ecard_id = wp_insert_post( $ecard_post );

            if ( isset( $_POST['ecard_pick_me'] ) ) {
                // Add featured image to post
                $ecard_picked = (int) $_POST['ecard_pick_me'];

                set_post_thumbnail( $ecard_id, $ecard_picked );
            }
            if ( ! empty( $_FILES['file']['name'] ) ) {
                set_post_thumbnail( $ecard_id, $attach_id );
            }

            add_post_meta( $ecard_id, 'ecard_name', $ecard_from, true );
            add_post_meta( $ecard_id, 'ecard_email_sender', $ecard_email_from, true );
            add_post_meta( $ecard_id, 'ecard_email_recipient', $ecard_to, true );
            if ( isset( $_POST['ecard_allow_cc'] ) ) {
                add_post_meta( $ecard_id, 'ecard_email_cc', $_POST['ecard_allow_cc'], true );
            }

            $ecard_content_converted = str_replace( '[name]', $ecard_from, $_POST['ecard_include_content'] );
            $ecard_content_converted = str_replace( '[email]', $ecard_email_from, $ecard_content_converted );

            add_post_meta( $ecard_id, 'ecard_content', sanitize_text_field( $ecard_content_converted ), true );

            if ( ! isset( $_POST['ecard_send_time_enable'] ) ) {
                // mail sending
                if ( is_array( $ecard_to ) ) {
                    foreach ( $ecard_to as $recipient ) {
                        wp_mail( $recipient, $subject, $ecard_template, $headers );
                    }
                } else {
                    // Check if it's a comma-separated list
                    $emails = array_map( 'trim', explode( ',', $ecard_to ) );
                    if ( count( $emails ) > 1 ) {
                        foreach ( $emails as $recipient ) {
                            wp_mail( $recipient, $subject, $ecard_template, $headers );
                        }
                    } else {
                        wp_mail( $ecard_to, $subject, $ecard_template, $headers );
                    }
                }

                if ( isset( $_POST['ecard_allow_cc'] ) ) {
                    wp_mail( $ecard_email_from, $subject, $ecard_template, $headers );
                }
            }

            // redirection
            if ( $ecard_redirection === 1 && (string) $ecard_page_thankyou !== '' ) {
                echo '<meta http-equiv="refresh" content="0;url=' . esc_url( $ecard_page_thankyou ) . '">';
                exit;
            }

            $send_success = 1;
        }
    }

    // Generate new CAPTCHA if enabled
    $captcha_data = null;
    if ( (int) get_option( 'ecard_captcha' ) === 1 ) {
        $captcha_data = ecard_generate_captcha();
    }

    /**
     * Display eCard grid
     */

    // Inline Critical CSS
    $output = '<style>
    .ecard-container input[type=text],.ecard-container input[type=email],.ecard-container input[type=submit],.ecard-container select,.ecard-container textarea{font-family:inherit;font-size:inherit;padding:8px;margin-bottom:4px;width:auto}.ecard-container select{height:auto}.ecard-confirmation{background-color:#7bdcb5;color:#000;padding:1.25em 2.375em}@media all and (max-width:720px){#ecard_email_from,#ecard_from,#ecard_message,#ecard_to{width:100%!important}}
    :root {
        --ecard-color-accent: ' . get_option( 'ecard_color_accent' ) . ';
        --ecard-button-color: ' . get_option( 'ecard_button_color' ) . ';
        --ecard-button-background: ' . get_option( 'ecard_button_background' ) . ';
    }
    </style>';

    // Inline custom CSS
    $output .= '<style>' .
        stripslashes( get_option( 'ecards_custom_css' ) ) .
    '</style>';

    if ( (int) $send_success === 1 ) {
        $ecard_label_success = get_option( 'ecard_label_success' );

        $output .= '<p class="ecard-confirmation">' . esc_html( $ecard_label_success ) . '</p>';
    }

    $output         .= '<div class="ecard-container">
        <form action="#" method="post" enctype="multipart/form-data" id="eCardForm">';
            $output .= wp_nonce_field( 'ecard_send_nonce', 'ecard_nonce', true, false );
            $output .= ecard_get_attachments( get_the_ID(), $id_array );

            $output .= '<div class="ecards-ui--grid">
                <div>';

    if ( (int) get_option( 'ecard_user_enable' ) === 1 ) {
        $output .= '<details class="ecards-ui--details">
            <summary><i class="ai-cloud-upload"></i> ' . __( 'Upload Image', 'ecards' ) . '</summary>
            <p><input type="file" name="file" id="file"></p>
        </details>';
    }

            $output .= '
                <p>
                    <label for="ecard_from">' . get_option( 'ecard_label_name_own' ) . '</label><br>
                    <input type="text" id="ecard_from" name="ecard_from" size="30" required>
                </p>
                <p>
                    <label for="ecard_email_from">' . get_option( 'ecard_label_email_own' ) . '</label><br>
                    <input type="email" id="ecard_email_from" name="ecard_email_from" size="30" required>
                </p>';

    if ( $ecard_send_behaviour === 1 ) {
        $output .= '<details class="ecards-ui--details" name="recipient_emails" open>
        <summary><i class="ai-envelope"></i> ' . get_option( 'ecard_label_email_friend' ) . '</summary>
        <p>
            <label for="ecard_to">' . get_option( 'ecard_label_email_friend' ) . '</label><br>
            <input type="text" id="ecard_to" name="ecard_to" size="30" required 
                placeholder="' . __( 'Enter multiple emails separated by commas', 'ecards' ) . '">
            <br><small>' . __( 'You can enter multiple email addresses separated by commas', 'ecards' ) . '</small>
        </p>
        </details>';

        // Only show CSV upload if enabled in settings
        if ( (int) get_option( 'ecard_allow_csv' ) === 1 ) {
            $output .= '<details class="ecards-ui--details" name="recipient_emails">
            <summary><i class="ai-circle-plus"></i> ' . __( 'Add More Recipients', 'ecards' ) . '</summary>
            <p>
                <label for="email_csv">' . __( 'Upload a CSV file with email addresses', 'ecards' ) . '</label><br>
                <input type="file" id="email_csv" name="email_csv" accept=".csv">
                <br><small>' . __( 'Upload a CSV file containing email addresses (one per line or comma-separated)', 'ecards' ) . '</small>
            </p>
            </details>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const emailInput = document.getElementById("ecard_to");
                    const csvInput = document.getElementById("email_csv");
                    
                    function updateEmailRequired() {
                        emailInput.required = !csvInput.files.length;
                    }
                    
                    csvInput.addEventListener("change", updateEmailRequired);
                    updateEmailRequired(); // Initial check
                });
            </script>';
        }
    }

                $output .= '</div>
                <div>';

                $ecard_send_later = get_option( 'ecard_send_later' );

    if ( (int) $ecard_send_later === 1 ) {
        $output .= '<details class="ecards-ui--details">
            <summary><i class="ai-clock"></i> ' . __( 'Schedule', 'ecards' ) . '</summary>
            <p>
                <input type="checkbox" name="ecard_send_time_enable" id="ecard_send_time_enable" value="1"> <label for="ecard_send_time_enable">' . get_option( 'ecard_label_send_time' ) . '</label></p>' .
                ecard_datetime_picker() .
        '</details>';
    }

    if ( (int) $ecard_body_toggle === 1 ) {
        $output .= '<p><label for="ecard_message">' . get_option( 'ecard_label_message' ) . '</label><br><textarea id="ecard_message" name="ecard_message" rows="6" cols="60"></textarea></p>';
    } elseif ( (int) $ecard_body_toggle === 0 ) {
        $output .= '<input type="hidden" name="ecard_message">';
    }

                $output .= '<input type="hidden" name="ecard_include_content" value="' . strip_tags( strip_shortcodes( get_the_content() ) ) . '">';

    if ( get_option( 'ecard_allow_cc' ) === 'on' ) {
        $output .= '<p><input type="checkbox" name="ecard_allow_cc" id="ecard_allow_cc"> <label for="ecard_allow_cc">' . get_option( 'ecard_label_cc' ) . '</label></p>';
    }

    if ( (int) get_option( 'ecard_gdpr_privacy_policy_page' ) > 0 && (string) get_option( 'ecard_label_gdpr_privacy_policy_page' ) !== '' ) {
        $output .= '<p><input type="checkbox" name="ecard_consent" id="ecard_consent"> <label for="ecard_consent"><a href="' . get_permalink( (int) get_option( 'ecard_gdpr_privacy_policy_page' ) ) . '" target="_blank">' . get_option( 'ecard_label_gdpr_privacy_policy_page' ) . '</a></label></p>';
        $output .= '<script>document.addEventListener("DOMContentLoaded",function(){document.getElementById("ecard_consent")&&(document.getElementById("ecard_send").disabled=!0,document.getElementById("ecard_consent").addEventListener("click",function(){document.getElementById("ecard_consent").checked?document.getElementById("ecard_send").disabled=!1:document.getElementById("ecard_send").disabled=!0}))});</script>';
    }

    // Add CAPTCHA field if enabled
    if ( (int) get_option( 'ecard_captcha' ) === 1 && $captcha_data ) {
        $output .= '<p>
            <label for="ecard_captcha_answer">' . __( 'Security Check', 'ecards' ) . '</label><br>
            <img src="' . esc_attr( $captcha_data['image'] ) . '" alt="CAPTCHA" style="border: 1px solid #ccc; margin-bottom: 10px;"><br>
            <input type="text" id="ecard_captcha_answer" name="ecard_captcha_answer" required maxlength="5" style="text-transform: uppercase;">
            <br><small>' . __( 'Please enter the 5 characters shown in the image above.', 'ecards' ) . '</small>
        </p>';
    }

                $output .= '<p>
                    <input type="hidden" name="ecard_referer" value="' . get_permalink() . '">
                    <input type="submit" id="ecard_send" name="ecard_send" value="' . $ecard_submit . '">
                </p>
            </div>
        </form>
    </div>';

    if ( (int) get_option( 'ecard_restrictions' ) === 0 || (int) get_option( 'ecard_restrictions' ) === 1 && is_user_logged_in() ) {
        return $output;
    } elseif ( (int) get_option( 'ecard_restrictions' ) === 1 && ! is_user_logged_in() ) {
        $output = get_option( 'ecard_restrictions_message' );
    }

    return $output;
}

add_shortcode( 'ecard', 'wp_ecard_display_ecards' );

// Displays options menu
function ecard_add_option_page() {
    add_submenu_page( 'edit.php?post_type=ecard', __( 'eCards Settings', 'ecards' ), __( 'eCards Settings', 'ecards' ), 'manage_options', 'ecard_options_page', 'ecard_options_page' );
}

add_action( 'admin_menu', 'ecard_add_option_page' );

// custom settings link inside Plugins section
function ecards_settings_link( $links ) {
    $settings_link = '<a href="' . admin_url( 'edit.php?post_type=ecard&page=ecard_options_page' ) . '">' . __( 'Settings', 'ecards' ) . '</a>';
    array_unshift( $links, $settings_link );

    return $links;
}
$plugin = plugin_basename( __FILE__ );

add_filter( "plugin_action_links_$plugin", 'ecards_settings_link' );

// Add CAPTCHA functions
function ecard_generate_captcha() {
    // Generate random string
    $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Excluding confusing characters
    $string     = '';
    for ( $i = 0; $i < 5; $i++ ) {
        $string .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
    }

    // Store the result in a transient
    set_transient( 'ecard_captcha_' . get_current_user_id(), $string, 300 ); // 5 minutes expiry

    // Create image
    $image = imagecreatetruecolor( 150, 50 );
    if ( ! $image ) {
        return false;
    }

    // Allocate colors
    $bg          = imagecolorallocate( $image, 255, 255, 255 );
    $text_color  = imagecolorallocate( $image, 0, 0, 0 );
    $noise_color = imagecolorallocate( $image, 200, 200, 200 );

    // Fill background
    imagefilledrectangle( $image, 0, 0, 150, 50, $bg );

    // Add noise
    for ( $i = 0; $i < 100; $i++ ) {
        imagesetpixel( $image, rand( 0, 150 ), rand( 0, 50 ), $noise_color );
    }

    // Add lines
    for ( $i = 0; $i < 3; $i++ ) {
        imageline( $image, rand( 0, 150 ), rand( 0, 50 ), rand( 0, 150 ), rand( 0, 50 ), $noise_color );
    }

    // Add text
    $font_size = 5;
    $x         = 20;
    $y         = 30;
    imagestring( $image, $font_size, $x, $y, $string, $text_color );

    // Output image
    ob_start();
    imagepng( $image );
    $image_data = ob_get_clean();
    imagedestroy( $image );

    if ( empty( $image_data ) ) {
        return false;
    }

    return [
        'image'  => 'data:image/png;base64,' . base64_encode( $image_data ),
        'string' => $string,
    ];
}

function ecard_validate_captcha( $answer ) {
    $stored_result = get_transient( 'ecard_captcha_' . get_current_user_id() );
    delete_transient( 'ecard_captcha_' . get_current_user_id() );
    return strtoupper( $answer ) === strtoupper( $stored_result );
}

// Add this function to handle CSV file processing
function ecard_process_csv_file( $file ) {
    $emails = [];
    if ( ( $handle = fopen( $file['tmp_name'], 'r' ) ) !== false ) {
        while ( ( $data = fgetcsv( $handle ) ) !== false ) {
            foreach ( $data as $email ) {
                $email = sanitize_email( trim( $email ) );
                if ( is_email( $email ) ) {
                    $emails[] = $email;
                }
            }
        }
        fclose( $handle );
    }
    return array_unique( $emails );
}
