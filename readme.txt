=== Disable Downloadable Repeat Purchase - WooCommerce ===
Contributors: squareonemedia, rwebster85, ryanhalliday
Author URI: https://www.squareonemedia.co.uk
Plugin URL: https://wordpress.org/plugins/disable-downloadable-repeat-purchase-woocommerce/
Requires at Least: 3.7
Tested Up To: 4.5.2
Tags: woocommerce, downloads, downloadable, repeat purchase, wordpress, wordpress.org

For WooCommerce. Disable the ability for logged in users to purchase items they already own that are downloadable.


== Description ==

This WooCommerce plugin prevents a user from being able to purchase a downloadable product that they already own, as long as they are eligable to download that product. In place of the "Add to Basket" button on the product page, a message will display informing the user they already own the item, and links to download the linked files are provided there. Those links are the special links to the file that are provided on the "My Downloads" page, or part of the "My Account" page.

If the user has had a refund for the item, or if they have used up all of their allowed download (when product has a limited number of downloads) then the normal "Add to Basket" button will show again, allowing purchase.

On any page that displays the products other than the single product page, the button shows as "Read More", as though the product is not available.



== Installation ==

Just install the plugin as normal and activate it. Nothing else required.



== Frequently Asked Questions ==

= How does it work? =

The plugin file adds 2 functions that hook into WooCommerce using add_filter on 'woocommerce_is_purchasable', and 'woocommerce_single_product_summary'.

It uses div tags, including <div class="woocommerce-info wc-nonpurchasable-message">, which means it should be styled in conjunction with any WooCommerce theme. Tested on Storefront.


== Screenshots ==

1. The disabled message with default WooCommerce styles
2. How it appears with Storefront theme


== Changelog ==
= 1.1 =
* Improved performance by saving query results
= 1.0 = 
* Initial release

