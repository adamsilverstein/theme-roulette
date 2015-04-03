<?php
/*
Plugin Name: Theme Roulette
Plugin URI:  http://wordpress.org/plugins/theme-roulette
Description: A random theme at random times.
Version:     0.1.3
Author:      Adam Silverstein
License:     GPLv2+
Text Domain: thmr
Domain Path: /languages
*/

// Useful global constants
define( 'THMR_VERSION',  '0.1.3' );
define( 'THMR_URL',      plugin_dir_url( __FILE__ ) );
define( 'THMR_PATH',     dirname( __FILE__ ) . '/' );
define( 'THMR_LOACLDEV', false );

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function thmr_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'thmr' );
	load_textdomain( 'thmr', WP_LANG_DIR . '/thmr/thmr-' . $locale . '.mo' );
	load_plugin_textdomain( 'thmr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/**
	 * Filter to skip on admin.
	 */
	if ( is_admin() && apply_filters( 'thmr_dont_show_on_admin', false ) ) {
		return;
	}

	if ( THMR_LOACLDEV ){
		$plugin_path = '//wpdev.localhost/wp-content/plugins/theme-roulette/assets/js/src/theme_roulette.js';
	} else {
		$plugin_path = THMR_URL . '/assets/js/src/theme_roulette.js';
	}

	wp_enqueue_script(
		'themeroulette',
		$plugin_path,
		array ( 'jquery' ),
		THMR_VERSION,
		true
	);

	$thmr_nonce = array(
		'nonce'   => wp_create_nonce( 'thmr_action_nonce' ),
		'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
		);
	wp_localize_script( 'themeroulette', 'thmr_data', $thmr_nonce );

	add_filter( 'wp_ajax_nopriv_thmr_maybe_change_theme', 'thmr_maybe_change_theme' );
	add_filter( 'wp_ajax_thmr_maybe_change_theme', 'thmr_maybe_change_theme' );

}

/**
 * Maybe load a new theme!
 */
function thmr_maybe_change_theme() {
	$nonce = $_REQUEST['_wpnonce'];
	wp_verify_nonce( $nonce, 'thmr_action_nonce' );

	/**
	 * Randomly decide to change theme.
	 */
	$random_number = rand( 0, 9 );
	if ( apply_filters( 'thmr_times_to_switch_out_of_ten', 11 ) > $random_number ) {
		$themes = wp_get_themes();

		/**
		 * If we found some themes, activate one randomly.
		 */
		if ( ! empty( $themes ) ) {

			/**
			 * Activate one randomly!
			 */
			$theme_max = sizeof( $themes );
			$theme_index_to_activate = rand( 0, $theme_max - 1 );
			$list_stylesheets = array_values( wp_list_pluck( $themes, 'stylesheet' ) );
			$theme_to_activate = $list_stylesheets[ $theme_index_to_activate ];
			switch_theme( $theme_to_activate );

			if ( ! current_user_can( 'install_themes' ) ) {
				wp_die( __( 'You do not have sufficient permissions to install themes on this site.' ) );
			}

			include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' ); //for themes_api..


			/**
			 * Now, if we have less than 50 themes installed, lets add a new one.
			 */
			if ( $theme_max <= apply_filters( 'thmr_max_themes_to_install', 50 ) ) {
				/**
				 * Query the wp_api to get the 100 most popular plugins.
				 */
				$theme_query_options = array(
					'per_page' => 100,
					'fields' => array(
						'description'  => false,
						'tested'       => true,
						'requires'     => false,
						'rating'       => false,
						'downloaded'   => false,
						'downloadLink' => true,
						'last_updated' => false,
						'homepage'     => false,
						'num_ratings'  => false,
					),
				);

				/**
				 * Cache the remote api request.
				 */
				$themes_cache_key = 'themes_cache_key_' . THMR_VERSION;
				if ( false === ( $all_the_themes = get_transient( $themes_cache_key ) ) ) {
					$themes = themes_api( 'query_themes', $theme_query_options );
					$all_the_themes = wp_list_pluck( $themes->themes, 'slug' );
					set_transient( $themes_cache_key, $all_the_themes );
				}

				/**
				 * Filter out the plugins we already have installed.
				 */
				$uninstalled_themes = array_diff( $all_the_themes, $list_stylesheets );

				/**
				 * Anything left?
				 */
				if ( ! empty( $uninstalled_themes ) ) {

					/**
					 * Randomly select one of the remaining themes and install.
					 */
					$theme_index_to_install = rand( 1, sizeof( $uninstalled_themes ) );
					$theme_to_install       = $uninstalled_themes[ $theme_index_to_install ];
					echo $theme_to_install;

					$api = themes_api(
								'theme_information',
								array(
									'slug' => $theme_to_install,
									'fields' => array(
										'sections' => false,
										'tags' => false
									)
								)
							);
					if ( is_wp_error( $api ) ) {
						wp_die( $api );
					}

					$nonce = 'install-theme_' . $theme_to_install;
					$url = 'update.php?action=install-theme&theme=' . urlencode( $theme_to_install );
					$type = 'web';

					$upgrader = new Theme_Upgrader( new Theme_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
					$upgrader->install( $api->download_link );
				}



			}

			/**
			 * Confirm our change.
			 */
			wp_send_json_success( 'Activated ' . $theme_to_activate );

		}

		//wp_send_json_success( $themes );
	}

	wp_send_json_success( 'No change' );
}

/**
 * Activate the plugin
 */
function thmr_activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	thmr_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'thmr_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function thmr_deactivate() {

}
register_deactivation_hook( __FILE__, 'thmr_deactivate' );

// Wireup actions
add_action( 'init', 'thmr_init' );


