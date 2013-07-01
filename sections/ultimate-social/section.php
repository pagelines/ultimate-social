<?php
/*
	Section: Ultimate Social
	Author: Aleksander Hansson
	Author URI: http://ahansson.com
	Demo: http://accordy.ahansson.com
	Version: 1.0
	Description: Section to show Ultimate Social buttons anywhere on your site.
	Class Name: UltimateSocialSection
	Workswith: main, templates, header
	Cloning: true
	V3: true
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

		$options[] = array(

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
