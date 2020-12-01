<?php
/**
 * Conditionally product access
 *
 * @author FixRunner, dev: Marko R. <marko@fixrunner.com>
 * @version 1.0.0
 * @since 1.0.0
 * @package ProductAccess
 * @subpackage Mp_Init
 */

class ProductsAccess extends Mp_Init {
	public function __construct() {
		if ( ! is_admin() ) {
			add_filter( 'user_has_cap', array( $this, 'give_permission_to_user_role' ), 20, 3 );
		}

		include get_stylesheet_directory() .'/memberships-plan/classes/ProductsDiscount.php';
	}

	/**
	 * Conditional access to page based on purchased product
	 *
	 * @param int $user_var
	 * @param int $product_ids
	 *
	 * @return bool
	 */
	public function has_bought_items( $user_var = 0,  $product_ids = 0 ) {
		global $wpdb;

		if ( is_numeric( $user_var) ) {
			$meta_key     = '_customer_user';
			$meta_value   = $user_var == 0 ? (int) get_current_user_id() : (int) $user_var;
		} else {
			$meta_key     = '_billing_email';
			$meta_value   = sanitize_email( $user_var );
		}

		$paid_statuses    = array_map( 'esc_sql', wc_get_is_paid_statuses() );
		$product_ids      = is_array( $product_ids ) ? implode(',', $product_ids) : $product_ids;

		$line_meta_value  = $product_ids !=  ( 0 || '' ) ? 'AND woim.meta_value IN ('.$product_ids.')' : 'AND woim.meta_value != 0';

		// Count the number of the products
		$count = $wpdb->get_var( "
        SELECT COUNT(p.ID) FROM {$wpdb->prefix}posts AS p
        INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
        INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim ON woi.order_item_id = woim.order_item_id
        WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $paid_statuses ) . "' )
        AND pm.meta_key = '$meta_key'
        AND pm.meta_value = '$meta_value'
        AND woim.meta_key IN ( '_product_id', '_variation_id' ) $line_meta_value 
    " );

		return $count > 0 ? true : false;
	}

	/**
	 * Allow to customer to view all restricted content
	 *
	 * @param mixed $allcaps
	 * @param array $caps
	 * @param $args
	 *
	 * @return mixed
	 */
	public function give_permission_to_user_role( $allcaps, $caps, $args ) {
		if ( isset( $caps[0] ) ) {
			$option_value = get_option( 'membership_plan_data_setting' );
			$selected_option = !empty($option_value['product']) ? $option_value['product'] : '1';

			// $product_ids  = array( 17630 );
			$product_ids  = array( $selected_option );
			$current_user = wp_get_current_user();
			$user         = $current_user->ID;

			switch ( $caps[0] ) :
				case 'wc_memberships_access_all_restricted_content':
				case 'wc_memberships_view_restricted_post_content' :
				case 'wc_memberships_view_restricted_product' :
				case 'wc_memberships_view_restricted_product_taxonomy_term':
				case 'wc_memberships_view_delayed_product_taxonomy_term':
				case 'wc_memberships_view_restricted_taxonomy_term' :
				case 'wc_memberships_view_restricted_taxonomy' :
				case 'wc_memberships_view_restricted_post_type' :
				case 'wc_memberships_view_delayed_post_type':
				case 'wc_memberships_view_delayed_taxonomy':
				case 'wc_memberships_view_delayed_taxonomy_term':
				case 'wc_memberships_view_delayed_post_content' :
				case 'wc_memberships_view_delayed_product' :
					//case 'wc_memberships_purchase_delayed_product' :
					//case 'wc_memberships_purchase_restricted_product' :

					// check if Customer and if product purchased
					if ( $this->has_bought_items( $user, $product_ids ) ) {
						if ( $allcaps['customer'] === true ) {
							$allcaps[ $caps[0] ] = true;
							break;
						}
					}

					break;
			endswitch;
		}
		return $allcaps;
	}
}
new ProductsAccess();