<?php
/*
Plugin Name: WooCommerce Disable Downloadable Repeat Purchase
Description: Disable the ability for logged in users to purchase items they already own that are downloadable. Links are provided on the product page for ease of re-download. It does not apply to products the customer has been refunded for, or has their download expired. It checks if the product has been purchased and is available to download on their account.
Author: Square One Media, Ryan Halliday
Author URI: https://www.squareonemedia.co.uk
Version: 1.2
Text Domain: woocommerce-disable-downloadable-repeat-purchase
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

$customers_downloadable_products = 0;

function som_disable_repeat_purchase( $purchasable, $product ) {
	global $customers_downloadable_products;

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

	// Get the ID for the current product
	$product_id = $product->get_id(); 
 
	// return false if the customer has bought the product and is currently available for download
	if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product_id ) && ($product->downloadable == 'yes') ) {

		if ($customers_downloadable_products === 0){
			$customer = WC()->customer;
			if (!is_null($customer)){
				$customers_downloadable_products = WC()->customer->get_downloadable_products();
			}
		}

		if ( $downloads = $customers_downloadable_products ) {

				foreach ( $downloads as $download ) :
					if ($download['product_id'] == $product->get_id()) {
						$purchasable = false;
					}
				endforeach;

		}
	}

	return $purchasable;
}
add_filter( 'woocommerce_is_purchasable', 'som_disable_repeat_purchase', 10, 2 );

$displayDownloads = false;
function som_repeat_purchase_buttons() {
	global $product;
	global $customers_downloadable_products;
	global $displayDownloads;
	$once = 0;
	$end = false;

	if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product->get_id() ) && ($product->downloadable == 'yes') ) {

		if ($customers_downloadable_products === 0){
			$customer = WC()->customer;
			if (!is_null($customer)){
				$customers_downloadable_products = WC()->customer->get_downloadable_products();
			}
		}

		if ( $downloads = $customers_downloadable_products ) {
			$displayDownloads = true;

			do_action( 'woocommerce_before_available_downloads' ); 

			$alreadyDisplayed = [];

			foreach ( $downloads as $download ) {

				if ($download['product_id'] == $product->get_id()) {

					if (in_array($download['download_name'], $alreadyDisplayed)){
						continue;
					} else {
						$alreadyDisplayed[] = $download['download_name'];
					}

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

					echo apply_filters(
						'woocommerce_available_download_link', 
						'<a class="add_to_cart_button download_button download-link-product-page" href="' . 
						esc_url( $download['download_url'] ) . 
						'">Download: ' . 
							$download['download_name'] . 
						'</a>', 
						$download 
					) ;

					do_action( 'woocommerce_available_download_end', $download );

				}
			}

			do_action( 'woocommerce_after_available_downloads' );

			}

		}

}

function som_repeat_purchase_disabled_message(){
	global $displayDownloads;
	if ($displayDownloads){
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

add_action( 'woocommerce_single_product_summary', 'som_repeat_purchase_buttons', 15 );
add_action( 'woocommerce_single_product_summary', 'som_repeat_purchase_disabled_message', 31 );
