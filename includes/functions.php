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

function ecard_is_valid_image_upload( $field ) {
    $file = $_FILES[ $field ] ?? null;

    if ( ! is_array( $file ) || (int) ( $file['error'] ?? UPLOAD_ERR_NO_FILE ) !== UPLOAD_ERR_OK || ! is_uploaded_file( $file['tmp_name'] ) ) {
        return false;
    }

    $check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );

    return in_array( $check['type'], [ 'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif' ], true );
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

    if ( ! empty( $ecard_to ) ) {
        foreach ( (array) $ecard_to as $recipient ) {
            ecard_mail( $recipient, $subject, $ecard_email_message, $headers );
        }
    }

    $ecard_email_cc = get_post_meta( $ecard_id, 'ecard_email_cc', true );
    if ( ! empty( $ecard_email_cc ) ) {
        ecard_mail( $ecard_email_from, $subject, $ecard_email_message, $headers );
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
        <input type="text" style="width: 100%;" value="<?php echo esc_attr( $ecard_name ); ?>" readonly>
    </p>
    <p>
        <input type="email" style="width: 100%;" value="<?php echo esc_attr( $ecard_email_sender ); ?>" readonly>
    </p>
    <p>
        <input type="text" style="width: 100%;" value="<?php echo esc_attr( implode( ', ', (array) $ecard_email_recipient ) ); ?>" readonly>
    </p>
    <p>
        <textarea style="width: 100%;" rows="6" readonly><?php echo esc_textarea( $ecard_content ); ?></textarea>
    </p>

    <?php
}

/**
 * Delete sent eCard records older than the retention setting (0 keeps them forever).
 */
function ecard_cleanup_old_records() {
    $days = (int) get_option( 'ecard_retention_days', 0 );

    if ( $days < 1 ) {
        return;
    }

    $old = get_posts(
        [
            'post_type'      => 'ecard',
            'post_status'    => [ 'private', 'publish' ],
            'fields'         => 'ids',
            'posts_per_page' => 200,
            'no_found_rows'  => true,
            'date_query'     => [ [ 'before' => $days . ' days ago' ] ],
        ]
    );

    foreach ( $old as $ecard_id ) {
        wp_delete_post( $ecard_id, true );
    }
}
add_action( 'ecard_cleanup', 'ecard_cleanup_old_records' );

function ecard_schedule_cleanup() {
    if ( ! wp_next_scheduled( 'ecard_cleanup' ) ) {
        wp_schedule_event( time(), 'daily', 'ecard_cleanup' );
    }
}
add_action( 'init', 'ecard_schedule_cleanup' );

/*
 * Shared with eCards Lite (keep both copies identical apart from the text domain).
 */

/**
 * Honeypot field: hidden from people and assistive tech, filled in by most bots.
 */
function ecard_honeypot_field() {
    return '<p class="ecard-hp" aria-hidden="true" style="position:absolute;left:-9999px"><label for="ecard_website">Website</label><input type="text" id="ecard_website" name="ecard_website" tabindex="-1" autocomplete="off"></p>';
}

/**
 * Honeypot and per-IP rate limit (filter `ecard_rate_limit`, default 10 cards per hour).
 */
function ecard_is_spam_request() {
    if ( ! empty( $_POST['ecard_website'] ) ) {
        return true;
    }

    $ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP ) : '';
    $key   = 'ecard_rate_' . md5( $ip . wp_salt() );
    $count = (int) get_transient( $key );

    if ( $count >= (int) apply_filters( 'ecard_rate_limit', 10 ) ) {
        return true;
    }

    set_transient( $key, $count + 1, HOUR_IN_SECONDS );

    return false;
}

/**
 * wp_mail() with a plain-text alternative part, which helps deliverability and text-only clients.
 */
function ecard_mail( $to, $subject, $html, $headers ) {
    $alt = static function ( $mailer ) use ( $html ) {
        $text            = preg_replace( [ '~<br\s*/?>~i', '~</(p|div|tr|h[1-6])>~i' ], [ "\n", "\n\n" ], $html );
        $mailer->AltBody = trim( html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES ) );
    };

    add_action( 'phpmailer_init', $alt );
    $sent = wp_mail( $to, $subject, $html, $headers );
    remove_action( 'phpmailer_init', $alt );

    return $sent;
}

/**
 * Admin shortcode generator: pick images from the Media Library, drag to reorder, copy [ecard id="..."].
 */
