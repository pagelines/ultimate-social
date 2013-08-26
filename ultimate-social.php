<?php
/*
Plugin Name: Ultimate Social
Description: Ultimate Social is a plugin that gives you 6 styled social buttons which loads faster than normal social buttons.
Version: 1.2
Author: Aleksander Hansson
Author URI: http://ahansson.com
Demo: http://ultimatesocial.ahansson.com
Class Name: UltimateSocialSection
Workswith: templates, main, sidebar1, sidebar2, sidebar_wrap, header, footer, morefoot
Cloning: true
v3: true
*/

define( 'AH_ULTIMATE_SOCIAL_STORE_URL', 'http://shop.ahansson.com' );
define( 'AH_ULTIMATE_SOCIAL_NAME', 'Ultimate Social' );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

// retrieve our license key from the DB
$license_key = trim( get_option( 'ah_ultimate_social_license_key' ) );

// setup the updater
$edd_updater = new EDD_SL_Plugin_Updater( AH_ULTIMATE_SOCIAL_STORE_URL, __FILE__, array(
		'version' 	=> '1.2', 				// current version number
		'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
		'item_name' => AH_ULTIMATE_SOCIAL_NAME, 		// name of this plugin
		'author' 	=> 'Aleksander Hansson' // author of this plugin
	)
);


function ah_ultimate_social_license_menu() {
	add_plugins_page( 'Ultimate Social License', 'Ultimate Social License', 'manage_options', 'ultimate-social-license', 'ah_ultimate_social_license_page' );
}
add_action('admin_menu', 'ah_ultimate_social_license_menu');

function ah_ultimate_social_license_page() {
	$license 	= get_option( 'ah_ultimate_social_license_key' );
	$status 	= get_option( 'ah_ultimate_social_license_status' );
	?>
	<div class="wrap">
		<h2><?php _e('Ultimate Social License Options'); ?></h2>
		<form method="post" action="options.php">

			<?php settings_fields('ah_ultimate_social_license'); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key'); ?>
						</th>
						<td>
							<input id="ah_ultimate_social_license_key" name="ah_ultimate_social_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<label class="description" for="ah_ultimate_social_license_key"><?php _e('Enter your license key'); ?></label>
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span style="color:green;"><?php _e('active'); ?></span>
									<?php wp_nonce_field( 'ah_ultimate_social_nonce', 'ah_ultimate_social_nonce' ); ?>
									<input type="submit" class="button-secondary" name="ah_ultimate_social_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
								<?php } else {
									wp_nonce_field( 'ah_ultimate_social_nonce', 'ah_ultimate_social_nonce' ); ?>
									<input type="submit" class="button-secondary" name="ah_ultimate_social_license_activate" value="<?php _e('Activate License'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php submit_button(); ?>

		</form>
	<?php
}

function ah_ultimate_social_register_option() {
	// creates our settings in the options table
	register_setting('ah_ultimate_social_license', 'ah_ultimate_social_license_key', 'ah_ultimate_social_sanitize_license' );
}
add_action('admin_init', 'ah_ultimate_social_register_option');

function ah_ultimate_social_sanitize_license( $new ) {
	$old = get_option( 'ah_ultimate_social_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'ah_ultimate_social_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}

function ah_ultimate_social_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['ah_ultimate_social_license_activate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'ah_ultimate_social_nonce', 'ah_ultimate_social_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'ah_ultimate_social_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( AH_ULTIMATE_SOCIAL_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, AH_ULTIMATE_SOCIAL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "active" or "inactive"

		update_option( 'ah_ultimate_social_license_status', $license_data->license );

	}
}
add_action('admin_init', 'ah_ultimate_social_activate_license');


/***********************************************
* Illustrates how to deactivate a license key.
* This will descrease the site count
***********************************************/

function ah_ultimate_social_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['ah_ultimate_social_license_deactivate'] ) ) {

		// run a quick security  check
	 	if( ! check_admin_referer( 'ah_ultimate_social_nonce', 'ah_ultimate_social_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'ah_ultimate_social_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( AH_ULTIMATE_SOCIAL_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, AH_ULTIMATE_SOCIAL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'ah_ultimate_social_license_status' );

	}
}
add_action('admin_init', 'ah_ultimate_social_deactivate_license');


function ah_ultimate_social_check_license() {

	global $wp_version;

	$license = trim( get_option( 'ah_ultimate_social_license_key' ) );

	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => urlencode( AH_ULTIMATE_SOCIAL_NAME )
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, AH_ULTIMATE_SOCIAL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );


	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
		// this license is still valid
	} else {
		echo 'invalid'; exit;
		// this license is no longer valid
	}
}

