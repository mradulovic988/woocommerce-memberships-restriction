<?php
/**
 * All initializations for the Memberships Plan
 *
 * @author FixRunner, dev: Marko R. <marko@fixrunner.com>
 * @version 1.0.0
 * @since 1.0.0
 * @package MembershipsPlan
 * @subpackage Mp_Init
 */


class MembershipsPlan extends Mp_Init {

	public function __construct() {

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'register_membership_plan_data' ) );
			add_action( 'admin_init', array( $this, 'membership_plan_data_register_settings' ) );
			add_action( 'admin_notices', array( $this, 'show_error_notice' ) );
		}
	}

	public function show_error_notice() {
		settings_errors();
	}

	/**
	 * Adding submenu pages inside WooCommerce admin page
	 */
	public function register_membership_plan_data() {
		add_submenu_page(
			'woocommerce',
			__('Membership Plan Data', 'membership-plan-data'),
			__('Membership Plan Data', 'membership-plan-data'),
			'manage_options',
			'membership_plan_data',
			array( $this, 'membership_plan_data_callback' )
		);
	}

	/**
	 * Created form with Settings API
	 */
	public function membership_plan_data_callback() {
		?>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'membership_plan_data_setting' );
			do_settings_sections( 'membership_plan_data_section' );

			submit_button();
			?>
		</form>
		<?php
	}

	/**
	 * Description for the section
	 */
	public function membership_plan_data_section() {
	    printf(
	        __('Manage all of your restricted, discounted products and coupon names as in the WooCommerce Memberships plugin. %s', 'membership-plan-data' ),
            '<a target="_blank" href="https://prnt.sc/vrd5gy">' . __( 'Please check this screenshot', 'membership-plan-data' ) . '</a>'
        );
	}

	/**
	 * Settings API
	 */
	public function membership_plan_data_register_settings() {
		register_setting(
			'membership_plan_data_setting',
			'membership_plan_data_setting',
			array( $this, 'membership_plan_data_sanitize' )
		);

		add_settings_section(
			'membership_plan_data_id',
			__( 'Membership Plan Data', 'membership-plan-data' ),
			array( $this, 'membership_plan_data_section' ),
			'membership_plan_data_section'
		);

		add_settings_field(
			'membership_plan_data_id_product',
			__( 'Restricted Products', 'membership-plan-data' ),
			array( $this, 'membership_plan_data_product' ),
			'membership_plan_data_section',
			'membership_plan_data_id'
		);

		add_settings_field(
			'membership_plan_data_id_coupon',
			__( 'Coupon Name', 'membership-plan-data' ),
			array( $this, 'membership_plan_data_coupon' ),
			'membership_plan_data_section',
			'membership_plan_data_id'
		);

		add_settings_field(
			'membership_plan_data_id_discount',
			__( 'Discount Products', 'membership-plan-data' ),
			array( $this, 'membership_plan_data_discount' ),
			'membership_plan_data_section',
			'membership_plan_data_id'
		);
	}

	/**
	 * Restricted products slugs field
	 */
	public function membership_plan_data_product() {
		$options = get_option( 'membership_plan_data_setting' );
		$is_options_empty = ( ! empty( $options[ 'product' ] ) ? $options[ 'product' ] : '' );

		echo '
	        <p><input type="text" id="membership_plan_data_id_product" name="membership_plan_data_setting[product]" 
	        class="membership_plan_data_product_size" 
	        value="' . esc_attr( sanitize_text_field( $is_options_empty ) ) . '" 
	        placeholder="1625, 5364, 2784">
	        <label for="membership_plan_data_id_product">
	        <span class="membership_plan_data_id_product">' . __( ' - comma separated slugs without #', 'membership-plan-data' ) . '
	        </span></label></p>';
	}

	public function membership_plan_data_coupon() {
		$options = get_option( 'membership_plan_data_setting' );
		$is_options_empty = ( ! empty( $options[ 'coupon' ] ) ? $options[ 'coupon' ] : '' );

		echo '
	        <input type="text" id="membership_plan_data_id_coupon" name="membership_plan_data_setting[coupon]" 
	        class="membership_plan_data_product_size" 
	        value="' . esc_attr( sanitize_text_field( $is_options_empty ) ) . '" 
	        placeholder="percentage">
	        <label for="membership_plan_data_id_coupon">
	        <span class="membership_plan_data_id_product">' . __( ' - add coupon slug name', 'membership-plan-data' ) . '
	        </span></label>';
	}

	public function membership_plan_data_discount() {
		$options = get_option( 'membership_plan_data_setting' );
		$is_options_empty = ( ! empty( $options[ 'discount' ] ) ? $options[ 'discount' ] : '' );

		echo '
	        <input type="text" id="membership_plan_data_id_discount" name="membership_plan_data_setting[discount]" 
	        class="membership_plan_data_product_size" 
	        value="' . esc_attr( sanitize_text_field( $is_options_empty ) ) . '" 
	        placeholder="2784, 3453, 1232">
	        <label for="membership_plan_data_id_discount">
	        <span class="membership_plan_data_id_product">' . __( ' - comma separated slugs without #', 'membership-plan-data' ) . '
	        </span></label>';
	}
}

new MembershipsPlan();
