=== Disable Downloadable Repeat Purchase - WooCommerce ===
Contributors: squareonemedia, rwebster85, ryanhalliday
Author URI: https://github.com/ryanhalliday/woocommerce-ddrp
Plugin URL: https://github.com/ryanhalliday/woocommerce-ddrp
Requires at Least: 3.7
Tested Up To: 6.4.3
Tags: woocommerce, downloads, downloadable, repeat purchase, wordpress, wordpress.org

For WooCommerce. Disable the ability for logged in users to purchase items they already own that are downloadable.


== Description ==

This WooCommerce plugin prevents a user from being able to purchase a downloadable product that they already own, as long as they are eligable to download that product. In place of the "Add to Basket" button on the product page, a message will display informing the user they already own the item, and links to download the linked files are provided there. Those links are the special links to the file that are provided on the "My Downloads" page, or part of the "My Account" page.

If the user has had a refund for the item, or if they have used up all of their allowed download (when product has a limited number of downloads) then the normal "Add to Basket" button will show again, allowing purchase.

On any page that displays the products other than the single product page, the button shows as "Read More", as though the product is not available.

Also exposes a global `$som_user_dl_permissions` for usage elsewhere on your website such as product grid download buttons. Check the code for usage.

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

= 2.0.1 =
* Fix doing it wrong on prop access

= 2.0 =
* Way improved performance
* Tidied up code and removed some jank

= 1.3 =
* Minor fixes

= 1.2 =
* QOL Improvements
* YITH bundle support
* Error handling
* Removed debug statements
* Template changes
* Some other changes...

= 1.1 =
* Improved performance by saving query results

= 1.0 = 
* Initial release

