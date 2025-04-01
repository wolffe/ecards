<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function ecard_options_page() {
    if ( isset( $_POST['info_settings_update'] ) ) {
        update_option( 'ecard_label', (int) sanitize_text_field( $_POST['ecard_label'] ) );
        update_option( 'ecard_columns', (int) sanitize_text_field( $_POST['ecard_columns'] ) );

        update_option( 'ecard_use_shadow', (int) sanitize_text_field( $_POST['ecard_use_shadow'] ) );
        update_option( 'ecard_use_highlight', (int) sanitize_text_field( $_POST['ecard_use_highlight'] ) );
        update_option( 'ecard_use_radius', (int) sanitize_text_field( $_POST['ecard_use_radius'] ) );
        update_option( 'ecard_color_scheme', sanitize_text_field( $_POST['ecard_color_scheme'] ) );
        update_option( 'ecard_color_accent', sanitize_text_field( $_POST['ecard_color_accent'] ) );
        update_option( 'ecard_button_color', sanitize_text_field( $_POST['ecard_button_color'] ) );
        update_option( 'ecard_button_background', sanitize_text_field( $_POST['ecard_button_background'] ) );

        // Check, unslash and sanitize
        update_option( 'ecard_user_enable', isset( $_POST['ecard_user_enable'] ) ? (int) sanitize_text_field( $_POST['ecard_user_enable'] ) : 0 );

        update_option( 'ecard_redirection', sanitize_text_field( $_POST['ecard_redirection'] ) );
        update_option( 'ecard_page_thankyou', esc_url( $_POST['ecard_page_thankyou'] ) );

        update_option( 'ecard_image_size', sanitize_text_field( $_POST['ecard_image_size'] ) );

        update_option( 'ecard_gdpr_privacy_policy_page', (int) $_POST['ecard_gdpr_privacy_policy_page'] );

        // Check, unslash and sanitize
        update_option( 'ecard_shortcode_fix', isset( $_POST['ecard_shortcode_fix'] ) ? (int) sanitize_text_field( $_POST['ecard_shortcode_fix'] ) : 0 );
        update_option( 'ecard_html_fix', isset( $_POST['ecard_html_fix'] ) ? (int) sanitize_text_field( $_POST['ecard_html_fix'] ) : 0 );

        update_option( 'ecard_use_akismet', sanitize_text_field( $_POST['ecard_use_akismet'] ) );
        update_option( 'ecard_captcha', (int) sanitize_text_field( $_POST['ecard_captcha'] ) );

        delete_option( 'ecard_dropbox_enable' );
        delete_option( 'ecard_dropbox_private' );

        delete_post_meta_by_key( '_ecards_impressions' );
        delete_post_meta_by_key( '_ecards_conversions' );

        echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Options updated successfully!', 'ecards' ) . '</p></div>';
    } elseif ( isset( $_POST['info_payment_update'] ) ) {
        update_option( 'ecard_restrictions', sanitize_text_field( $_POST['ecard_restrictions'] ) );
        update_option( 'ecard_restrictions_message', esc_html( stripslashes_deep( $_POST['ecard_restrictions_message'] ) ) );

        echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Options updated successfully!', 'ecards' ) . '</p></div>';
    } elseif ( isset( $_POST['info_designer_update'] ) ) {
        update_option( 'ecard_title', stripslashes( $_POST['ecard_title'] ) );
        update_option( 'ecard_template', wp_kses_post( $_POST['ecard_template'] ) );
        update_option( 'ecard_image_size_email', sanitize_text_field( $_POST['ecard_image_size_email'] ) );
        update_option( 'ecard_body_toggle', sanitize_text_field( $_POST['ecard_body_toggle'] ) );

        delete_option( 'ecards_reusable_block_id' );

        echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Options updated successfully!', 'ecards' ) . '</p></div>';
    } elseif ( isset( $_POST['info_email_update'] ) ) {
        update_option( 'ecard_send_behaviour', sanitize_text_field( $_POST['ecard_send_behaviour'] ) );
        update_option( 'ecard_hardcoded_email', sanitize_email( $_POST['ecard_hardcoded_email'] ) );
        update_option( 'ecard_send_later', sanitize_text_field( $_POST['ecard_send_later'] ) );

        update_option( 'ecard_allow_cc', sanitize_text_field( $_POST['ecard_allow_cc'] ) );
        update_option( 'ecard_allow_csv', (int) sanitize_text_field( $_POST['ecard_allow_csv'] ) );

        delete_option( 'ecard_noreply' );

        echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Options updated successfully!', 'ecards' ) . '</p></div>';
    } elseif ( isset( $_POST['info_labels_update'] ) ) {
        update_option( 'ecard_label_name_own', stripslashes( sanitize_text_field( $_POST['ecard_label_name_own'] ) ) );
        update_option( 'ecard_label_email_own', stripslashes( sanitize_text_field( $_POST['ecard_label_email_own'] ) ) );
        update_option( 'ecard_label_email_friend', stripslashes( sanitize_text_field( $_POST['ecard_label_email_friend'] ) ) );
        update_option( 'ecard_label_message', stripslashes( sanitize_text_field( $_POST['ecard_label_message'] ) ) );
        update_option( 'ecard_label_send_time', stripslashes( sanitize_text_field( $_POST['ecard_label_send_time'] ) ) );
        update_option( 'ecard_label_cc', stripslashes( sanitize_text_field( $_POST['ecard_label_cc'] ) ) );
        update_option( 'ecard_label_success', sanitize_text_field( $_POST['ecard_label_success'] ) );
        update_option( 'ecard_label_gdpr_privacy_policy_page', stripslashes( sanitize_text_field( $_POST['ecard_label_gdpr_privacy_policy_page'] ) ) );
        update_option( 'ecard_submit', stripslashes( sanitize_text_field( $_POST['ecard_submit'] ) ) );
        update_option( 'ecard_link_anchor', stripslashes( sanitize_text_field( $_POST['ecard_link_anchor'] ) ) );

        echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Options updated successfully!', 'ecards' ) . '</p></div>';
    } elseif ( isset( $_POST['info_debug_update'] ) ) {
        $headers[] = 'Content-Type: text/html;';

        if ( ! empty( $_POST['ecard_test_email'] ) && wp_mail( $_POST['ecard_test_email'], 'eCards test email', 'Testing eCards plugin...', $headers ) ) {
            echo '<div id="message" class="updated notice is-dismissible"><p>Mail sent successfully. Check your inbox.</p></div>';
        } else {
            echo '<div id="message" class="updated notice notice-error is-dismissible"><p>Mail not sent. Check your server configuration.</p></div>';
        }

        echo '<div id="message" class="updated notice is-dismissible"><p>Options updated successfully!</p></div>';
    }
    ?>
    <div class="wrap">
        <h2>eCards</h2>

        <?php
        $ecard_template = get_option( 'ecard_template' );

        if ( empty( $ecard_template ) ) {
            echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'You have not set an email template for eCards! <a href="' . admin_url( 'options-general.php?page=ecards&tab=designer' ) . '">Click here</a> to set it.', 'ecards' ) . '</p></div>';
        }

        $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'ecards_dashboard';
        $page_tab   = 'edit.php?post_type=ecard&page=ecard_options_page&tab=ecards_';
        ?>

        <style>.ecards-lite-icon,.ecards-pro-icon{color:#fff;padding:2px 4px;font-size:11px;text-transform:uppercase;border-radius:3px;font-weight:400;border-left:4px solid rgba(0,0,0,.25);text-shadow:none}.ecards-lite-icon{background-color:#F39C12}.ecards-pro-icon{background-color:#9B59B6}</style>

        <h2 class="nav-tab-wrapper">
            <a href="<?php echo $page_tab; ?>dashboard" class="nav-tab <?php echo $active_tab === 'ecards_dashboard' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Dashboard', 'ecards' ); ?></a>
            <a href="<?php echo $page_tab; ?>settings" class="nav-tab <?php echo $active_tab === 'ecards_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', 'ecards' ); ?></a>
            <a href="<?php echo $page_tab; ?>css" class="nav-tab <?php echo $active_tab === 'ecards_css' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Custom CSS', 'ecards' ); ?></a>
            <a href="<?php echo $page_tab; ?>designer" class="nav-tab <?php echo $active_tab === 'ecards_designer' ? 'nav-tab-active' : ''; ?>"><?php _e( 'eCard Designer', 'ecards' ); ?></a>
            <a href="<?php echo $page_tab; ?>email" class="nav-tab <?php echo $active_tab === 'ecards_email' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Email Options', 'ecards' ); ?></a>
            <a href="<?php echo $page_tab; ?>members" class="nav-tab <?php echo $active_tab === 'ecards_members' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Restrictions', 'ecards' ); ?></a>
            <a href="<?php echo $page_tab; ?>labels" class="nav-tab <?php echo $active_tab === 'ecards_labels' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Labels', 'ecards' ); ?></a>
            <a href="<?php echo $page_tab; ?>diagnostics" class="nav-tab <?php echo $active_tab === 'ecards_diagnostics' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Diagnostics', 'ecards' ); ?></a>
        </h2>

        <?php if ( $active_tab === 'ecards_dashboard' ) { ?>
            <h2>Thank you for using eCards!</h2>
            <p><a href="https://codecanyon.net/item/wordpress-ecards/reviews/1051966" rel="external noopener follow" class="button button-secondary">Rate <b>eCards</b> on CodeCanyon</a></p>

            <p>For support, feature requests and bug reporting, please visit the <a href="https://getbutterfly.com/wordpress-plugins/wordpress-ecards-plugin/" rel="external noopener follow">official website</a>. <a href="https://getbutterfly.com/support/documentation/ecards/" class="button button-secondary">eCards Documentation</a></p>

            <h2>eCard Statistics</h2>
            <div style="width: 100%; height: 400px; margin: 20px 0;">
                <canvas id="ecardsChart"></canvas>
            </div>

            <?php
            // Get eCard data for the past 90 days
            $args   = [
                'post_type'      => 'ecard',
                'posts_per_page' => -1,
                'date_query'     => [
                    [
                        'after'     => '90 days ago',
                        'inclusive' => true,
                    ],
                ],
            ];
            $ecards = get_posts( $args );

            // Prepare data for the chart
            $dates        = [];
            $counts       = [];
            $current_date = new DateTime();

            // Initialize the last 90 days with zero counts
            for ( $i = 90; $i >= 0; $i-- ) {
                $date = clone $current_date;
                $date->modify( "-{$i} days" );
                $dates[]  = $date->format( 'Y-m-d' );
                $counts[] = 0;
            }

            // Count eCards per day
            foreach ( $ecards as $ecard ) {
                $post_date = get_the_date( 'Y-m-d', $ecard->ID );
                $index     = array_search( $post_date, $dates );
                if ( $index !== false ) {
                    ++$counts[ $index ];
                }
            }
            ?>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('ecardsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode( $dates ); ?>,
                        datasets: [{
                            label: 'eCards Sent',
                            data: <?php echo json_encode( $counts ); ?>,
                            backgroundColor: '#9B59B6',
                            borderColor: '#8E44AD',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'eCards Sent Over the Past 90 Days'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
            </script>

            <h2>About eCards</h2>
            <p>
                You are using <b>eCards</b> version <b><?php echo ECARDS_VERSION; ?></b> <span class="ecards-pro-icon">PRO</span> on PHP version <?php echo PHP_VERSION; ?> and MySQL version 
                                                                    <?php
                                                                    global $wpdb;
                                                                    echo $wpdb->db_version();
                                                                    ?>
                .
                <br>&copy;<?php echo gmdate( 'Y' ); ?> <a href="https://getbutterfly.com/" rel="external"><strong>getButterfly</strong>.com</a> &middot; <small>Code wrangling since 2005</small>
            </p>

            <h3>Summary and usage examples (shortcodes and template tags)</h3>
            <p>eCards plugin uses one shortcode: <code>[ecard]</code> for all image types (JPG, PNG, GIF). Adding eCards to a post or a page is accomplished by uploading one or more images for the <code>[ecard]</code> shortcode. Images should be uploaded directly to the post or page, not from the <b>Media Library</b>. Inserting the images is not necessary, as the plugin creates the eCard automatically.</p>

            <p>
                Add the <code>[ecard]</code> shortcode to a post or a page or call the function from a template file:<br>
                <code>&lt;?php if ( function_exists( 'wp_ecard_display_ecards' ) ) { echo wp_ecard_display_ecards(); } ?&gt;</code>
            </p>

            <h3>Styling examples (CSS classes)</h3>
            <p>Use <code>.ecard-confirmation</code> class to style the confirmation message, use <code>.ecard-error</code> class to style the error message.</p>

            <p>Use <code>.ecards</code> class as a selector for lightbox plugins. Based on your plugin configuration, you can also use <code>.ecard a</code> as a selector.</p>
        <?php } elseif ( $active_tab === 'ecards_settings' ) { ?>
            <style>
            .color-well {
                font-family: monospace;
                width: 96px;
            }
            </style>

            <script>
            document.addEventListener('DOMContentLoaded', () => {
                function ecardsGetContrast(hexcolor) {
                    hexcolor = hexcolor.replace('#', '');

                    return (parseInt(hexcolor, 16) > 0xffffff / 2) ? 'black' : 'white';
                }

                function ecardsResetColorWell() {
                    this.style.backgroundColor = 'white';
                    this.style.color = 'black';
                }

                if (document.querySelector('.color-well')) {
                    [].forEach.call(document.querySelectorAll('.color-well'), function (colorWell) {
                        colorWell.style.backgroundColor = colorWell.value;
                        colorWell.style.color = ecardsGetContrast(colorWell.value);

                        colorWell.addEventListener('input', ecardsResetColorWell, false);
                        colorWell.addEventListener('click', ecardsResetColorWell, false);
                        colorWell.addEventListener('touch', ecardsResetColorWell, false);

                        colorWell.addEventListener('blur', function () {
                            this.style.backgroundColor = this.value;
                            this.style.color = ecardsGetContrast(this.value);
                        });
                    });
                }
            });
            </script>

            <form method="post" action="">
                <h3 class="title"><?php _e( 'eCards Settings', 'ecards' ); ?></h3>

                <p>eCars are displayed in a grid, as Cards with a selectable radiobox inside.</p>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ecard_label">Card Behaviour</label></th>
                            <td>
                                <select name="ecard_label" id="ecard_label">
                                    <option value="1"<?php selected( (int) get_option( 'ecard_label' ), 1 ); ?>>Use label behaviour for eCard thumbnail</option>
                                    <option value="0"<?php selected( (int) get_option( 'ecard_label' ), 0 ); ?>>Use source (large image) for eCard thumbnail</option>
                                </select>
                                <br><small>Choose what happens when users click on eCards.</small>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="ecard_columns">Card Grid</label></th>
                            <td>
                                <select name="ecard_columns" id="ecard_columns">
                                    <option value="1"<?php selected( (int) get_option( 'ecard_columns' ), 1 ); ?>>1</option>
                                    <option value="2"<?php selected( (int) get_option( 'ecard_columns' ), 2 ); ?>>2</option>
                                    <option value="3"<?php selected( (int) get_option( 'ecard_columns' ), 3 ); ?>>3</option>
                                    <option value="4"<?php selected( (int) get_option( 'ecard_columns' ), 4 ); ?>>4</option>
                                    <option value="5"<?php selected( (int) get_option( 'ecard_columns' ), 5 ); ?>>5</option>
                                    <option value="6"<?php selected( (int) get_option( 'ecard_columns' ), 6 ); ?>>6</option>
                                </select>
                                <br><small>Choose the number of eCard items to show per row (default is <code>3</code>).</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_image_size">Card image size</label></th>
                            <td>
                                <select name="ecard_image_size" id="ecard_image_size">
                                    <option value="<?php echo get_option( 'ecard_image_size' ); ?>"><?php echo get_option( 'ecard_image_size' ); ?></option>
                                    <?php
                                    $options     = get_option( 'ecard_image_size' );
                                    $thumbsize   = isset( $options['thumb_size_box_select'] ) ? esc_attr( $options['thumb_size_box_select'] ) : '';
                                    $image_sizes = ecards_return_image_sizes();

                                    foreach ( $image_sizes as $size => $atts ) {
                                        ?>
                                        <option value="<?php echo $size; ?>" <?php selected( $thumbsize, $size ); ?>><?php echo $size . ' - ' . implode( 'x', $atts ); ?></option>
                                        <?php
                                    }
                                    ?>
                                    <option value="full">full (size depends on the original image)</option>
                                </select>
                                <br><small>Add more image sizes using third-party plugins.</small>
                                <br><small><b>Note that adding custom sizes may require thumbnail regeneration.</b> We recommend the <a href="https://wordpress.org/plugins/force-regenerate-thumbnails/">Force Regenerate Thumbnails</a> plugin (free).</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_theme">Card Design</label></th>
                            <td>
                                <p>
                                    <input type="checkbox" name="ecard_use_shadow" value="1" <?php checked( 1, (int) get_option( 'ecard_use_shadow' ) ); ?>> <label>Enable card shadow</label>
                                    <br>
                                    <input type="checkbox" name="ecard_use_highlight" value="1" <?php checked( 1, (int) get_option( 'ecard_use_highlight' ) ); ?>> <label>Highlight selected card</label>
                                </p>
                                <p>
                                    <input type="number" min="0" name="ecard_use_radius" value="<?php echo (int) get_option( 'ecard_use_radius' ); ?>"> <label>Enable card border radius (px)</label>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php _e( 'Colours', 'ecards' ); ?></label></th>
                            <td>
                                <p>
                                    <select name="ecard_color_scheme" id="ecard_color_scheme">
                                        <option value="light"<?php selected( (string) get_option( 'ecard_color_scheme' ), 'light' ); ?>>Light</option>
                                        <option value="dark"<?php selected( (string) get_option( 'ecard_color_scheme' ), 'dark' ); ?>>Dark</option>
                                    </select>
                                    <label for="ecard_color_scheme">Choose the colour scheme to match your theme.</label>
                                </p>
                                <p>
                                    <input class="color-well" name="ecard_color_accent" type="text" value="<?php echo get_option( 'ecard_color_accent' ); ?>">
                                    <label for="ecard_color_accent">Choose the accent colour for your eCard cards.</label>
                                </p>
                                <p>
                                    <input class="color-well" name="ecard_button_color" type="text" value="<?php echo get_option( 'ecard_button_color' ); ?>">
                                    <label for="ecard_button_color">Choose the button text colour for your eCard form button.</label>
                                </p>
                                <p>
                                    <input class="color-well" name="ecard_button_background" type="text" value="<?php echo get_option( 'ecard_button_background' ); ?>">
                                    <label for="ecard_button_background">Choose the button background colour for your eCard form button.</label>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="ecard_use_akismet">Anti-spam settings</label></th>
                            <td>
                                <p>
                                    <select name="ecard_use_akismet" id="ecard_use_akismet">
                                        <option value="false"<?php selected( 'false', get_option( 'ecard_use_akismet' ) ); ?>>Do not use Akismet</option>
                                        <option value="true"<?php selected( 'true', get_option( 'ecard_use_akismet' ) ); ?>>Use Akismet (recommended)</option>
                                    </select>

                                    <?php
                                    if ( function_exists( 'akismet_init' ) ) {
                                        $wpcom_api_key = get_option( 'wordpress_api_key' );

                                        if ( ! empty( $wpcom_api_key ) ) {
                                            echo '<p><small>Your Akismet plugin is installed and working properly. Your API key is <code>' . $wpcom_api_key . '</code>.</small></p>';
                                        } else {
                                            echo '<p><small>Your Akismet plugin is installed but no API key is present. Please fix it.</small></p>';
                                        }
                                    } else {
                                        echo '<p><small>You need Akismet in order to send eCards. Please install/activate it.</small></p>';
                                    }
                                    ?>
                                </p>
                                <p>
                                    <select name="ecard_captcha" id="ecard_captcha">
                                        <option value="0"<?php selected( 0, (int) get_option( 'ecard_captcha' ) ); ?>>Disable CAPTCHA</option>
                                        <option value="1"<?php selected( 1, (int) get_option( 'ecard_captcha' ) ); ?>>Enable CAPTCHA</option>
                                    </select>
                                    <br><small>Enable a simple CAPTCHA to prevent spam submissions.</small>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_user_enable">User upload settings</label></th>
                            <td>
                                <p>
                                    <input type="checkbox" name="ecard_user_enable" value="1" <?php checked( 1, (int) get_option( 'ecard_user_enable' ) ); ?>> <label>Enable user upload</label>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_redirection">Redirection settings</label></th>
                            <td>
                                <select name="ecard_redirection">
                                    <option value="0"<?php selected( 0, (int) get_option( 'ecard_redirection' ) ); ?>>Do not redirect to another page</option>
                                    <option value="1"<?php selected( 1, (int) get_option( 'ecard_redirection' ) ); ?>>Redirect to another page (see below)</option>
                                </select>
                                <br>
                                <input name="ecard_page_thankyou" id="ecard_page_thankyou" type="url" class="regular-text" value="<?php echo get_option( 'ecard_page_thankyou' ); ?>" placeholder="https://"> <label for="ecard_page_thankyou">Page to redirect to</label>
                                <br><small>Use these options to customize your success actions and/or redirect to a &quot;Thank You&quot; page.</small>
                            </td>
                        </tr>

                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                            <th scope="row"><label>GDPR/Privacy Policy</label></th>
                            <td>
                                <p>
                                    <?php
                                    if ( ! get_option( 'ecard_gdpr_privacy_policy_page' ) ) {
                                        $privacy_policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
                                    } else {
                                        $privacy_policy_page_id = (int) get_option( 'ecard_gdpr_privacy_policy_page' );
                                    }

                                    wp_dropdown_pages(
                                        [
                                            'name'     => 'ecard_gdpr_privacy_policy_page',
                                            'selected' => $privacy_policy_page_id,
                                            'show_option_none' => 'Select a page...',
                                        ]
                                    );
                                    ?>
                                    <br><small>This is the GDPR/Privacy Policy page. Select a page to display a consent box below your eCard form. Change your label in <a href="<?php echo admin_url( 'edit.php?post_type=ecard&page=ecard_options_page&tab=ecards_labels' ); ?>">Labels</a>.</small>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>

                        <tr>
                            <th scope="row"><label>Debugging<br><small>(developers only)</small></label></th>
                            <td>
                                <p>
                                    <input name="ecard_shortcode_fix" id="ecard_shortcode_fix" type="checkbox"<?php checked( 'on', get_option( 'ecard_shortcode_fix' ) ); ?>> <label for="ecard_shortcode_fix">Apply content shortcode fix</label>
                                    <br><small>Only use this option if your WordPress version is old, or you have a buggy theme and the shortcode is not working.</small>
                                </p>
                                <p>
                                    <input name="ecard_html_fix" id="ecard_html_fix" type="checkbox"<?php checked( 'on', get_option( 'ecard_html_fix' ) ); ?>> <label for="ecard_html_fix">Apply HTML content type fix</label>
                                    <br><small>Only use this option if your emails are missing formatting and line breaks.</small>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>

                <p><input type="submit" name="info_settings_update" class="button button-primary" value="Save Changes"></p>
            </form>
            <?php
        } elseif ( $active_tab === 'ecards_css' ) {
            if ( isset( $_POST['ecards_save'] ) ) {
                update_option( 'ecards_custom_css', $_POST['ecards_custom_css'] );

                echo '<div class="updated notice is-dismissible"><p>Settings updated successfully!</p></div>';
            }
            ?>
            <h3><?php esc_html_e( 'Custom CSS', 'ecards' ); ?></h3>

            <p>Add your own CSS code here to customise the appearance and layout of your cards.</p>

            <style>
            .ecards-code {
                font-family: SFMono-Regular, Consolas, "Liberation Mono", Menlo, monospace;

                background-color: #1a2023;
                border: 0 none;
                color: #ecf0f1;
                word-wrap: normal;
                white-space: pre;
            }
            </style>

            <form method="post" action="">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label>Custom CSS Rules</label></th>
                            <td>
                                <p>
                                    <textarea name="ecards_custom_css" id="ecards_custom_css" class="large-text code ecards-code" rows="32" spellcheck="false" autocomplete="off"><?php echo stripslashes( get_option( 'ecards_custom_css' ) ); ?></textarea>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>

                <p><input type="submit" name="ecards_save" value="Save Changes" class="button-primary"></p>
            </form>
            <?php
        } elseif ( $active_tab === 'ecards_members' ) {
            ?>
            <form method="post" action="">
                <h3 class="title"><?php _e( 'eCards Restrictions', 'ecards' ); ?></h3>
                <p>Restricting access to members only requires a user to be logged into your WordPress site.</p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ecard_restrictions">Member restrictions</label></th>
                            <td>
                                <p>
                                    <select name="ecard_restrictions">
                                        <option value="0"<?php selected( 0, (int) get_option( 'ecard_restrictions' ) ); ?>>Do not restrict access to eCard form</option>
                                        <option value="1"<?php selected( 1, (int) get_option( 'ecard_restrictions' ) ); ?>>Restrict access to members only</option>
                                    </select> <label for="ecard_restrictions_message">Add a guest message below, if you restrict access to members only.</label>
                                </p>
                                <p>
                                    <textarea rows="8" autocomplete="off" name="ecard_restrictions_message" id="ecard_restrictions_message" class="large-text"><?php echo get_option( 'ecard_restrictions_message' ); ?></textarea>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>

                <p><input type="submit" name="info_payment_update" class="button button-primary" value="Save Changes"></p>
            </form>
            <?php
        } elseif ( $active_tab === 'ecards_designer' ) {
            ?>
            <form method="post" action="">
                <h3 class="title"><?php _e( 'eCard Designer', 'ecards' ); ?></h3>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ecard_title">Email subject</label></th>
                            <td>
                                <input name="ecard_title" id="ecard_title" type="text" class="regular-text" value="<?php echo get_option( 'ecard_title' ); ?>">
                                <br><small>This is the subject of the eCard email.</small>
                                <br><small>Use <code class="codor">[name]</code> and <code class="codor">[email]</code> shortcodes to replace sender's name and email address (e.g. "You have received an eCard from [name]").</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_image_size_email">eCard image size<br><small>(email only)</small></label></th>
                            <td>
                                <?php $image_sizes = get_intermediate_image_sizes(); ?>
                                <select name="ecard_image_size_email" id="ecard_image_size_email">
                                    <option value="<?php echo get_option( 'ecard_image_size_email' ); ?>"><?php echo get_option( 'ecard_image_size_email' ); ?></option>
                                    <?php
                                    $options     = get_option( 'ecard_image_size_email' );
                                    $thumbsize   = isset( $options['thumb_size_box_select'] ) ? esc_attr( $options['thumb_size_box_select'] ) : '';
                                    $image_sizes = ecards_return_image_sizes();

                                    foreach ( $image_sizes as $size => $atts ) {
                                        ?>
                                        <option value="<?php echo $size; ?>" <?php selected( $thumbsize, $size ); ?>><?php echo $size . ' - ' . implode( 'x', $atts ); ?></option>
                                    <?php } ?>
                                    <option value="full">full (size depends on the original image)</option>
                                </select>
                                <br><small>We recommend a width no wider than 600px for maximum email client compatibility.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_body_toggle">Message area</label></th>
                            <td>
                                <select name="ecard_body_toggle" id="ecard_body_toggle">
                                    <option value="1"<?php selected( (int) get_option( 'ecard_body_toggle' ), 1 ); ?>>Show message area (default)</option>
                                    <option value="0"<?php selected( (int) get_option( 'ecard_body_toggle' ), 0 ); ?>>Hide message area</option>
                                </select>
                                <br><small>Show or hide the message textarea. Use it for &quot;Tip a friend&quot; type email message.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_template">Email template</label></th>
                            <td>
                                <?php
                                $ecard_template = get_option( 'ecard_template' );
                                wp_editor(
                                    $ecard_template,
                                    'ecard_template',
                                    [
                                        'textarea_name' => 'ecard_template',
                                        'textarea_rows' => 20,
                                        //'media_buttons' => false,
                                        //'teeny' => true,
                                        'quicktags'     => true,
                                    ]
                                );
                                ?>
                                <br><small>Use <code class="codor">[name]</code> and <code class="codor">[email]</code> Designer tags to replace sender's name and email address.</small>
                                <br><small>Use <code class="codor">[image]</code> Designer tag to add the eCard image.</small>
                                <br><small>Use <code class="codor">[ecard-link]</code> Designer tag to include the eCard URL.</small>
                                <br><small>Use <code class="codor">[ecard-url]</code> Designer tag to include the eCard direct URL (JPG, PNG, GIF).</small>
                                <br><small>Use <code class="codor">[ecard-message]</code> Designer tag to include the eCard message.</small>
                                <br><small>Use <code class="codor">[ecard-content]</code> Designer tag to include the post/page content. Useful if you have a certain eCard &quot;story&quot; or message you want to convey.</small>
                                <br>
                                <br><small>Check the <a href="https://getbutterfly.com/support/documentation/ecards/#ecard-designer" rel="external">documentation section</a> for more Designer samples.</small>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <p><input type="submit" name="info_designer_update" class="button button-primary" value="Save Changes"></p>
            </form>
        <?php } elseif ( $active_tab === 'ecards_email' ) { ?>
            <form method="post" action="">
                <h3 class="title"><?php _e( 'Email Settings', 'ecards' ); ?></h3>
                <p><b>Note:</b> To avoid your email address being marked as spam, it is highly recommended that your "from" domain match your website. Some hosts may require that your "from" address be a legitimate address.</p>
                <p>Emails sometimes end up in your spam (or junk) folder. Sometimes they don't arrive at all. While the latter may indicate a server issue, the former may easily be fixed by setting up a dedicated email address.</p>

                <p>If your host blocks the <code>mail()</code> function, or if you notice errors or restrictions, configure your WordPress site to use SMTP. We recommend <a href="https://wordpress.org/plugins/post-smtp/" rel="external">Post SMTP Mailer/Email Log</a>.</p>
 
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ecard_send_behaviour">Sending behaviour</label></th>
                            <td>
                                <select name="ecard_send_behaviour">
                                    <option value="1"
                                    <?php
                                    if ( get_option( 'ecard_send_behaviour' ) === '1' ) {
                                        echo ' selected';}
                                    ?>
                                    >Require recipient email address</option>
                                    <option value="0"
                                    <?php
                                    if ( get_option( 'ecard_send_behaviour' ) === '0' ) {
                                        echo ' selected';}
                                    ?>
                                    >Hide recipient and send all eCards to the following email address</option>
                                </select>
                                <br>&lfloor; <input name="ecard_hardcoded_email" type="email" class="regular-text" value="<?php echo get_option( 'ecard_hardcoded_email' ); ?>">
                                <br><small>If you want to send all eCards to a universal email address, select the option above and fill in the email address.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_send_later">"Send Later" behaviour</label></th>
                            <td>
                                <select name="ecard_send_later">
                                    <option value="1"
                                    <?php
                                    if ( get_option( 'ecard_send_later' ) === '1' ) {
                                        echo ' selected';}
                                    ?>
                                    >Allow eCard scheduling</option>
                                    <option value="0"
                                    <?php
                                    if ( get_option( 'ecard_send_later' ) === '0' ) {
                                        echo ' selected';}
                                    ?>
                                    >Do not allow eCard scheduling</option>
                                </select>
                                <br><small>Allow users to pick a later date and time to send the eCard. The plugin uses the server time - <b><code><?php echo get_option( 'timezone_string' ); ?></code></b> - for post scheduling.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_allow_cc">Carbon copy (CC)</label></th>
                            <td>
                                <select name="ecard_allow_cc" id="ecard_allow_cc">
                                    <option value="on"
                                    <?php
                                    if ( get_option( 'ecard_allow_cc' ) === 'on' ) {
                                        echo ' selected';}
                                    ?>
                                    >Allow sender to CC self</option>
                                    <option value="off"
                                    <?php
                                    if ( get_option( 'ecard_allow_cc' ) === 'off' ) {
                                        echo ' selected';}
                                    ?>
                                    >Do not allow sender to CC self</option>
                                </select>
                                <br><small>Display a checkbox to allow the sender to CC self</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_allow_csv"><?php _e( 'CSV File Upload', 'ecards' ); ?></label></th>
                            <td>
                                <p>
                                    <input type="checkbox" name="ecard_allow_csv" id="ecard_allow_csv" value="1" <?php checked( 1, (int) get_option( 'ecard_allow_csv' ) ); ?>> 
                                    <label for="ecard_allow_csv"><?php _e( 'Enable CSV file upload', 'ecards' ); ?></label>
                                </p>
                                <small><?php _e( 'Allow users to upload CSV files containing email addresses', 'ecards' ); ?></small>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <p><input type="submit" name="info_email_update" class="button button-primary" value="Save Changes"></p>
            </form>
        <?php } elseif ( $active_tab === 'ecards_labels' ) { ?>
            <form method="post" action="">
                <h3 class="title"><?php _e( 'Labels', 'ecards' ); ?></h3>
                <p>Use the labels to personalize or translate your eCards form.</p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ecard_label_name_own">Your name<br><small>(input label)</small></label></th>
                            <td>
                                <input name="ecard_label_name_own" id="ecard_label_name_own" type="text" class="regular-text" value="<?php echo get_option( 'ecard_label_name_own' ); ?>">
                                <br><small>Default is "Your name"</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_label_email_own">Your email address<br><small>(input label)</small></label></th>
                            <td>
                                <input name="ecard_label_email_own" id="ecard_label_email_own" type="text" class="regular-text" value="<?php echo get_option( 'ecard_label_email_own' ); ?>">
                                <br><small>Default is "Your email address"</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_label_email_friend">Recipient Email<br><small>(input label)</small></label></th>
                            <td>
                                <input name="ecard_label_email_friend" id="ecard_label_email_friend" type="text" class="regular-text" value="<?php echo get_option( 'ecard_label_email_friend' ); ?>">
                                <br><small>Default is "Recipient Email"</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_label_message">eCard message<br><small>(textarea label)</small></label></th>
                            <td>
                                <input name="ecard_label_message" id="ecard_label_message" type="text" class="regular-text" value="<?php echo get_option( 'ecard_label_message' ); ?>">
                                <br><small>Default is "eCard message"</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_label_send_time">eCard send date/time<br><small>(date/time picker label)</small></label></th>
                            <td>
                                <input name="ecard_label_send_time" id="ecard_label_send_time" type="text" class="regular-text" value="<?php echo get_option( 'ecard_label_send_time' ); ?>">
                                <br><small>Default is "Schedule this eCard"</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_label_cc">Send a copy to self<br><small>(checkbox label)</small></label></th>
                            <td>
                                <input name="ecard_label_cc" id="ecard_label_cc" type="text" class="regular-text" value="<?php echo get_option( 'ecard_label_cc' ); ?>">
                                <br><small>Default is "Send a copy to self"</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_label_success">Success message<br><small>(paragraph)</small></label></th>
                            <td>
                                <input name="ecard_label_success" id="ecard_label_success" type="text" class="regular-text" value="<?php echo get_option( 'ecard_label_success' ); ?>">
                                <br><small>Default is "eCard sent successfully!"</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_submit">eCard submit<br><small>(button label)</small></label></th>
                            <td>
                                <input id="ecard_submit" name="ecard_submit" type="text" class="regular-text" value="<?php echo get_option( 'ecard_submit' ); ?>">
                                <br><small>Default is "Send eCard"</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_link_anchor">Email link anchor<br><small>(link)</small></label></th>
                            <td>
                                <input name="ecard_link_anchor" name="ecard_link_anchor" type="text" class="regular-text" value="<?php echo get_option( 'ecard_link_anchor' ); ?>">
                                <br><small>Default is "Click to see your eCard!"</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_label_gdpr_privacy_policy_page">Privacy Policy/GDPR consent text<br><small>(checkbox label)</small></label></th>
                            <td>
                                <input name="ecard_label_gdpr_privacy_policy_page" name="ecard_label_gdpr_privacy_policy_page" type="text" class="regular-text" value="<?php echo get_option( 'ecard_label_gdpr_privacy_policy_page' ); ?>">
                                <br><small>Default is "Check here to indicate that you have read and agree to our terms and conditions"</small>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <p><input type="submit" name="info_labels_update" class="button button-primary" value="Save Changes"></p>
            </form>
        <?php } elseif ( $active_tab === 'ecards_diagnostics' ) { ?>
            <form method="post" action="">
                <h3 class="title"><?php _e( 'Diagnostics', 'ecards' ); ?></h3>
                <p>If your host blocks the <code>mail()</code> function, or if you notice errors or restrictions, configure your WordPress site to use SMTP. We recommend <a href="https://wordpress.org/plugins/post-smtp/" rel="external">Post SMTP Mailer/Email Log</a>.</p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ecard_test_email"><?php _e( 'Test <code>wp_mail()</code> function', 'ecards' ); ?></label></th>
                            <td>
                                <input name="ecard_test_email" id="ecard_test_email" type="email" class="regular-text" value="<?php echo get_option( 'admin_email' ); ?>">
                                <br><small><?php _e( 'Use this address to send a test email message.', 'ecards' ); ?></small>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <p><input type="submit" name="info_debug_update" class="button button-primary" value="<?php _e( 'Send Email', 'ecards' ); ?>"></p>
            </form>
        <?php } ?>
    </div>
    <?php
}
