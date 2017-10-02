<?php
/**
 * Plugin Name: WP Settings Test
 * Plugin URI: https://github.com/turtlepod/wp-settings-test
 * Description: Easily Create PoC using WP Setting Page.
 * Version: 1.0.0
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
**/


/* Constants
------------------------------------------ */

$prefix = 'WPST';
define( $prefix . '_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( $prefix . '_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( $prefix . '_FILE', __FILE__ );
define( $prefix . '_PLUGIN', plugin_basename( __FILE__ ) );
define( $prefix . '_VERSION', '1.0.0' );

/* Init
------------------------------------------ */

add_action( 'plugins_loaded', function() {

	// Register Settings.
	add_action( 'admin_init', function() {
		register_setting(
			$option_group      = 'wpst',
			$option_name       = 'wpst',
			$sanitize_callback = function( $in ) { // Sanitize Here!
				$out = $in;
				return $out;
			}
		);
	} );

	// Add Settings Page.
	add_action( 'admin_menu', function() {

		// Add page.
		$page = add_menu_page(
			$page_title  = 'Test',
			$menu_title  = 'Test - Play!',
			$capability  = 'manage_options',
			$menu_slug   = 'wpst',
			$function    = function() {
				?>
				<div class="wrap">
					<h1>Test</h1>
					<form method="post" action="options.php">
						<?php settings_errors(); ?>
						<?php require_once( WPST_PATH . 'test/html.php' ); ?>
						<?php do_settings_sections( 'wpst' ); ?>
						<?php settings_fields( 'wpst' ); ?>
						<?php submit_button(); ?>
					</form>
				</div><!-- wrap -->
				<?php
			},
			$icon        = '',
			$position    = 2
		);

		// Load assets.
		add_action( 'admin_enqueue_scripts', function( $hook_suffix ) use( $page ) {
			if ( $page === $hook_suffix ) {

				// CSS.
				wp_enqueue_style( 'wpst_settings', WPST_URI . 'test/style.css', array(), WPST_VERSION );

				// JS.
				wp_enqueue_media();
				$deps = array(
					'jquery',
					'jquery-ui-sortable',
					'wp-backbone',
					'wp-util',
				);
				wp_enqueue_script( 'wpst_settings', WPST_URI . 'test/script.js', $deps, WPST_VERSION, true );

				// JS Data.
				$option = get_option( 'wpst' );
				$option = is_array( $option ) ? $option : array();
				wp_localize_script( 'wpst_settings', 'wpstData', $option );
			}
		} );
	} );

} );
