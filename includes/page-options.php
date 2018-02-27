<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

function ecard_options_page() {
    $ecard_counter = get_option('ecard_counter');

    if(isset($_POST['info_settings_update'])) {
        update_option('ecard_label', sanitize_text_field($_POST['ecard_label']));

        update_option('ecard_dropbox_private', sanitize_text_field($_POST['ecard_dropbox_private']));

        update_option('ecard_user_enable', sanitize_text_field($_POST['ecard_user_enable']));
        update_option('ecard_dropbox_enable', sanitize_text_field($_POST['ecard_dropbox_enable']));

        update_option('ecard_redirection', sanitize_text_field($_POST['ecard_redirection']));
        update_option('ecard_page_thankyou', esc_url($_POST['ecard_page_thankyou']));

        update_option('ecard_image_size', sanitize_text_field($_POST['ecard_image_size']));

        update_option('ecard_shortcode_fix', sanitize_text_field($_POST['ecard_shortcode_fix']));
        update_option('ecard_html_fix', sanitize_text_field($_POST['ecard_html_fix']));
        update_option('ecard_show_menu_ui', sanitize_text_field($_POST['ecard_show_menu_ui']));

        update_option('ecard_use_akismet', sanitize_text_field($_POST['ecard_use_akismet']));
        update_option('ecard_post_create_status', sanitize_text_field($_POST['ecard_post_create_status']));
        update_option('ecard_user_create', sanitize_text_field($_POST['ecard_user_create']));

        echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Options updated successfully!', 'ecards') . '</p></div>';
    }
    if(isset($_POST['info_payment_update'])) {
        update_option('ecard_restrictions', sanitize_text_field($_POST['ecard_restrictions']));
        update_option('ecard_restrictions_message', esc_html(stripslashes_deep($_POST['ecard_restrictions_message'])));

        update_option('p2v_paypal_sandbox', sanitize_text_field($_POST['p2v_paypal_sandbox']));
        update_option('p2v_who', sanitize_text_field($_POST['p2v_who']));
        update_option('p2v_paypal_email', sanitize_email($_POST['p2v_paypal_email']));
        update_option('p2v_paypal_currency', sanitize_text_field($_POST['p2v_paypal_currency']));
        update_option('p2v_paypal_default_amount', sanitize_text_field($_POST['p2v_paypal_default_amount']));
        update_option('p2v_paypal_button', esc_url($_POST['p2v_paypal_button']));

        update_option('p2v_ipn', esc_url($_POST['p2v_ipn']));
        update_option('p2v_continue', esc_url($_POST['p2v_continue']));
        update_option('p2v_cancel', esc_url($_POST['p2v_cancel']));
        update_option('p2v_style', sanitize_text_field($_POST['p2v_style'])); 
        update_option('p2v_lc', sanitize_text_field($_POST['p2v_lc']));
        update_option('p2v_shipping', sanitize_text_field($_POST['p2v_shipping']));
        update_option('p2v_cbt', sanitize_text_field($_POST['p2v_cbt']));

        echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Options updated successfully!', 'ecards') . '</p></div>';
    }
    if (isset($_POST['info_designer_update'])) {
        update_option('ecard_title', stripslashes($_POST['ecard_title']));
        update_option('ecard_template', stripslashes($_POST['ecard_template']));
        update_option('ecard_image_size_email', sanitize_text_field($_POST['ecard_image_size_email']));
        update_option('ecard_body_toggle', sanitize_text_field($_POST['ecard_body_toggle']));
    }
    if(isset($_POST['info_email_update'])) {
        update_option('ecard_noreply', sanitize_email($_POST['ecard_noreply']));

        update_option('ecard_send_behaviour', sanitize_text_field($_POST['ecard_send_behaviour']));
        update_option('ecard_hardcoded_email', sanitize_email($_POST['ecard_hardcoded_email']));
        update_option('ecard_send_later', sanitize_text_field($_POST['ecard_send_later']));

        update_option('ecard_allow_cc', sanitize_text_field($_POST["ecard_allow_cc"]));

        echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Options updated successfully!', 'ecards') . '</p></div>';
    }
    if(isset($_POST['info_labels_update'])) {
        update_option('ecard_label_name_own', stripslashes(sanitize_text_field($_POST['ecard_label_name_own'])));
        update_option('ecard_label_email_own', stripslashes(sanitize_text_field($_POST['ecard_label_email_own'])));
        update_option('ecard_label_email_friend', stripslashes(sanitize_text_field($_POST['ecard_label_email_friend'])));
        update_option('ecard_label_message', stripslashes(sanitize_text_field($_POST['ecard_label_message'])));
        update_option('ecard_label_send_time', stripslashes(sanitize_text_field($_POST['ecard_label_send_time'])));
        update_option('ecard_label_cc', stripslashes(sanitize_text_field($_POST['ecard_label_cc'])));
        update_option('ecard_label_success', sanitize_text_field($_POST['ecard_label_success']));
        update_option('ecard_submit', stripslashes(sanitize_text_field($_POST['ecard_submit'])));
        update_option('ecard_link_anchor', stripslashes(sanitize_text_field($_POST['ecard_link_anchor'])));

        echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Options updated successfully!', 'ecards') . '</p></div>';
    }
    if(isset($_POST['info_appearance_update'])) {
        update_option('ecard_use_display', sanitize_text_field($_POST['ecard_use_display']));

        echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Options updated successfully!', 'ecards') . '</p></div>';
    }
    if (isset($_POST['info_debug_update'])) {
        $headers = '';
        $headers[] = "Content-Type: text/html;";

        if (!empty($_POST['ecard_test_email']) && wp_mail($_POST['ecard_test_email'], 'eCards test email', 'Testing eCards plugin...', $headers)) {
            echo '<div id="message" class="updated notice is-dismissible"><p>Mail sent successfully. Check your inbox.</p></div>';
        } else {
            echo '<div id="message" class="updated notice notice-error is-dismissible"><p>Mail not sent. Check your server configuration.</p></div>';
        }

        echo '<div id="message" class="updated notice is-dismissible"><p>Options updated successfully!</p></div>';
    } ?>
    <style>
    .ecards-lite-icon { background-color: #F39C12; color: #ffffff; padding: 2px 4px; font-size: 11px; text-transform: uppercase; border-radius: 3px; font-weight: 400; border-left: 4px solid rgba(0, 0, 0, 0.25); text-shadow: none; }
    .ecards-pro-icon { background-color: #9B59B6; color: #ffffff; padding: 2px 4px; font-size: 11px; text-transform: uppercase; border-radius: 3px; font-weight: 400; border-left: 4px solid rgba(0, 0, 0, 0.25); text-shadow: none; }
    .ecards-pro-label { opacity: 0.75; cursor: not-allowed !important; }
    </style>
    <div class="wrap">
		<h2>eCards</h2>

		<?php
        $ecard_noreply = get_option('ecard_noreply');
        $ecard_template = get_option('ecard_template');
        if (empty($ecard_noreply)) {
            echo '<div id="message" class="error notice is-dismissible"><p>' . esc_html__('You have not set a dedicated email address for eCards! <a href="' . admin_url('options-general.php?page=ecards&tab=ecards_email') . '">Click here</a> to set it.', 'ecards') . '</p></div>';
        }
        if (empty($ecard_template)) {
            echo '<div id="message" class="error notice is-dismissible"><p>' . esc_html__('You have not set an email template for eCards! <a href="' . admin_url('options-general.php?page=ecards&tab=designer') . '">Click here</a> to set it.', 'ecards') . '</p></div>';
        }

		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'ecards_dashboard';
		if(isset($_GET['tab']))
			$active_tab = $_GET['tab'];
		?>
		<h2 class="nav-tab-wrapper">
			<a href="?page=ecards&amp;tab=ecards_dashboard" class="nav-tab <?php echo $active_tab === 'ecards_dashboard' ? 'nav-tab-active' : ''; ?>"><?php _e('Dashboard', 'ecards'); ?></a>
			<a href="?page=ecards&amp;tab=ecards_settings" class="nav-tab <?php echo $active_tab === 'ecards_settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', 'ecards'); ?></a>
			<a href="?page=ecards&amp;tab=ecards_appearance" class="nav-tab <?php echo $active_tab === 'ecards_appearance' ? 'nav-tab-active' : ''; ?>"><?php _e('Appearance', 'ecards'); ?></a>
			<a href="?page=ecards&amp;tab=designer" class="nav-tab <?php echo $active_tab === 'designer' ? 'nav-tab-active' : ''; ?>"><?php _e('eCard Designer', 'ecards'); ?></a>
			<a href="?page=ecards&amp;tab=ecards_email" class="nav-tab <?php echo $active_tab === 'ecards_email' ? 'nav-tab-active' : ''; ?>"><?php _e('Email Options', 'ecards'); ?></a>
			<a href="?page=ecards&amp;tab=ecards_payment" class="nav-tab <?php echo $active_tab === 'ecards_payment' ? 'nav-tab-active' : ''; ?>"><?php _e('Restrictions &amp; Payment', 'ecards'); ?></a>
			<a href="?page=ecards&amp;tab=ecards_labels" class="nav-tab <?php echo $active_tab === 'ecards_labels' ? 'nav-tab-active' : ''; ?>"><?php _e('Labels', 'ecards'); ?></a>
			<a href="?page=ecards&amp;tab=ecards_diagnostics" class="nav-tab <?php echo $active_tab === 'ecards_diagnostics' ? 'nav-tab-active' : ''; ?>"><?php _e('Diagnostics', 'ecards'); ?></a>
		</h2>
		<?php if($active_tab === 'ecards_dashboard') { ?>
            <?php
            echo '<div id="gb-ad-ecards">
                <div id="gb-ad-content">
                    <div class="inside">
                        <p><strong>Thank you for using eCards!</strong></p>
                        <p>If you enjoy this plugin, do not forget to <a href="https://codecanyon.net/item/wordpress-ecards/1051966" rel="external">rate it on CodeCanyon</a>! We work hard to update it, fix bugs, add new features and make it compatible with the latest web technologies.</p>
                    </div>
                    <div class="gb-footer">
                        <p><img src="' . plugins_url('img/gb-logo-white-512.png', dirname(__FILE__)) . '" alt="getButterfly"></p>
                        <p>For support, feature requests and bug reporting, please visit the <a href="https://getbutterfly.com/wordpress-plugins/wordpress-ecards-plugin/" rel="external">official website</a>. <a href="https://getbutterfly.com/members/documentation/ecards/" class="gb-documentation">eCards Documentation</a>.<br>&copy;' . date('Y') . ' <a href="https://getbutterfly.com/" rel="external"><strong>getButterfly</strong>.com</a> &middot; <small>Code wrangling since 2005</small></p>
                    </div>
                </div>
            </div>';
            ?>

            <div class="ecards-stats">
                <span class="ecards-inlinebar">
                    <?php
                    global $wpdb;

                    $results = $wpdb->get_results("SELECT sent FROM `" . $wpdb->prefix . "ecards_stats` ORDER BY date DESC LIMIT 30", ARRAY_A);
                    foreach($results as $result) {
                        echo $result['sent'] . ', ';
                    }
                    ?>0
                </span>
                <?php $last30 = $wpdb->get_var("SELECT SUM(sent) FROM (SELECT sent FROM `" . $wpdb->prefix . "ecards_stats` ORDER BY date DESC limit 30) as howmany"); ?>
                <div><small><b><?php echo $ecard_counter; ?></b> total eCards sent, from which <b><?php echo $last30; ?></b> in the past 30 days.</small></div>
            </div>

			<div id="poststuff">
				<div class="postbox">
					<h2>About WordPress eCards</h2>
					<div class="inside">
						<p>
                            You are using <b>eCards</b> version <b><?php echo eCardsGetVersion(); ?></b> <span class="ecards-pro-icon">PRO</span> with <b><?php bloginfo('charset'); ?></b> charset.<br>
                            <small>You are using PHP version <?php echo PHP_VERSION; ?> and MySQL version <?php global $wpdb; echo $wpdb->db_version(); ?>.</small>
						</p>

						<h3>Summary and usage examples (shortcodes and template tags)</h3>
                        <p>eCards plugin uses one shortcode: <code>[ecard]</code> for all image types (JPG, PNG, GIF). Adding eCards to a post or a page is accomplished by uploading one or more images for the <code>[ecard]</code> shortcode. Images should be uploaded directly to the post or page, not from the <b>Media Library</b>. Inserting the images is not necessary, as the plugin creates the eCard automatically.</p>

                        <p>
                            <small>1.</small> Add the <code>[ecard]</code> shortcode to a post or a page or call the function from a template file:<br>
                            <code>&lt;?php if(function_exists('display_ecardMe')) echo display_ecardMe(); ?&gt;</code>
                        </p>
                        <p>
                            <small>2.</small> Use the <code>[ecard_counter]</code> shortcode to display the number of eCards sent or call the function from a template file (example: <code>[ecard_counter]</code> eCards sent so far!):<br>
                            <code>&lt;?php if(function_exists('display_ecardCounter')) echo display_ecardCounter(); ?&gt;</code>
                        </p>
                        <p><small>3.</small> Use the <code>[paypal amount=8][ecard][/paypal]</code> shortcode to hide the eCard form and require payment. Only guests and non-members see the payment button. Members always see the hidden content.</p>

                        <p><small>4.</small> Use <code>noselect</code> as ALT text for attached images you do not want included as eCards.</p>

                        <h3>Styling examples (CSS classes)</h3>
						<p>Use <code>.ecard-confirmation</code> class to style the confirmation message, use <code>.ecard-error</code> class to style the error message.</p>

						<p>Use <code>.ecards</code> class as a selector for lightbox plugins. Based on your plugin configuration, you can also use <code>.ecard a</code> as a selector.</p>
					</div>
				</div>
			</div>
		<?php } if($active_tab === 'ecards_settings') { ?>
			<form method="post" action="">
    			<h3 class="title"><?php _e('eCards Settings', 'ecards'); ?></h3>

    		    <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="ecard_label">eCard behaviour</label></th>
    		                <td>
								<select name="ecard_label" id="ecard_label" class="regular-text">
									<option value="0"<?php if(get_option('ecard_label') == 0) echo ' selected'; ?>>Use source (large image) for eCard thumbnail</option>
									<option value="1"<?php if(get_option('ecard_label') == 1) echo ' selected'; ?>>Use label behaviour for eCard thumbnail</option>
								</select>
                                <br><small>Choose what happens when users click on eCards.</small>
    		                </td>
    		            </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_use_akismet">Akismet settings</label></th>
    		                <td>
								<select name="ecard_use_akismet" id="ecard_use_akismet" class="regular-text">
									<option value="true"<?php if(get_option('ecard_use_akismet') === 'true') echo ' selected'; ?>>Use Akismet (recommended)</option>
									<option value="false"<?php if(get_option('ecard_use_akismet') === 'false') echo ' selected'; ?>>Do not use Akismet</option>
								</select>
    							<?php
    							if (function_exists('akismet_init')) {
    								$wpcom_api_key = get_option('wordpress_api_key');
    
    								if(!empty($wpcom_api_key)) {
    									echo '<p><small>Your Akismet plugin is installed and working properly. Your API key is <code>' . $wpcom_api_key . '</code>.</small></p>';
    								} else {
    									echo '<p><small>Your Akismet plugin is installed but no API key is present. Please fix it.</small></p>';
    								}
    							} else {
    								echo '<p><small>You need Akismet in order to send eCards. Please install/activate it.</small></p>';
    							}
    							?>
    		                </td>
    		            </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_user_enable">User upload settings</label></th>
    		                <td>
                                <p>
                                    <input type="checkbox" name="ecard_user_enable" value="1" <?php if(get_option('ecard_user_enable') === '1') echo 'checked'; ?>> <label>Enable user upload</label><br>
                                    <input type="checkbox" name="ecard_dropbox_enable" value="1" <?php if(get_option('ecard_dropbox_enable') === '1') echo 'checked'; ?>> <label>Enable Dropbox upload</label>
                                </p>
                                <p>
                                    <input name="ecard_dropbox_private" id="ecard_dropbox_private" type="text" class="regular-text" value="<?php echo get_option('ecard_dropbox_private'); ?>"> <label for="ecard_dropbox_private">Dropbox API Key</label>
                                    <br><small>Allow users to send images from their Dropbox accounts. Requires an <a href="https://www.dropbox.com/developers/dropins/chooser/js" rel="external">API key</a>.</small>
                                </p>
    		                </td>
    		            </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_redirection">Redirection settings</label></th>
    		                <td>
								<select name="ecard_redirection">
									<option value="0"<?php if(get_option('ecard_redirection') === '0') echo ' selected'; ?>>Do not redirect to another page</option>
									<option value="1"<?php if(get_option('ecard_redirection') === '1') echo ' selected'; ?>>Redirect to another page (see below)</option>
								</select>
                                <br>
								<input name="ecard_page_thankyou" id="ecard_page_thankyou" type="url" class="regular-text" value="<?php echo get_option('ecard_page_thankyou'); ?>" placeholder="https://"> <label for="ecard_page_thankyou">Page to redirect to</label>
                                <br><small>Use these options to customize your success actions and/or redirect to a &quot;Thank You&quot; page.</small>
    		                </td>
    		            </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_image_size">eCard image size<br><small>(display only)</small></label></th>
    		                <td>
                                <?php $image_sizes = get_intermediate_image_sizes(); ?>
                                <select name="ecard_image_size" id="ecard_image_size">
                                    <option value="<?php echo get_option('ecard_image_size'); ?>"><?php echo get_option('ecard_image_size'); ?></option>
                                    <?php
                                    $options = get_option('ecard_image_size');
                                    $thumbsize = isset($options['thumb_size_box_select']) ? esc_attr( $options['thumb_size_box_select']) : '';
                                    $image_sizes = ecards_return_image_sizes();
                                    foreach($image_sizes as $size => $atts) { ?>
                                        <option value="<?php echo $size ;?>" <?php selected($thumbsize, $size); ?>><?php echo $size . ' - ' . implode('x', $atts); ?></option>
                                    <?php } ?>
                                    <option value="full">full (size depends on the original image)</option>
                                </select>
                                <br><small>Add more image sizes using third-party plugins.</small>
                                <br><small><b>Note that adding custom sizes may require thumbnail regeneration.</b> We recommend the <a href="https://wordpress.org/plugins/force-regenerate-thumbnails/">Force Regenerate Thumbnails</a> plugin (free).</small>
    		                </td>
    		            </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_user_create">User creation settings</label></th>
    		                <td>
								<select name="ecard_user_create">
									<option value="0"<?php if(get_option('ecard_user_create') === '0') echo ' selected'; ?>>Do not create user on eCard sending</option>
									<option value="1"<?php if(get_option('ecard_user_create') === '1') echo ' selected'; ?>>Create user on eCard sending</option>
								</select>
                                <br><small>
                                    If active, this option will create a new user with the "eCards Sender" role. This role is restricted and may be used to store users.<br>
                                    Please disclose terms of service if you activate this option.
                                </small>
    		                </td>
    		            </tr>
                        <tr><td colspan="2"><hr></td></tr>
    		            <tr>
    		                <th scope="row"><label>Debugging<br><small>(developers only)</small></label></th>
    		                <td>
                                <p>
                                    <input name="ecard_shortcode_fix" id="ecard_shortcode_fix" type="checkbox"<?php if(get_option('ecard_shortcode_fix') === 'on') echo ' checked'; ?>> <label for="ecard_shortcode_fix">Apply content shortcode fix</label>
                                    <br><small>Only use this option if your WordPress version is old, or you have a buggy theme and the shortcode is not working.</small>
                                </p>
                                <p>
                                    <input name="ecard_html_fix" id="ecard_html_fix" type="checkbox"<?php if(get_option('ecard_html_fix') === 'on') echo ' checked'; ?>> <label for="ecard_html_fix">Apply HTML content type fix</label>
                                    <br><small>Only use this option if your emails are missing formatting and line breaks.</small>
                                </p>
    		                </td>
    		            </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_post_create_status">eCard status settings<br><small>(developers only)</small></label></th>
    		                <td>
								<select name="ecard_post_create_status">
									<option value="private"<?php if(get_option('ecard_post_create_status') === 'private') echo ' selected'; ?>>Hide eCards from search engines and sitemap plugins (mark as private)</option>
									<option value="publish"<?php if(get_option('ecard_post_create_status') === 'publish') echo ' selected'; ?>>Show eCards to search engines and sitemap plugins (mark as published)</option>
								</select>
                                <br><small>eCards are saved as custom post types. Use the options above to make them available to custom loops, sitemap plugins and search engines or hide them by marking them as private posts.</small>
    		                </td>
    		            </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_show_menu_ui">Show menu UI<br><small>(developers only)</small></label></th>
    		                <td>
                                <input name="ecard_show_menu_ui" id="ecard_show_menu_ui" type="checkbox"<?php if(get_option('ecard_show_menu_ui') === 'on') echo ' checked'; ?>> <label for="ecard_show_menu_ui">Show sent/scheduled eCards in administration menu</label>
                                <br><small>By default, the eCard post type is hidden. If you need to see all sent or scheduled eCards, enable this option.</small>
    		                </td>
    		            </tr>
    		        </tbody>
    		    </table>

                <hr>
                <p><input type="submit" name="info_settings_update" class="button button-primary" value="Save Changes"></p>
			</form>
		<?php } if($active_tab === 'ecards_payment') { ?>
			<form method="post" action="">
    			<h3 class="title"><?php _e('eCards Restrictions and Payment', 'ecards'); ?></h3>
                <p>Restricting access to members only does not require payment. It only requires a user to be logged into your WordPress site.</p>
                <p>If PayPal&trade; payment option is enabled, access to eCards will be available for 10 minutes after the payment process. You do not need to enable the return mode on your PayPal&trade; account. If this feature is enabled and a return URL is set, the eCard payment will not function properly.</p>
    		    <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="ecard_restrictions">Member restrictions</label></th>
    		                <td>
								<select name="ecard_restrictions">
									<option value="0"<?php if(get_option('ecard_restrictions') === '0') echo ' selected'; ?>>Do not restrict access to eCard form</option>
									<option value="1"<?php if(get_option('ecard_restrictions') === '1') echo ' selected'; ?>>Restrict access to members only</option>
								</select> <label for="ecard_restrictions_message">Add a guest message below, if you restrict access to members only.</label>

								<?php wp_editor(get_option('ecard_restrictions_message'), 'ecard_restrictions_message', array('teeny' => true, 'textarea_rows' => 5, 'media_buttons' => false)); ?>
    		                </td>
    		            </tr>
    		            <tr>
    		                <th scope="row"><label for="p2v_who">PayPal&trade; payment</label></th>
    		                <td>
                                <p>
    								<select name="p2v_paypal_sandbox">
    									<option value="0"<?php if(get_option('p2v_paypal_sandbox') === '0') echo ' selected'; ?>>Disable PayPal&trade; sandbox</option>
    									<option value="1"<?php if(get_option('p2v_paypal_sandbox') === '1') echo ' selected'; ?>>Enable PayPal&trade; sandbox</option>
    								</select>
                                </p>
                                <p><div class="dashicons dashicons-info"></div> If payment option is enabled, access to eCards will be available for 10 minutes after the payment process.</p>
    				            <p>
    								<select name="p2v_who">
    									<option value="0"<?php if(get_option('p2v_who') === '0') echo ' selected'; ?>>Request payment via PayPal&trade; from guests only (default)</option>
    									<option value="1"<?php if(get_option('p2v_who') === '1') echo ' selected'; ?>>Request payment via PayPal&trade; from ALL users (both guests and members)</option>
    								</select>
                                    <br>
    
                                    <input name="p2v_paypal_button" id="p2v_paypal_button" type="url" class="regular-text" value="<?php echo get_option('p2v_paypal_button'); ?>"> <label for="p2v_paypal_button">PayPal&trade; Button Image URL</label>
                                    <br><small>Default is <b>https://www.paypalobjects.com/webstatic/en_US/i/btn/png/gold-rect-paypalcheckout-34px.png</b>. Find more buttons <a href="https://developer.paypal.com/docs/classic/api/buttons/" rel="external">here</a>.</small>
    								<br>
    								<input name="p2v_paypal_email" id="p2v_paypal_email" type="email" class="regular-text" value="<?php echo get_option('p2v_paypal_email'); ?>"> <label for="p2v_paypal_email">PayPal&trade; Email</label><br>
    								<input type="number" min="1" max="9999" step="0.01" name="p2v_paypal_default_amount" value="<?php echo get_option('p2v_paypal_default_amount'); ?>"> <input name="p2v_paypal_currency" id="p2v_paypal_currency" type="text" size="3" value="<?php echo get_option('p2v_paypal_currency'); ?>"> <label for="p2v_paypal_currency">PayPal&trade; Currency Code (e.g. USD, EUR, GBP)</label>
    								<br><small>PayPal&trade; amount can also be set when using the shortcode (e.g. <code>[paypal amount=8]</code>).</small>
    								<br><small>Read more about PayPal&trade; <a href="https://www.paypal.com/cgi-bin/webscr?cmd=p/sell/mc/mc_intro-outside" rel="external">accepted currencies</a> and <a href="https://developer.paypal.com/docs/classic/api/currency_codes/" rel="external">currency codes</a>.</small>
    				            </p>
                                <hr>
                                <p><div class="dashicons dashicons-info"></div> Options below are optional and are used to enhance the user experience.</p>
    				            <p>
                                    <input name="p2v_ipn" id="p2v_ipn" type="url" class="regular-text" value="<?php echo get_option('p2v_ipn'); ?>"> <label for="p2v_ipn">IPN URL (optional)</label>
                                    <br><small>The URL to which PayPal posts information about the payment, in the form of Instant Payment Notification messages.</small>
    								<br>
                                    <input name="p2v_continue" id="p2v_continue" type="url" class="regular-text" value="<?php echo get_option('p2v_continue'); ?>"> <label for="p2v_continue">"Continue Shopping" URL (optional)</label>
                                    <br><small>The URL of the page on the merchant website that buyers go to when they click the "Continue Shopping" button on the PayPal Shopping Cart page.</small>
    								<br>
                                    <input name="p2v_cancel" id="p2v_cancel" type="url" class="regular-text" value="<?php echo get_option('p2v_cancel'); ?>"> <label for="p2v_cancel">"Cancel Payment" URL (optional)</label>
                                    <br><small>A URL to which PayPal redirects the buyers' browsers if they cancel checkout before completing their payments. For example, specify a URL on your website that displays a "Payment Canceled" page. By default, PayPal redirects the browser to a PayPal webpage.</small>
    								<br>

                                    <input name="p2v_style" id="p2v_style" type="text" class="regular-text" value="<?php echo get_option('p2v_style'); ?>"> <label for="p2v_style">Payment Style (optional)</label>
                                    <br><small>The custom payment page style for checkout pages. Allowable values are <code>paypal</code>, <code>primary</code> or a custom name.</small>
    								<br>

                                    <select name="p2v_lc">
                                        <option value="">Select locale (optional)...</option>
                                        <option value="AU"<?php if(get_option('p2v_lc') === 'AU') echo ' selected'; ?>>AU – Australia</option>
                                        <option value="AT"<?php if(get_option('p2v_lc') === 'AT') echo ' selected'; ?>>AT – Austria</option>
                                        <option value="BE"<?php if(get_option('p2v_lc') === 'BE') echo ' selected'; ?>>BE – Belgium</option>
                                        <option value="BR"<?php if(get_option('p2v_lc') === 'BR') echo ' selected'; ?>>BR – Brazil</option>
                                        <option value="CA"<?php if(get_option('p2v_lc') === 'CA') echo ' selected'; ?>>CA – Canada</option>
                                        <option value="CH"<?php if(get_option('p2v_lc') === 'CH') echo ' selected'; ?>>CH – Switzerland</option>
                                        <option value="CN"<?php if(get_option('p2v_lc') === 'CN') echo ' selected'; ?>>CN – China</option>
                                        <option value="DE"<?php if(get_option('p2v_lc') === 'DE') echo ' selected'; ?>>DE – Germany</option>
                                        <option value="ES"<?php if(get_option('p2v_lc') === 'ES') echo ' selected'; ?>>ES – Spain</option>
                                        <option value="GB"<?php if(get_option('p2v_lc') === 'GB') echo ' selected'; ?>>GB – United Kingdom</option>
                                        <option value="FR"<?php if(get_option('p2v_lc') === 'FR') echo ' selected'; ?>>FR – France</option>
                                        <option value="IT"<?php if(get_option('p2v_lc') === 'IT') echo ' selected'; ?>>IT – Italy</option>
                                        <option value="NL"<?php if(get_option('p2v_lc') === 'NL') echo ' selected'; ?>>NL – Netherlands</option>
                                        <option value="PL"<?php if(get_option('p2v_lc') === 'PL') echo ' selected'; ?>>PL – Poland</option>
                                        <option value="PT"<?php if(get_option('p2v_lc') === 'PT') echo ' selected'; ?>>PT – Portugal</option>
                                        <option value="RU"<?php if(get_option('p2v_lc') === 'RU') echo ' selected'; ?>>RU – Russia</option>
                                        <option value="US"<?php if(get_option('p2v_lc') === 'US') echo ' selected'; ?>>US – United States</option>

                                        <option value="da_DK"<?php if(get_option('p2v_lc') === 'da_DK') echo ' selected'; ?>>da_DK – Danish (for Denmark only)</option>
                                        <option value="he_IL"<?php if(get_option('p2v_lc') === 'he_IL') echo ' selected'; ?>>he_IL – Hebrew (all)</option>
                                        <option value="id_ID"<?php if(get_option('p2v_lc') === 'id_ID') echo ' selected'; ?>>id_ID – Indonesian (for Indonesia only)</option>
                                        <option value="ja_JP"<?php if(get_option('p2v_lc') === 'ja_JP') echo ' selected'; ?>>ja_JP – Japanese (for Japan only)</option>
                                        <option value="no_NO"<?php if(get_option('p2v_lc') === 'no_NO') echo ' selected'; ?>>no_NO – Norwegian (for Norway only)</option>
                                        <option value="pt_BR"<?php if(get_option('p2v_lc') === 'pt_BR') echo ' selected'; ?>>pt_BR – Brazilian Portuguese (for Portugal and Brazil only)</option>
                                        <option value="ru_RU"<?php if(get_option('p2v_lc') === 'ru_RU') echo ' selected'; ?>>ru_RU – Russian (for Lithuania, Latvia, and Ukraine only)</option>
                                        <option value="sv_SE"<?php if(get_option('p2v_lc') === 'sv_SE') echo ' selected'; ?>>sv_SE – Swedish (for Sweden only)</option>
                                        <option value="th_TH"<?php if(get_option('p2v_lc') === 'th_TH') echo ' selected'; ?>>th_TH – Thai (for Thailand only)</option>
                                        <option value="tr_TR"<?php if(get_option('p2v_lc') === 'tr_TR') echo ' selected'; ?>>tr_TR – Turkish (for Turkey only)</option>
                                        <option value="zh_CN"<?php if(get_option('p2v_lc') === 'zh_CN') echo ' selected'; ?>>zh_CN – Simplified Chinese (for China only)</option>
                                        <option value="zh_HK"<?php if(get_option('p2v_lc') === 'zh_HK') echo ' selected'; ?>>zh_HK – Traditional Chinese (for Hong Kong only)</option>
                                        <option value="zh_TW"<?php if(get_option('p2v_lc') === 'AU') echo ' selected'; ?>>zh_TW – Traditional Chinese (for Taiwan only)</option>
    								</select>
                                    <br><small>The locale of the login or sign-up page, which may have the specific country's language available, depending on localization. If unspecified, PayPal determines the locale by using a cookie in the subscriber's browser. If there is no PayPal cookie, the default locale is US.</small>
                                    <br>
                                    <select name="p2v_shipping">
                                        <option value="">Select shipping requirement (optional)...</option>
                                        <option value="0"<?php if(get_option('p2v_shipping') == 0) echo ' selected'; ?>>Prompt for an address, but do not require one</option>
                                        <option value="1"<?php if(get_option('p2v_shipping') == 1) echo ' selected'; ?>>Do not prompt for an address</option>
                                        <option value="2"<?php if(get_option('p2v_shipping') == 2) echo ' selected'; ?>>Prompt for an address, and require one</option>
    								</select>
                                    <br><small>Do not prompt buyers for a shipping address.</small>

                                    <br>
                                    <input name="p2v_cbt" id="p2v_cbt" type="text" class="regular-text" value="<?php echo get_option('p2v_cbt'); ?>"> <label for="p2v_cbt">"Return to Merchant" text (optional)</label>
                                    <br><small>Sets the text for the "Return to Merchant" button on the PayPal Payment Complete page. For Business accounts, the return button displays your business name in place of the word "Merchant" by default. For Donate buttons, the text reads "Return to donations coordinator" by default.</small>
                                </p>
    		                </td>
    		            </tr>
    		        </tbody>
    		    </table>

                <hr>
				<p><input type="submit" name="info_payment_update" class="button button-primary" value="Save Changes"></p>
			</form>
        <?php } if ($active_tab === 'designer') { ?>
            <form method="post" action="">
                <h3 class="title"><?php _e('eCard Designer', 'ecards'); ?></h3>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ecard_title">Email subject</label></th>
                            <td>
                                <input name="ecard_title" id="ecard_title" type="text" class="regular-text" value="<?php echo get_option('ecard_title'); ?>">
                                <br><small>This is the subject of the eCard email.</small>
                                <br><small>Use <code class="codor">[name]</code> and <code class="codor">[email]</code> shortcodes to replace sender's name and email address (e.g. "You have received an eCard from [name]").</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_image_size_email">eCard image size<br><small>(email only)</small></label></th>
                            <td>
                                <?php $image_sizes = get_intermediate_image_sizes(); ?>
                                <select name="ecard_image_size_email" id="ecard_image_size_email">
                                    <option value="<?php echo get_option('ecard_image_size_email'); ?>"><?php echo get_option('ecard_image_size_email'); ?></option>
                                    <?php
                                    $options = get_option('ecard_image_size_email');
                                    $thumbsize = isset($options['thumb_size_box_select']) ? esc_attr( $options['thumb_size_box_select']) : '';
                                    $image_sizes = ecards_return_image_sizes();
                                    foreach ($image_sizes as $size => $atts) { ?>
                                        <option value="<?php echo $size ;?>" <?php selected($thumbsize, $size); ?>><?php echo $size . ' - ' . implode('x', $atts); ?></option>
                                    <?php } ?>
                                    <option value="full">full (size depends on the original image)</option>
                                </select>
                                <br><small>Add more image sizes using third-party plugins.</small>
                                <br><small><b>We recommend a width no wider than 600px for maximum email client compatibility.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_body_toggle">Message area</label></th>
                            <td>
                                <select name="ecard_body_toggle" id="ecard_body_toggle" class="regular-text">
                                    <option value="1"<?php if(get_option('ecard_body_toggle') === '1') echo ' selected'; ?>>Show message area (default)</option>
                                    <option value="0"<?php if(get_option('ecard_body_toggle') === '0') echo ' selected'; ?>>Hide message area</option>
                                </select>
                                <br><small>Show or hide the message textarea. Use it for &quot;Tip a friend&quot; type email message.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ecard_template">Email template</label></th>
                            <td>
                                <?php wp_editor(get_option('ecard_template'), 'ecard_template', array('textarea_rows' => 20)); ?>
                                <br><small>Use <code class="codor">[name]</code> and <code class="codor">[email]</code> Designer tags to replace sender's name and email address.</small>
                                <br><small>Use <code class="codor">[image]</code> Designer tag to add the eCard image.</small>
                                <br><small>Use <code class="codor">[ecard-link]</code> Designer tag to include the eCard URL.</small>
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
		<?php } if ($active_tab === 'ecards_email') { ?>
    		<form method="post" action="">
    			<h3 class="title"><?php _e('Email Settings', 'ecards'); ?></h3>
                <p><b>Note:</b> To avoid your email adress being marked as spam, it is highly recommended that your "from" domain match your website. Some hosts may require that your "from" address be a legitimate address. Use a plugin to set custom <b>From Name</b> and <b>From Email</b> headers. We recommend <a href="https://wordpress.org/plugins/wp-mailfrom-ii/" rel="external">WP Mail From II</a>.</p>

                <p>Sometimes emails end up in your spam (or junk) folder. Sometimes they don't arrive at all. While the latter may indicate a server issue, the former may easily be fixed by setting up an email address (ecards@yourdomain.com or noreply@yourdomain.com) and use a third-party plugin to override email options (<b>From Name</b> and <b>From Email</b>).</p>

                <p>If your host blocks the <code>mail()</code> function, or if you notice errors or restrictions, configure your WordPress site to use SMTP. We recommend <a href="https://wordpress.org/plugins/post-smtp/" rel="external">Post SMTP Mailer/Email Log</a>.</p>
                <div class="postbox">
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row"><label for="ecard_noreply">Dedicated email address</label></th>
                                    <td>
                                        <input name="ecard_noreply" id="ecard_noreply" type="email" class="regular-text" value="<?php echo get_option('ecard_noreply'); ?>">
                                        <br><small>Create a dedicated email address to use for sending eCards and prevent your messages landing in Spam/Junk folders.<br>Use <code>noreply@yourdomain.com</code>, <code>ecards@yourdomain.com</code> or something similar.</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="ecard_send_behaviour">Sending behaviour</label></th>
    		                <td>
                                <select name="ecard_send_behaviour" class="regular-text">
									<option value="1"<?php if(get_option('ecard_send_behaviour') === '1') echo ' selected'; ?>>Require recipient email address</option>
									<option value="0"<?php if(get_option('ecard_send_behaviour') === '0') echo ' selected'; ?>>Hide recipient and send all eCards to the following email address</option>
								</select>
                                <br>&lfloor; <input name="ecard_hardcoded_email" type="email" class="regular-text" value="<?php echo get_option('ecard_hardcoded_email'); ?>">
								<br><small>If you want to send all eCards to a universal email address, select the option above and fill in the email address.</small>
				            </td>
				        </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_send_later">"Send Later" behaviour</label></th>
    		                <td>
                                <select name="ecard_send_later" class="regular-text">
									<option value="1"<?php if(get_option('ecard_send_later') === '1') echo ' selected'; ?>>Allow eCard scheduling</option>
									<option value="0"<?php if(get_option('ecard_send_later') === '0') echo ' selected'; ?>>Do not allow eCard scheduling</option>
								</select>
								<br><small>Allow users to pick a later date and time to send the eCard. The plugin uses the server time - <b><code><?php echo get_option('timezone_string'); ?></code></b> - for post scheduling.</small>
				            </td>
				        </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_allow_cc">Carbon copy (CC)</label></th>
    		                <td>
                                <select name="ecard_allow_cc" id="ecard_allow_cc" class="regular-text">
									<option value="on"<?php if(get_option('ecard_allow_cc') === 'on') echo ' selected'; ?>>Allow sender to CC self</option>
									<option value="off"<?php if(get_option('ecard_allow_cc') === 'off') echo ' selected'; ?>>Do not allow sender to CC self</option>
								</select>
								<br><small>Display a checkbox to allow the sender to CC self</small>
				            </td>
				        </tr>
				    </tbody>
				</table>

                <hr>
    			<p><input type="submit" name="info_email_update" class="button button-primary" value="Save Changes"></p>
			</form>
		<?php } if($active_tab === 'ecards_labels') { ?>
			<form method="post" action="">
    			<h3 class="title"><?php _e('Labels', 'ecards'); ?></h3>
    			<p>Use the labels to personalize or translate your eCards form.</p>
    		    <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="ecard_label_name_own">Your name<br><small>(input label)</small></label></th>
    		                <td>
                                <input name="ecard_label_name_own" id="ecard_label_name_own" type="text" class="regular-text" value="<?php echo get_option('ecard_label_name_own'); ?>">
                                <br><small>Default is "Your name"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_label_email_own">Your email address<br><small>(input label)</small></label></th>
    		                <td>
                                <input name="ecard_label_email_own" id="ecard_label_email_own" type="text" class="regular-text" value="<?php echo get_option('ecard_label_email_own'); ?>">
                                <br><small>Default is "Your email address"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_label_email_friend">Your friend's email address<br><small>(input label)</small></label></th>
    		                <td>
                                <input name="ecard_label_email_friend" id="ecard_label_email_friend" type="text" class="regular-text" value="<?php echo get_option('ecard_label_email_friend'); ?>">
                                <br><small>Default is "Your friend's email address"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_label_message">eCard message<br><small>(textarea label)</small></label></th>
    		                <td>
                                <input name="ecard_label_message" id="ecard_label_message" type="text" class="regular-text" value="<?php echo get_option('ecard_label_message'); ?>">
                                <br><small>Default is "eCard message"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_label_send_time">eCard send date/time<br><small>(date/time picker label)</small></label></th>
    		                <td>
                                <input name="ecard_label_send_time" id="ecard_label_send_time" type="text" class="regular-text" value="<?php echo get_option('ecard_label_send_time'); ?>">
                                <br><small>Default is "Schedule this eCard"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_label_cc">Send a copy to self<br><small>(checkbox label)</small></label></th>
    		                <td>
                                <input name="ecard_label_cc" id="ecard_label_cc" type="text" class="regular-text" value="<?php echo get_option('ecard_label_cc'); ?>">
                                <br><small>Default is "Send a copy to self"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_label_success">Success message<br><small>(paragraph)</small></label></th>
    		                <td>
                                <input name="ecard_label_success" id="ecard_label_success" type="text" class="regular-text" value="<?php echo get_option('ecard_label_success'); ?>">
                                <br><small>Default is "eCard sent successfully!"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_submit">eCard submit<br><small>(button label)</small></label></th>
    		                <td>
                                <input id="ecard_submit" name="ecard_submit" type="text" class="regular-text" value="<?php echo get_option('ecard_submit'); ?>">
                                <br><small>Default is "Send eCard"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="ecard_link_anchor">Email link anchor<br><small>(link)</small></label></th>
    		                <td>
                                <input name="ecard_link_anchor" name="ecard_link_anchor" type="text" class="regular-text" value="<?php echo get_option('ecard_link_anchor'); ?>">
                                <br><small>Default is "Click to see your eCard!"</small>
                            </td>
                        </tr>
                    </tbody>
                </table>

				<hr>
				<p><input type="submit" name="info_labels_update" class="button button-primary" value="Save Changes"></p>
			</form>
		<?php } if($active_tab === 'ecards_appearance') { ?>
			<form method="post" action="">
    			<h3 class="title"><?php _e('Appearance', 'ecards'); ?></h3>
    			<p>Customize the appearance of your eCards.</p>
    		    <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="ecard_use_display">eCard appearance settings</label></th>
    		                <td>
                                <p>
                                    <select name="ecard_use_display" id="ecard_use_display">
                                        <option value="">Select eCard appearance...</option>
                                        <option value="carousel" <?php if (get_option('ecard_use_display') === 'carousel') echo 'selected'; ?>>Carousel</option>
                                        <option value="masonry" <?php if (get_option('ecard_use_display') === 'masonry') echo 'selected'; ?>>Masonry Grid</option>
                                    </select>
                                </p>
    		                </td>
    		            </tr>
                    </tbody>
                </table>

				<hr>
				<p><input type="submit" name="info_appearance_update" class="button button-primary" value="Save Changes"></p>
			</form>
		<?php } if($active_tab === 'ecards_diagnostics') { ?>
			<form method="post" action="">
    			<h3 class="title"><?php _e('Diagnostics', 'ecards'); ?></h3>
                <p>Try using <a href="https://wordpress.org/plugins/wp-mail-smtp/" rel="external">WP Mail SMTP</a> plugin (free) if <code>wp_mail()</code> is not working.</p>
    		    <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="ecard_test_email"><?php _e('Test <code>wp_mail()</code> function', 'ecards'); ?></label></th>
    		                <td>
                                <input name="ecard_test_email" id="ecard_test_email" type="email" class="regular-text" value="<?php echo get_option('admin_email'); ?>">
                                <br><small><?php _e('Use this address to send a test email message.', 'ecards'); ?></small>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <p><input type="submit" name="info_debug_update" class="button button-primary" value="<?php _e('Test/Save Changes', 'ecards'); ?>"></p>
			</form>
		<?php } ?>
	</div>
<?php }
