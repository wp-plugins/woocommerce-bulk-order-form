=== WooCommerce Quick/Bulk Order Form ===
Contributors: jprummer
Donate link: https://www.wpovernight.com/
Tags: woocommerce, quick order, bulk order, order form, bulk order form, quick order form
Requires at least: 3.4
Tested up to: 4.1
Stable tag: 2.1.1

Automatically add a bulk or quick order form to your WooCommerce site with a single shortcode.

== Description ==

This plugin helps you sell more by letting you add a WooCommerce bulk/quick order form to your website in seconds via the [wcbulkorder] shortcode. The shortcode is extremely customizable and includes the following awesome features:

* Let user search by product id, title, or sku
* Turn price fields on/off
* Set default number of rows
* Set title for product input field column
* Set title for quantity input field column
* Set title for price input field column
* Price totals are calculated in real time
* Disable jquery ui css or add your own
* Include specific products or variations by id
* Exclude specific products or variations by id
* Include only specific categories

There's also a pro version found at [WP Overnight](https://wpovernight.com/downloads/woocommerce-bulk-order-form/). Why? The funds generated help to ensure long term support of this plugin. It's like a donation but better. :)

Pro Version Includes:

* Display product images in autocomplete search
* Limit user to be able to search only by product id, title, sku, or allow them to search all.
* 4 additional search label output formats
* Add New Row button so the customer can add additional fields as needed
* Create as many forms as you want and customize them with shortcode attributes

**Download the Pro version here - https://wpovernight.com/downloads/woocommerce-bulk-order-form/**


== Installation ==

Delete any old installations of the plugin. Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

Once the plugin is activated navigate to Settings > WC Bulk Order Form. Select your options, save and you're ready to go. It's that easy!

== Screenshots ==

1. Screenshot of settings page
2. Autocomplete product search in action
3. Quantity field
4. Real-time price updating

== Frequently Asked Questions ==

== Changelog ==

= 2.1.1 =

* New: Foundation to set search time delay and character delay
* Tweak: Reformatted price display
* Fix: Price display not working for comma separated decimals

= 2.1 =

* New: Variation label display now has its own option
* New: Support Tab to more easily locate support
* Tweak: Use product thumbnail images for faster loading
* Tweak: Improved price fetching to include sale and dynamic pricing
* Tweak: Replaced inline styles with styles in wcbulkorder.css. Can be overridden from theme directory.
* Tweak: Changed price to autochange when new product is selected.
* Fix: Issue with price displaying as NaN
* Fix: Invalid argument error on activation
* Fix: Missing spinner on 'add row' in variation template

= 2.0.2 =

* Fix: Products now display if not categorized
* Fix: Removed multiple products that were displayed if they had variations

= 2.0.1 =

* New: Display attribute name and value or just attribute value in variations
* Fix: Attribute capitilization.
* Fix: Bug in standard template with variations.

= 2.0 =

**This is a major update. Test thoroughly and <a href="https://wpovernight.com/2014/11/woocommerce-bulk-order-form-2-0-update/" title="WooCommerce Bulk Order Form 2.0 Update">review the full changes here</a>**

* New: Completely new template for better handling variations.
* New: Limit product search to a specific category via shortcode.
* New: Include only a specific set of products globally via the limit products extension or per shortcode.
* New: Exclude only a specific set of products globally via the limit products extension or per shortcode.

= 1.1.4 =

* Tweak: Consolidated and cleaned up some code

= 1.1.3 =

* Fix: problem with variation attributed displaying in cart

= 1.1.2 =

* Fix: html entity decode bug

= 1.1.1 =

* Tweak: Added filter to modify bulk order form messages
* Tweak: Added filter to modify label
* Tweak: Added translation elements for bulk order from messages

= 1.1.0 =

* Feature: Set max items in search
* Tweak: Improved css
* Fix: Extra characters outputted in debug mode
* Fix: Shortcode not working in sidebars

= 1.0.9 =

* Fix: Missing Translation Function

= 1.0.8 =

* Fix: Compatibility with pa_attribute format
* Tweak: Now works with carts like <a href="https://wpovernight.com/downloads/menu-cart-pro/">Menu Cart Pro</a>

= 1.0.7 =

