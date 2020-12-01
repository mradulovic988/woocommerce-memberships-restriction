<?php
/**
 * Products Discounts for memberships
 *
 * @author FixRunner, dev: Marko R. <marko@fixrunner.com>
 * @version 1.0.0
 * @since 1.0.0
 * @package ProductsDiscount
 * @subpackage Mp_Init
 */

class ProductsDiscount extends ProductsAccess {

	public function __construct() {
		add_action( 'woocommerce_before_cart', array( $this, 'add_discount' ) );
	}

	/**
	 * Add a discount to specific products IDs based on the purchased product
	 *
	 * @version 1.0.0
	 * @since 1.0.0
	 */
	public function add_discount() {
		$option_value             = get_option( 'membership_plan_data_setting' );
		$selected_option          = ! empty( $option_value['product'] ) ? $option_value['product'] : '1';
		$selected_option_discount = ! empty( $option_value['discount'] ) ? $option_value['discount'] : '1';
		$selected_option_coupon   = ! empty( $option_value['coupon'] ) ? $option_value['coupon'] : '1';

		$product_ids  = array( $selected_option );
		$current_user = wp_get_current_user();
		$user         = $current_user->ID;

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( $this->has_bought_items( $user, $product_ids ) ) {

			$coupon_code = $selected_option_coupon;
			$product_ids = array( $selected_option_discount );
			$apply       = false;

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( in_array( $cart_item['product_id'], $product_ids ) ) {
					$apply = true;
					break;
				}
			}

			if ( $apply == true ) {
				WC()->cart->apply_coupon( $coupon_code );
				wc_print_notices();
			} else {
				WC()->cart->remove_coupons( sanitize_text_field( $coupon_code ) );
				wc_print_notices();
				WC()->cart->calculate_totals();
			}
		}
	}
}

new ProductsDiscount();