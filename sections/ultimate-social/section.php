<?php
/*
	Section: Ultimate Social
	Author: Aleksander Hansson
	Author URI: http://ahansson.com
	Demo: http://accordy.ahansson.com
	Description: Section to show Ultimate Social buttons anywhere on your site.
	Class Name: UltimateSocialSection
	Cloning: true
	V3: true
	Filter: social
*/

class UltimateSocialSection extends PageLinesSection {

	function section_styles() {

	}

	function section_head() {

	}

	function section_template() {

		$facebook = $this->opt('facebook', $this->oset);

		$twitter = $this->opt('twitter', $this->oset);

		$google = $this->opt('google', $this->oset);

		$pinterest = $this->opt('pinterest', $this->oset);

		$linkedin = $this->opt('linkedin', $this->oset);

		$mail = $this->opt('mail', $this->oset);

		$custom_url = $this->opt('custom_url', $this->oset) ? $this->opt('custom_url', $this->oset): false;

		if ( $facebook || $twitter || $google || $pinterest || $linkedin || $mail ) {
			if (class_exists('UltimateSocial')) {
		    	UltimateSocial::buttons($facebook, $twitter, $google, $pinterest, $linkedin, $mail, $custom_url);
			}
		} else {
			echo setup_section_notify($this, __('Please set up Ultimate Social.', 'ultimate-social'));
		}

	}

	function section_opts() {

		$options = array();

		$how_to_use = __( '
		<strong>Read the instructions below before asking for additional help:</strong>
		</br></br>
		<strong>Ultimate Social can be placed different places.</strong>
		</br>
		<strong class="tac">To place with section:</strong>
		</br>
		<strong>1.</strong> In the frontend editor, drag the Ultimate Social section to a template of your choice.
		</br></br>
		<strong>2.</strong> Edit settings for Ultimate Social.
		</br></br>
		<strong>3.</strong> When you are done, hit "Publish" and refresh to see changes.
		</br></br>
		<strong class="tac">To place with shortcode:</strong>
		</br>
		To show all buttons that counts on the current page:</br>
		&#91;ultimatesocial&#93;
		</br></br>
		To show a Facebook button that counts likes on http://ultimatesocial.ahansson.com:</br>
		&#91;ultimatesocial facebook="true" url="http://ultimatesocial.ahansson.com"&#93;
		</br></br>
		To hide the Facebook button but show all other buttons:</br>
		&#91;ultimatesocial facebook="false" twitter="true" google="true" pinterest="true" linkedin="true" mail="true"&#93;
		</br></br>
		<strong class="tac">To place in post, pages, etc.:</strong>
		</br>
		Go to (Global Settings->Ultimate Social) in the front editor and check/uncheck where you want it.
		</br></br>
		<div class="row zmb">
				<div class="span6 tac zmb">
					<a class="btn btn-info" href="http://forum.pagelines.com/71-products-by-aleksander-hansson/" target="_blank" style="padding:4px 0 4px;width:100%"><i class="icon-ambulance"></i>          Forum</a>
				</div>
				<div class="span6 tac zmb">
					<a class="btn btn-info" href="http://betterdms.com" target="_blank" style="padding:4px 0 4px;width:100%"><i class="icon-align-justify"></i>          Better DMS</a>
				</div>
			</div>
			<div class="row zmb" style="margin-top:4px;">
				<div class="span12 tac zmb">
					<a class="btn btn-success" href="http://shop.ahansson.com" target="_blank" style="padding:4px 0 4px;width:100%"><i class="icon-shopping-cart" ></i>          My Shop</a>
				</div>
			</div>
		', 'ultimate-social' );

		$options[] = array(
			'key' => 'us_help',
			'type'     => 'template',
			'template'      => do_shortcode( $how_to_use ),
			'title' =>__( 'How to use:', 'ultimate-social' ) ,
		);

		$options[] = array(
			'key' => 'us_placement',
			'title' => __( 'Buttons', 'ultimate-social' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'facebook',
					'type' 			=> 'check',
					'label' 		=> __( 'Show Facebook Button?', 'ultimate-social' ),
				),
				array(
					'key'			=> 'twitter',
					'type' 			=> 'check',
					'label' 		=> __( 'Show Twitter Button?', 'ultimate-social' ),
				),
				array(
					'key'			=> 'google',
					'type' 			=> 'check',
					'label' 		=> __( 'Show Google+ Button?', 'ultimate-social' ),
				),
				array(
					'key'			=> 'pinterest',
					'type' 			=> 'check',
					'label' 		=> __( 'Show Pinterest Button?', 'ultimate-social' ),
				),array(
					'key'			=> 'linkedin',
					'type' 			=> 'check',
					'label' 		=> __( 'Show LinkedIn Button?', 'ultimate-social' ),
				),
				array(
					'key'			=> 'mail',
					'type' 			=> 'check',
					'label' 		=> __( 'Show Mail Button?', 'ultimate-social' ),
				)
			)

		);

		$options[] = array(
			'key' => 'us_settings',
			'title' => __( 'Settings', 'ultimate-social' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'custom_url',
					'type' 			=> 'text',
					'label' 		=> __( 'Custom URL? (Optional)', 'ultimate-social' ),
				),
			)

		);
		return $options;
	}

}