class UltimateSocial {

	function __construct() {

		add_action( 'template_redirect', array( &$this, 'custom_less' ) );

		add_action( 'wp_enqueue_scripts', array( &$this, 'js' ) );

		add_action( 'wp_head', array( &$this, 'js_settings' ) );

		add_action( 'init', array( &$this, 'settings' ) );

		add_action( 'template_redirect', array( &$this, 'shortcode' ) );

		add_action( 'pagelines_loop_after_excerpt', array( &$this, 'buttons_excerpts_bottom' ) );

		add_action( 'pagelines_loop_before_post_content', array( &$this, 'buttons_posts_top' ) );

		add_action( 'pagelines_loop_after_post_content', array( &$this, 'buttons_posts_bottom' ) );

		add_action( 'pagelines_loop_before_post_content', array( &$this, 'buttons_pages_top' ) );

		add_action( 'pagelines_loop_after_post_content', array( &$this, 'buttons_pages_bottom' ) );

		add_action( 'pagelines_page', array( &$this, 'buttons_floating' ) );

	}
	/**
	 * Function for loading style.less
	 */

	function custom_less() {
		$file = sprintf( '%sstyle.less', plugin_dir_path( __FILE__ ) );
		pagelines_insert_core_less( $file );
	}

	function js() {

		wp_enqueue_script( 'jquery');

		$sharrre = sprintf( '%s/%s/%s', WP_PLUGIN_URL, basename(dirname( __FILE__ )), 'js/jquery.sharrre.min.js' );

		wp_enqueue_script( 'sharrre', $sharrre );

	}

	function js_settings() {

		$tweet_via = ploption('tweet_via') ? sprintf('buttons: { twitter: { via:"%s" } },', ploption('tweet_via') ): '';

		?>
			<script type="text/javascript">
				jQuery(document).ready(function() {

					jQuery(".us_floating .us_twitter, .us_floating .us_facebook, .us_floating .us_googleplus, .us_floating .us_pinterest, .us_floating .us_linkedin, .us_floating .us_mail").hover(function(){
					    jQuery(this).stop(true, false).animate({ width: "95px" }, 200);
					}, function() {
					    jQuery(this).stop(true, false).animate({ width: "55px" }, 200);
					});

					jQuery('.us_twitter').sharrre({
						share: {
							twitter: true
						},
						enableHover: false,
						template: '<a class="box" href="#"><div class="share"><i class="icon-twitter"></i></div><div class="count" href="#">{total}</div></a>',
						<?php echo $tweet_via; ?>
						click: function(api, options){
							api.simulateClick();
							api.openPopup('twitter');
							return false;
						}
					});
					jQuery('.us_facebook').sharrre({
						share: {
							facebook: true
						},
						enableHover: false,
						template: '<a class="box" href="#"><div class="share"><i class="icon-facebook"></i></div><div class="count" href="#">{total}</div></a>',
						click: function(api, options){
							api.simulateClick();
							api.openPopup('facebook');
							return false;
						}
					});
					jQuery('.us_googleplus').sharrre({
						share: {
							googlePlus: true
						},
						enableHover: false,
						template: '<a class="box" href="#"><div class="share"><i class="icon-google-plus"></i></div><div class="count" href="#">{total}</div></a>',
						urlCurl: '<?php printf( "%s/%s/%s", WP_PLUGIN_URL, basename(dirname( __FILE__ )), "js/sharrre.php" ); ?>',
						click: function(api, options){
							api.simulateClick();
							api.openPopup('googlePlus');
							return false;
						}
					});
					jQuery('.us_pinterest').sharrre({
						share: {
							pinterest: true
						},
						enableHover: false,
						template: '<a class="box" href="#"><div class="share"><i class="icon-pinterest"></i></div><div class="count" href="#">{total}</div></a>',
						urlCurl: '<?php printf( "%s/%s/%s", WP_PLUGIN_URL, basename(dirname( __FILE__ )), "js/sharrre.php" ); ?>',
						click: function(api, options){
							api.simulateClick();
							api.openPopup('pinterest');
							return false;
						}
					});
					jQuery('.us_linkedin').sharrre({
						share: {
							linkedin: true
						},
						enableHover: false,
						template: '<a class="box" href="#"><div class="share"><i class="icon-linkedin"></i></div><div class="count" href="#">{total}</div></a>',
						urlCurl: '<?php printf( "%s/%s/%s", WP_PLUGIN_URL, basename(dirname( __FILE__ )), "js/sharrre.php" ); ?>',
						click: function(api, options){
							api.simulateClick();
							api.openPopup('linkedin');
							return false;
						}
					});

				});

			</script>
		<?php
	}