* Fix: Duplicate Spinner Displayed in Font Awesome
* Fix: Search By SKU Broken
* Tweak: Search for numbered titles enababled

= 1.0.6 =

* Fix: Removed extra fields outputted when price field turned off
* Tweak: Added spinner so user knows the form is working

= 1.0.5 =

* Tweak: Strings now available for translation
* Tweak: Bulk Order Form now displayed inline

= 1.0.4 =

* Tweak: WC 2.0.xx compatibility
* Tweak: Price filter added
* Fix: No longer prints unfound strings

= 1.0.3 =

* Feature: Pro version now displays images in autcomplete search
* Tweak: Variations pull attribute data for better readability
* Tweak: CSS only loaded on pages where bulk order form is used
* Fix: Output format was switched

= 1.0.2 =

* Fixed issue with search
* Added option to remove css
* Scripts are only loaded on pages that have the bulk order form
* General code improvement
* CSS now loaded from within plugin

= 1.0.1 =

Initial Release

== Upgrade Notice ==

= 2.1.1 =

* New: Foundation to set search time delay and character delay
* Tweak: Reformatted price display
* Fix: Price display not working for comma separated decimals

= 2.1 =

* New: Variation label display now has its own option
* New: Support Tab to more easily locate support
* Tweak: Use product thumbnail images for faster loading
* Tweak: Improved price fetching to include sale and dynamic pricing
* Tweak: Replaced inline styles with styles in wcbulkorder.css. Can be overridden from theme directory.
* Tweak: Changed price to autochange when new product is selected.
* Fix: Issue with price displaying as NaN
* Fix: Invalid argument error on activation
* Fix: Missing spinner on 'add row' in variation template

= 2.0.2 =

* Fix: Products now display if not categorized
* Fix: Removed multiple products that were displayed if they had variations

= 2.0.1 =

* New: Display attribute name and value or just attribute value in variations
* Fix: Attribute capitilization.
* Fix: Bug in standard template with variations.

= 2.0 =

**This is a major update. Test thoroughly and <a href="https://wpovernight.com/2014/11/woocommerce-bulk-order-form-2-0-update/" title="WooCommerce Bulk Order Form 2.0 Update">review the full changes here</a>**

* New: Completely new template for better handling variations.
* New: Limit product search to a specific category via shortcode.
* New: Include only a specific set of products globally via the limit products extension or per shortcode.
* New: Exclude only a specific set of products globally via the limit products extension or per shortcode.

= 1.1.4 =

* Tweak: Consolidated and cleaned up some code

= 1.1.3 =

* Fix: problem with variation attributed displaying in cart

= 1.1.2 =

* Fix: html entity decode bug

= 1.1.1 =

* Tweak: Added filter to modify bulk order form messages
* Tweak: Added filter to modify label
* Tweak: Added translation elements for bulk order from messages

= 1.1.0 =

* Feature: Set max items in search
* Tweak: Improved css
* Fix: Extra characters outputted in debug mode
* Fix: Shortcode not working in sidebars

= 1.0.9 =

* Fix: Missing Translation Function

= 1.0.8 =

* Fix: Compatibility with pa_attribute format
* Tweak: Now works with carts like <a href="https://wpovernight.com/downloads/menu-cart-pro/">Menu Cart Pro</a>

= 1.0.7 =

* Fix: Duplicate Spinner Displayed in Font Awesome
* Fix: Search By SKU Broken
* Tweak: Search for numbered titles enababled

= 1.0.6 =

* Fix: Removed extra fields outputted when price field turned off
* Tweak: Added spinner so user knows the form is working

= 1.0.5 =

* Tweak: Strings now available for translation
* Tweak: Bulk Order Form now displayed inline

= 1.0.4 =

* Tweak: WC 2.0.xx compatibility
* Tweak: Price filter added
* Fix: No longer prints unfound strings

= 1.0.3 =

* Feature: Pro version now displays images in autcomplete search
* Tweak: Variations pull attribute data for better readability
* Tweak: CSS only loaded on pages where bulk order form is used
* Fix: Output format was switched

= 1.0.2 =

* Fixed issue with search
* Added option to remove css
* Scripts are only loaded on pages that have the bulk order form
* General code improvement
* CSS now loaded from within plugin

= 1.0.1 =

Initial Release