function ecard_shortcode_generator() {
    wp_enqueue_media();

    $themes = '';

    foreach ( ecard_themes() as $key => $label ) {
        $themes .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $key === '' ? __( 'Default (from Email settings)', 'ecards' ) : $label ) . '</option>';
    }

    return '<h3>' . esc_html__( 'Shortcode Generator', 'ecards' ) . '</h3>
    <p>' . esc_html__( 'Choose the card images, drag them into the order you want, then copy the shortcode into any post or page.', 'ecards' ) . '</p>
    <p><button type="button" class="button button-primary" id="ecard-gen-pick">' . esc_html__( 'Choose images', 'ecards' ) . '</button></p>
    <ul id="ecard-gen-list" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;margin:0 0 16px;padding:0" aria-label="' . esc_attr__( 'Selected images (drag to reorder)', 'ecards' ) . '"></ul>
    <p><label for="ecard-gen-theme">' . esc_html__( 'Email theme', 'ecards' ) . '</label><br><select id="ecard-gen-theme">' . $themes . '</select></p>
    <p><label for="ecard-gen-output">' . esc_html__( 'Shortcode', 'ecards' ) . '</label><br><input type="text" id="ecard-gen-output" class="large-text code" value="[ecard]" readonly></p>
    <p><button type="button" class="button" id="ecard-gen-copy">' . esc_html__( 'Copy shortcode', 'ecards' ) . '</button> <span id="ecard-gen-status" role="status"></span></p>
    <script type="module">
        const list = document.getElementById("ecard-gen-list");
        const output = document.getElementById("ecard-gen-output");
        const theme = document.getElementById("ecard-gen-theme");
        let dragged;

        const build = () => {
            const ids = [...list.children].map((item) => item.dataset.id);
            output.value = `[ecard${ids.length ? ` id="${ids.join(",")}"` : ""}${theme.value ? ` theme="${theme.value}"` : ""}]`;
        };

        const frame = wp.media({ title: "' . esc_js( __( 'Choose eCard images', 'ecards' ) ) . '", library: { type: "image" }, multiple: "add" });

        frame.on("select", () => {
            list.replaceChildren(...frame.state().get("selection").map((attachment) => {
                const { id, alt, sizes, url } = attachment.toJSON();
                const item = Object.assign(document.createElement("li"), { draggable: true, tabIndex: 0 });

                item.dataset.id = id;
                item.style.cursor = "move";
                item.append(Object.assign(document.createElement("img"), { src: sizes?.thumbnail?.url ?? url, alt: alt || `#${id}`, width: 100, height: 100 }));
                return item;
            }));
            build();
        });

        list.addEventListener("dragstart", (event) => { dragged = event.target.closest("li"); });
        list.addEventListener("dragover", (event) => {
            event.preventDefault();
            const target = event.target.closest("li");

            if (target && target !== dragged) {
                target.before(dragged);
            }
        });
        list.addEventListener("drop", (event) => {
            event.preventDefault();
            build();
        });
        list.addEventListener("keydown", (event) => {
            const item = event.target.closest("li");

            if (event.key === "ArrowLeft" && item?.previousElementSibling) {
                item.previousElementSibling.before(item);
            } else if (event.key === "ArrowRight" && item?.nextElementSibling) {
                item.nextElementSibling.after(item);
            } else {
                return;
            }

            event.preventDefault();
            item.focus();
            build();
        });

        theme.addEventListener("change", build);
        document.getElementById("ecard-gen-pick").addEventListener("click", () => frame.open());
        document.getElementById("ecard-gen-copy").addEventListener("click", async () => {
            await navigator.clipboard.writeText(output.value);
            document.getElementById("ecard-gen-status").textContent = "' . esc_js( __( 'Copied!', 'ecards' ) ) . '";
        });
    </script>
    <p class="description">' . esc_html__( 'Tip: focus an image and use the left and right arrow keys to move it.', 'ecards' ) . '</p>';
}

function ecard_themes() {
    return [
        ''          => __( 'None', 'ecards' ),
        'christmas' => __( 'Christmas', 'ecards' ),
        'new-year'  => __( 'New Year', 'ecards' ),
        'birthday'  => __( 'Birthday', 'ecards' ),
        'thank-you' => __( 'Thank you', 'ecards' ),
    ];
}

/**
 * Wrap the email body in an email-safe (table, inline styles) seasonal frame.
 */
function ecard_theme_wrap( $html, $theme ) {
    $palette = [
        'christmas' => [ '#b3001b', '#fff8f0', '&#127876;' ],
        'new-year'  => [ '#1f2a44', '#fdf6e3', '&#10024;' ],
        'birthday'  => [ '#d6336c', '#fff0f6', '&#127874;' ],
        'thank-you' => [ '#2b8a3e', '#f4fbf5', '&#128144;' ],
    ];

    if ( ! isset( $palette[ $theme ] ) ) {
        return $html;
    }

    [ $accent, $background, $emoji ] = $palette[ $theme ];

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $background . ';"><tr><td align="center" style="padding:24px 12px;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-top:6px solid ' . $accent . ';border-radius:8px;"><tr><td style="padding:24px;font-family:Georgia,serif;font-size:16px;line-height:1.6;color:#222222;">'
        . '<p style="margin:0 0 16px;font-size:32px;line-height:1;text-align:center;">' . $emoji . '</p>' . $html
        . '</td></tr></table></td></tr></table>';
}