	function settings() {
		// options array for creating the settings tab
		$options = array(

		'us_settings'  => array(
			'title' => __('Settings', 'ultimate-social'),
			'type'    => 'multi_option',
			'selectvalues' => array(
				// tweet via option
				'tweet_via'   =>  array(
					'type'    =>  'text',
					'title'   =>  __('Tweet via.', 'ultimate-social'),
					'inputlabel'  =>  __('Type in your Twitter name: @', 'ultimate-social'),
				),
			),
		),

		'us_floating'  => array(
			'title' => __( 'Floating', 'ultimate-social' ),
			'type'     => 'multi_option',
			'selectvalues'   => array(
				'us_floating_facebook' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Facebook Button?', 'ultimate-social' )
				),
				'us_floating_twitter' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Twitter Button?', 'ultimate-social' )
				),
				'us_floating_google' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Google Button?', 'ultimate-social' )
				),
				'us_floating_pinterest' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Pinterest Button?', 'ultimate-social' )
				),
				'us_floating_linkedin' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show LinkedIn Button?', 'ultimate-social' )
				),
				'us_floating_mail' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Mail Button?', 'ultimate-social' )
				),
				'us_floating_custom_url' =>  array(
					'type' => 'text',
					'inputlabel' => __( 'Custom URL? (Optional)', 'ultimate-social' )
				),
			),
		),

		'us_excerpts_bottom'  => array(
			'title' => __( 'Excerpts Bottom', 'ultimate-social' ),
			'type'     => 'multi_option',
			'selectvalues'   => array(
				'us_excerpts_bottom_facebook' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Facebook Button?', 'ultimate-social' )
				),
				'us_excerpts_bottom_twitter' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Twitter Button?', 'ultimate-social' )
				),
				'us_excerpts_bottom_google' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Google Button?', 'ultimate-social' )
				),
				'us_excerpts_bottom_pinterest' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Pinterest Button?', 'ultimate-social' )
				),
				'us_excerpts_bottom_linkedin' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show LinkedIn Button?', 'ultimate-social' )
				),
				'us_excerpts_bottom_mail' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Mail Button?', 'ultimate-social' )
				),
			),
		),

		'us_posts_top'  => array(
			'title' => __( 'Posts Top', 'ultimate-social' ),
			'type'     => 'multi_option',
			'selectvalues'   => array(
				'us_posts_top_facebook' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Facebook Button?', 'ultimate-social' )
				),
				'us_posts_top_twitter' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Twitter Button?', 'ultimate-social' )
				),
				'us_posts_top_google' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Google Button?', 'ultimate-social' )
				),
				'us_posts_top_pinterest' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Pinterest Button?', 'ultimate-social' )
				),
				'us_posts_top_linkedin' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show LinkedIn Button?', 'ultimate-social' )
				),
				'us_posts_top_mail' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Mail Button?', 'ultimate-social' )
				),
			),
		),

		'us_posts_bottom'  => array(
			'title' => __( 'Posts Bottom', 'ultimate-social' ),
			'type'     => 'multi_option',
			'selectvalues'   => array(
				'us_posts_bottom_facebook' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Facebook Button?', 'ultimate-social' )
				),
				'us_posts_bottom_twitter' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Twitter Button?', 'ultimate-social' )
				),
				'us_posts_bottom_google' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Google Button?', 'ultimate-social' )
				),
				'us_posts_bottom_pinterest' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Pinterest Button?', 'ultimate-social' )
				),
				'us_posts_bottom_linkedin' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show LinkedIn Button?', 'ultimate-social' )
				),
				'us_posts_bottom_mail' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Mail Button?', 'ultimate-social' )
				),
			),
		),

		'us_pages_top'  => array(
			'title' => __( 'Pages Top', 'ultimate-social' ),
			'type'     => 'multi_option',
			'selectvalues'   => array(
				'us_pages_top_facebook' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Facebook Button?', 'ultimate-social' )
				),
				'us_pages_top_twitter' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Twitter Button?', 'ultimate-social' )
				),
				'us_pages_top_google' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Google Button?', 'ultimate-social' )
				),
				'us_pages_top_pinterest' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Pinterest Button?', 'ultimate-social' )
				),
				'us_pages_top_linkedin' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show LinkedIn Button?', 'ultimate-social' )
				),
				'us_pages_top_mail' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Mail Button?', 'ultimate-social' )
				),
			),
		),

		'us_pages_bottom'  => array(
			'title' => __( 'Pages Bottom', 'ultimate-social' ),
			'type'     => 'multi_option',
			'selectvalues'   => array(
				'us_pages_bottom_facebook' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Facebook Button?', 'ultimate-social' )
				),
				'us_pages_bottom_twitter' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Twitter Button?', 'ultimate-social' )
				),
				'us_pages_bottom_google' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Google Button?', 'ultimate-social' )
				),
				'us_pages_bottom_pinterest' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Pinterest Button?', 'ultimate-social' )
				),
				'us_pages_bottom_linkedin' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show LinkedIn Button?', 'ultimate-social' )
				),
				'us_pages_bottom_mail' =>  array(
					'type' => 'check',
					'inputlabel' => __( 'Show Mail Button?', 'ultimate-social' )
				),
			),
		),



	);

		// add options page to pagelines settings
		pl_add_options_page(
			array(
				'name' => 'Ultimate Social',
				'array' => $options
			)
		);

	}

	function catch_first_image() {
		global $post, $posts;
		$first_img = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			if (isset($matches[1][0])) {
				$first_img = $matches [1] [0];
			} else {
				$first_img = plugins_url('/pinitimage.png', __FILE__);
			}
		return $first_img;
	}

	/**
	 * Function for loading the buttons
	 */
	function buttons_top ( $header, $format ) {

		if ( is_single() && ! ploption( 'show_in_posts_top' ) )
			return $header;

		if ( ! ploption( 'show_in_excerpts_top' ) )
			return $header;

		ob_start();
		$this->buttons();
		return $header . ob_get_clean();
	}

	function buttons_excerpts_bottom () {

		$facebook = ploption('us_excerpts_bottom_facebook');

		$twitter = ploption('us_excerpts_bottom_twitter');

		$google = ploption('us_excerpts_bottom_google');

		$pinterest = ploption('us_excerpts_bottom_pinterest');

		$linkedin = ploption('us_excerpts_bottom_linkedin');

		$mail = ploption('us_excerpts_bottom_mail');

		if ($facebook || $twitter || $google || $pinterest || $linkedin || $mail ) {

			?>
				<div class="us_excerpts_bottom">
					<?php

						$this->buttons($facebook, $twitter, $google, $pinterest, $linkedin, $mail, false);

					?>
				</div>
			<?php
		}
	}

	function buttons_posts_bottom () {

		$facebook = ploption('us_posts_bottom_facebook');

		$twitter = ploption('us_posts_bottom_twitter');

		$google = ploption('us_posts_bottom_google');

		$pinterest = ploption('us_posts_bottom_pinterest');

		$linkedin = ploption('us_posts_bottom_linkedin');

		$mail = ploption('us_posts_bottom_mail');

		if( is_single() ) {

			if ( $facebook || $twitter || $google || $pinterest || $linkedin || $mail ) {

				?>
					<div class="us_posts_top">
						<?php

							$this->buttons($facebook, $twitter, $google, $pinterest, $linkedin, $mail, false);

						?>
					</div>
				<?php
			}
		}
	}

	function buttons_posts_top () {

		$facebook = ploption('us_posts_top_facebook');

		$twitter = ploption('us_posts_top_twitter');

		$google = ploption('us_posts_top_google');

		$pinterest = ploption('us_posts_top_pinterest');

		$linkedin = ploption('us_posts_top_linkedin');

		$mail = ploption('us_posts_top_mail');

		if( is_single() ) {

			if ( $facebook || $twitter || $google || $pinterest || $linkedin || $mail ) {

				?>
					<div class="us_posts_top">
						<?php

							$this->buttons($facebook, $twitter, $google, $pinterest, $linkedin, $mail, false);

						?>
					</div>
				<?php
			}
		}
	}

	function buttons_pages_bottom () {

		$facebook = ploption('us_pages_bottom_facebook');

		$twitter = ploption('us_pages_bottom_twitter');

		$google = ploption('us_pages_bottom_google');

		$pinterest = ploption('us_pages_bottom_pinterest');

		$linkedin = ploption('us_pages_bottom_linkedin');

		$mail = ploption('us_pages_bottom_mail');

		if( is_page() ) {

			if ( $facebook || $twitter || $google || $pinterest || $linkedin || $mail ) {

				?>
					<div class="us_pages_top">
						<?php

							$this->buttons($facebook, $twitter, $google, $pinterest, $linkedin, $mail, false);

						?>
					</div>
				<?php
			}
		}
	}

	function buttons_pages_top () {


		$facebook = ploption('us_pages_top_facebook');

		$twitter = ploption('us_pages_top_twitter');

		$google = ploption('us_pages_top_google');

		$pinterest = ploption('us_pages_top_pinterest');

		$linkedin = ploption('us_pages_top_linkedin');

		$mail = ploption('us_pages_top_mail');

		if( is_page() ) {

			if ( $facebook || $twitter || $google || $pinterest || $linkedin || $mail ) {

				?>
					<div class="us_pages_top">
						<?php

							$this->buttons($facebook, $twitter, $google, $pinterest, $linkedin, $mail, false);

						?>
					</div>
				<?php
			}
		}
	}

	function buttons_floating () {

		$facebook = ploption('us_floating_facebook');

		$twitter = ploption('us_floating_twitter');

		$google = ploption('us_floating_google');

		$pinterest = ploption('us_floating_pinterest');

		$linkedin = ploption('us_floating_linkedin');

		$mail = ploption('us_floating_mail');

		$custom_url = ploption('us_floating_custom_url');

		if ( $facebook || $twitter || $google || $pinterest || $linkedin || $mail ) {

			?>
				<div class="us_floating">
					<?php

						$this->buttons($facebook, $twitter, $google, $pinterest, $linkedin, $mail, $custom_url);

					?>
				</div>
			<?php
		}

	}

	function buttons($facebook, $twitter, $google, $pinterest, $linkedin, $mail, $custom_url) {

		if ( $custom_url ) {
			$url = $custom_url;
			$text = '';
		} elseif ( in_the_loop() || is_singular() ) {
			$text = get_the_title();
			$url = get_permalink();
		} elseif ( is_archive() ) {
			$text = single_tag_title("", false);
			$url = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		} else {
			$url = get_home_url();
			$text = get_bloginfo( 'name' );
		}

		?>
			<div id="us_wrapper">

				<?php
					if ($facebook) {
						UltimateSocial::facebook_button($url, $text);
					}

					if ($twitter) {
						UltimateSocial::twitter_button($url, $text);
					}

					if ($google) {
						UltimateSocial::google_button($url, $text);
					}

					if ($pinterest) {
						UltimateSocial::pinterest_button($url, $text);
					}

					if ($linkedin) {
						UltimateSocial::linkedin_button($url, $text);
					}

					if ($mail) {
						UltimateSocial::mail_button($url, $text);
					}
				?>

			</div>
		<?php
	}

	function facebook_button($url, $text) {

		?>
			<div class="us_facebook" data-url="<?php echo $url; ?>" data-text="<?php echo $text; ?>" data-title="<i class='icon-facebook'></i>"></div>
		<?php

	}

	function twitter_button($url, $text) {

		?>
			<div class="us_twitter" data-url="<?php echo $url; ?>" data-text="<?php echo $text; ?>" data-title="<i class='icon-twitter'></i>"></div>
		<?php

	}

	function google_button($url, $text) {

		?>
			<div class="us_googleplus" data-url="<?php echo $url; ?>" data-text="<?php echo $text; ?>" data-title="<i class='icon-google-plus'></i>"></div>
		<?php

	}

	function pinterest_button($url, $text) {

		?>
			<div class="us_pinterest" data-url="<?php echo $url; ?>" data-text="<?php echo $text; ?>" data-title="<i class='icon-pinterest'></i>"></div>
		<?php

	}

	function linkedin_button($url, $text) {

		?>
			<div class="us_linkedin" data-url="<?php echo $url; ?>" data-text="<?php echo $text; ?>" data-title="<i class='icon-linkedin'></i>"></div>
		<?php

	}

	function mail_button($url, $text) {

		?>
			<a class="us_mail" href="mailto:?subject=<?php echo $text; ?>&body=Check this out: <?php echo $url; ?>"><div class="box"><div class="share"><i class="icon-envelope-alt"></i></div></div></a>
		<?php

	}

	function shortcode_markup($atts) {

		if ($atts) {
			extract(
				shortcode_atts(
					array(
						"facebook" => false,
						"twitter" => false,
						"google" => false,
						"pinterest" => false,
						"linkedin" => false,
						"mail" => false,
						"url" => false,
					), $atts
				)
			);
		} else {
			$facebook = true;
			$twitter = true;
			$google = true;
			$pinterest = true;
			$linkedin = true;
			$mail = true;
			$url = false;
		}


		ob_start();
		$this->buttons($facebook, $twitter, $google, $pinterest, $linkedin, $mail, $url);
		$output = ob_get_clean();
		return $output;
	}

	function shortcode() {
		add_shortcode( 'ultimatesocial', array( &$this, 'shortcode_markup' ) );
	}

}
new UltimateSocial;