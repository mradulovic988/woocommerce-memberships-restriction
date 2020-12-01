<?php
/**
 * All initializations for the WooCommerce Memberships
 *
 * @author FixRunner, dev: Marko R. <marko@fixrunner.com>
 * @version 1.0.0
 * @since 1.0.0
 * @package Mp_Init
 */

if ( ! class_exists( 'Mp_Init' ) ) {
	class Mp_Init {
		public function __construct() {
			if ( is_admin() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
			} else {
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			}

			include get_stylesheet_directory() .'/memberships-plan/classes/MembershipsPlan.php';
			include get_stylesheet_directory() .'/memberships-plan/classes/ProductsAccess.php';
		}

		public function admin_enqueue_styles() {
			wp_enqueue_style(
				'memberships_plan_admin_css',
				get_stylesheet_directory_uri() . '/memberships-plan/assets/css/mp_style.css'
			);
		}

		public function enqueue_styles() {
			wp_enqueue_script(
				'memberships_plan_script',
				get_stylesheet_directory_uri() . '/memberships-plan/assets/js/mp_script.js',
				array(), '1.0.0', true
			);
		}
	}

	new Mp_Init();
}