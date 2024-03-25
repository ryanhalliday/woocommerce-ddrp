<?php
/*
Plugin Name: WooCommerce Disable Downloadable Repeat Purchase
Description: Disable the ability for logged in users to purchase items they already own that are downloadable. Links are provided on the product page for ease of re-download. It does not apply to products the customer has been refunded for, or has their download expired. It checks if the product has been purchased and is available to download on their account.
Author: Square One Media, Ryan Halliday
Author URI: https://github.com/ryanhalliday/woocommerce-ddrp
Version: 2.0.1
Text Domain: woocommerce-disable-downloadable-repeat-purchase
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

$som_user_dl_permissions = [];

function som_prepare_data(){
	global $som_user_dl_permissions;
	if (get_current_user_id() !== 0){
		$som_user_dl_permissions = wc_get_customer_download_permissions(get_current_user_id());
	}
}
add_action( 'init', 'som_prepare_data' );

function som_get_download_url($product_id, $download_id, $product_filtered_dl_permission = []){
	if (empty($product_filtered_dl_permission)){
		global $som_user_dl_permissions;
		$download_permission = array_filter($som_user_dl_permissions, function($permission) use ($product_id, $download_id){
			return $permission->product_id == $product_id && $permission->download_id == $download_id;
		});
	} else {
		$download_permission = array_filter($product_filtered_dl_permission, function($permission) use ($download_id){
			return $permission->download_id == $download_id;
		});
	}

	if (empty($download_permission)){
		return '#';
	}

	$download_permission = array_shift($download_permission);

	return add_query_arg(
		array(
			'download_file' => $product_id,
			'order'         => $download_permission->order_key,
			'email'         => rawurlencode( $download_permission->user_email ),
			'key'           => $download_permission->download_id,
		),
		home_url( '/' )
	);
}

function som_disable_repeat_purchase( $purchasable, $product ) {
	if ($product->get_type() == "yith_bundle" || is_a($product, 'YITH_WC_Bundled_Item')){
		//Handle bundle logic another day
		$bundled_items = $product->get_bundled_items();
		$purchasable_count = 0;
		foreach ( $bundled_items as $bundled_item ) {
			if ( $bundled_item->get_product()->is_purchasable() ) {
				$purchasable_count++;
			}
		}

		if ($purchasable_count == 0 && $product->get_price() !== '' ){
			return false;
		} else {
			return true;
		}
	}

	$product_id = $product->get_id(); 

	if ($product->get_downloadable() == 'yes' && wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product_id ) ) {
		global $som_user_dl_permissions;
		// User can purchase if they have no download permissions for this product
		$download_permission = array_filter($som_user_dl_permissions, function($permission) use ($product_id){
			return $permission->product_id == $product_id;
		});
		return empty($download_permission) ? true : false;
	}

	return $purchasable;
}
add_filter( 'woocommerce_is_purchasable', 'som_disable_repeat_purchase', 10, 2 );

function som_download_buttons() {
	global $som_user_dl_permissions;
	global $product;

	if (empty($som_user_dl_permissions)){
		return;
	}

	if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product->get_id() ) && ($product->get_downloadable() == 'yes') ) {

		do_action( 'woocommerce_before_available_downloads' );

		$product_download_permissions = array_filter($som_user_dl_permissions, function($permission) use ($product){
			return $permission->product_id == $product->get_id();
		});

		foreach ( $product->get_downloads() as $download ){
			do_action( 'woocommerce_available_download_start', $download );

			if ( is_numeric( $download['downloads_remaining'] ) ) {
				echo apply_filters(
					'woocommerce_available_download_count',
					'<span class="count">' . 
						sprintf( _n( 
							'%s download remaining',
							'%s downloads remaining',
							$download['downloads_remaining'],
							'woocommerce'
						), 
						$download['downloads_remaining'] 
					) . 
					'</span> ',
					$download
				);
			}

			$download_url = som_get_download_url($product->get_id(), $download['id'], $product_download_permissions);

			echo apply_filters(
				'woocommerce_available_download_link', 
				'<a class="add_to_cart_button download_button download-link-product-page" href="' . 
				esc_url( $download_url ) . 
				'">Download: ' . 
					$download['name'] . 
				'</a>', 
				$download 
			) ;

			do_action( 'woocommerce_available_download_end', $download );
		}

		do_action( 'woocommerce_after_available_downloads' );

		$displayedDownloads = true;

	}

}

function som_repeat_purchase_disabled_message(){
	global $product;

	if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product->get_id() ) && ($product->get_downloadable() == 'yes') ) {
		?>
		<div class="woocommerce">
			<div class="woocommerce-info wc-nonpurchasable-message">
				<p>
					<strong>You've already purchased this product.</strong><br>
					Your download links are above.
				</p>
			</div>
		</div>
		<?php
	}
}

add_action( 'woocommerce_single_product_summary', 'som_download_buttons', 15 );
add_action( 'woocommerce_single_product_summary', 'som_repeat_purchase_disabled_message', 31 );
