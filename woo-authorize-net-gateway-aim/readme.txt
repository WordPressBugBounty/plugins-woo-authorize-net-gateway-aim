=== Authorize.Net Payment Gateway For WooCommerce ===
Contributors: mohsinoffline, freemius
Donate link: https://wpgateways.com/support/send-payment/
Tags: Authorize.Net, payment gateway, woocommerce, pci, subscriptions
Plugin URI: https://pledgedplugins.com/products/authorize-net-payment-gateway-woocommerce/
Author URI: https://pledgedplugins.com
Requires at least: 4.4
Tested up to: 6.8
Requires PHP: 5.6
Stable tag: 6.1.19
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Authorize.Net payment gateway integration for WooCommerce to accept credit cards directly on WordPress e-commerce websites.

== Description ==

[Authorize.Net](https://www.authorize.net/) Payment Gateway for [WooCommerce](https://woocommerce.com/) allows you to accept credit cards payments into your Authorize.Net merchant account from all over the world on your websites.

WooCommerce is one of the oldest and most powerful e-commerce solutions for WordPress. This platform is very widely supported in the WordPress community which makes it easy for even an entry level e-commerce entrepreneur to learn to use and modify.

#### FREE Pro Version Features
* **Easy Install**: Like all Pledged Plugins add-ons, this plugin installs with one click. After installing, you will have only a few fields to fill out before you are ready to accept credit cards on your store.
* **Secure Credit Card Processing**: Uses [Accept.js](https://developer.authorize.net/api/reference/features/acceptjs.html) library to send secure payment data directly to Authorize.Net to reduce the PCI scope.
* **Refund via Dashboard**: Process full or partial refunds, directly from your WordPress dashboard! No need to search order in your Authorize.Net account.
* **Authorize Now, Capture Later**: Optionally choose only to authorize transactions, and capture at a later date.
* **Restrict Card Types**: Optionally choose to restrict certain card types and the plugin will hide its icon and provide a proper error message on checkout.
* **Gateway Receipts**: Optionally choose to send receipts from your Authorize.Net merchant account.
* **Logging**: Enable logging so you can debug issues that arise if any.

> #### Enterprise Version Features
> * **Process Subscriptions:**  Use with  [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/)  extension to **create and manage products with recurring payments**  — payments that will give you residual revenue you can track and count on.
> * **Setup Pre-Orders:**  Use with  [WooCommerce Pre-Orders](https://woocommerce.com/products/woocommerce-pre-orders/)  extension so customers can order products before they’re available by submitting their card details. The card is then automatically charged when the pre-order is available.
> * **Pay via Saved Cards:** Enable option to use saved card details on the gateway servers for quicker checkout. No sensitive card data is stored on the website!
> * **ACH Payments:** Fully supports eCheck payments via ACH network.
> * **One Click Upsells:** Compatible with [FunnelKit (formerly WooFunnels) One Click Upsells](https://funnelkit.com/woocommerce-one-click-upsells-upstroke/).
>
> [Click here](https://pledgedplugins.com/products/authorize-net-payment-gateway-woocommerce/) for Pricing details.

#### Requirements
* Active  [Authorize.Net](https://www.authorize.net/)  account – Sign up for a sandbox account  [here](https://developer.authorize.net/hello_world/sandbox.html)  if you need to test.
* [**WooCommerce**](https://woocommerce.com/)  version 3.3 or later.
* A valid SSL certificate is required to ensure your customer credit card details are safe and make your site PCI DSS compliant. This plugin does not store the customer credit card numbers or sensitive information on your website.
#### Extend, Contribute, Integrate
Visit the [plugin page](https://pledgedplugins.com/products/authorize-net-payment-gateway-woocommerce/) for more details. Contributors are welcome to send pull requests via [Bitbucket repository](https://bitbucket.org/pledged/wc-authorize.net-pro/).

For custom payment gateway integration with your WordPress website, please [contact us here](https://wpgateways.com/support/custom-payment-gateway-integration/).

#### Disclaimer
This plugin is not affiliated with or supported by Authorize.Net, WooCommerce.com or Automattic. All logos and trademarks are the property of their respective owners.

== Installation ==

1. Upload `woo-authorize-net-gateway-aim` folder/directory to the `/wp-content/plugins/` directory.
2. Activate the plugin (WordPress -> Plugins).
3. Go to the WooCommerce settings page (WordPress -> WooCommerce -> Settings) and select the Payments tab.
4. Under the Payments tab, you will find all the available payment methods. Find the 'Authorize.Net' link in the list and click it.
5. On this page you will find all the configuration options for this payment gateway.
6. Enable the method by using the checkbox.
7. Enter the Authorize.Net account details (API Login ID, Transaction Key and Public Client Key).

**IMPORTANT:** Live merchant accounts cannot be used in a sandbox environment, so to test the plugin, please make sure you are using a separate sandbox account. If you do not have a sandbox account, you can sign up for one from <https://developer.authorize.net/hello_world/sandbox.html>. Check the Authorize.Net testing guide from <https://developer.authorize.net/hello_world/testing_guide/> to generate various test scenarios before going live.

That's it! You are ready to accept credit cards with your Authorize.Net merchant account now connected to WooCommerce.

== Frequently Asked Questions ==

= Which API method does this plugin use? =
Since version 6.0.0, the plugin uses the latest Authorize.Net  [Payment Transactions API](https://developer.authorize.net/api/reference/features/payment_transactions.html) along with [Accept.js](https://developer.authorize.net/api/reference/features/acceptjs.html) integration to provide maximum security to your transactions.

= Does this plugin support Authorize.Net AIM Emulation? =
Unfortunately, the [Authorize.Net emulation method is deprecated](https://developer.authorize.net/api/upgrade_guide/#aim), and will soon be phased out. If you are using another merchant account provider that supports Authorize.Net AIM emulator, we would advise you to use its native API instead of emulation and chances are that *we already have* a **[WooCommerce integration](https://pledgedplugins.com/product-category/woocommerce/)** available for it.

= I **still** need to use Authorize.Net AIM Emulation? =
You are in luck! The free version of the plugin has an option to use the AIM integration.

= Is SSL Required to use this plugin? =
A valid SSL certificate is required to ensure your customer credit card details are safe and make your site PCI DSS compliant. This plugin does not store the customer credit card numbers or sensitive information on your website.

== Changelog ==

= 6.1.19 =
* HOTFIX: Fixed order line items issue in AIM mode

= 6.1.18 =
* Fixed order line items issue
* Updated "WC tested up to" header to 10.0
* Updated Freemius SDK to 2.12.1

= 6.1.17 =
* Fixed issues with WC version < 8.4.0
* Updated "WC tested up to" header to 9.9

= 6.1.16 =
* Mentioned payment amount in order note
* Fixed PHP notice on loading translation
* Fixed issue with data erasure
* Added filter for capturing on order status change
* Updated Freemius SDK to 2.12.0
* Updated "WC tested up to" header to 9.8
* Updated compatibility info to WordPress 6.8

= 6.1.15 =
* Updated "WC tested up to" header to 9.7
* Updated Freemius SDK to 2.11.0

= 6.1.14 =
* Updated "WC tested up to" header to 9.6
* Fixed error handling for error code E00027
* Fixed gateway log URL

= 6.1.13 =
* Updated "WC tested up to" header to 9.5
* Updated Freemius SDK to 2.10.1

= 6.1.12 =
* Updated compatibility info to WordPress 6.7
* Updated Freemius SDK to 2.9.0

= 6.1.11 =
* Added "Capture authorized transaction on status change" option
* Updated "WC tested up to" header to 9.4
* Updated Freemius SDK to 2.8.1

= 6.1.10 =
* Added fix for set order status to Failed only once on unsuccessful capture

= 6.1.9 =
* Set order status to Failed only once on unsuccessful capture
* Updated "WC tested up to" header to 9.3
* Updated Freemius SDK to 2.8.0

= 6.1.8 =
* Fixed orders going to on hold with 6.1.7 update rollout

= 6.1.7 =
* Added filters for checkout error messages
* Added a check for allowing double quotes in the API response
* Updated compatibility info to WordPress 6.6
* Updated "WC tested up to" header to 9.2
* Updated Freemius SDK to 2.7.3

= 6.1.6 =
* Added minor improvements in code base
* Updated "WC tested up to" header to 9.0
* Updated Freemius SDK to 2.7.2

= 6.1.5 =
* Added order key check for order payment page
* Updated compatibility info to WordPress 6.5
* Updated "WC tested up to" header to 8.7

= 6.1.4 =
* Fixed issue with Elementor editor container

= 6.1.3 =
* Fixed JS error not showing on checkout block

= 6.1.2 =
* Fixed issue with 6.1.0 update rollout

= 6.1.1 =
* Reversed to 6.0.7 position

= 6.1.0 =
* Added checkout block payments support
* Updated "WC tested up to" header to 8.5
* Updated Freemius SDK to 2.6.2

= 6.0.7 =
* Updated "WC tested up to" header to 8.3
* Updated compatibility info to WordPress 6.4
* Updated Freemius SDK to 2.6.0
* Declared incompatibility with cart and checkout blocks
* Added email to refund request
* Added "Line Items" option

= 6.0.6 =
* Updated "WC tested up to" header to 8.2
* Updated Freemius SDK to 2.5.12

= 6.0.5 =
* Updated "WC tested up to" header to 8.0
* Updated compatibility info to WordPress 6.3

= 6.0.4 =
* Saved "_authnet_cc_type" to order meta
* Updated "WC tested up to" header to 7.8
* Updated Freemius SDK to 2.5.10

= 6.0.3 =
* Added billTo fields to refund payment request
* Updated "WC tested up to" header to 7.7
* Updated Freemius SDK to 2.5.7

= 6.0.2 =
* Updated Freemius SDK to 2.5.5

= 6.0.1 =
* Made compatible with WooCommerce HPOS
* Updated "WC tested up to" header to 7.5

= 6.0.0 - MAJOR UPDATE =
* Implemented Payments API and added an option to use it
* Fixed captured payments being voided on cancelling orders
* Complete overhaul of the plugin
* Updated "WC tested up to" header to 7.4
* Updated Freemius SDK to 2.5.3

= 5.2.4 =
* Updated "WC tested up to" header to 7.3

= 5.2.3 =
* Updated Freemius SDK to 2.5.2
* Updated "WC tested up to" header to 7.1
* Updated compatibility info to WordPress 6.1

= 5.2.2 =
* Added minor improvements in code base
* Fixed plugin SEO texts
* Updated "WC tested up to" header to 7.0

= 5.2.1 =
* Fixed PHP notices
* Updated "WC tested up to" header to 6.7
* Updated compatibility info to WordPress 6.0

= 5.2.0 =
* Updated "WC tested up to" header to 6.5
* Added AVS and CVV responses to order notes
* Fixed capture payments that are put on hold by Authorize.Net fraud filters
* Shown error while processing transaction with non ecommerce merchant account
* Saved "authorization_code" from transaction response to order meta
* General code clean up

= 5.1.28 =
* Updated "WC tested up to" header to 6.4
* Capture or void payment if the order is authorized regardless of whether it was changed from on-hold or not

= 5.1.27 =
* Updated Freemius SDK to 2.4.3
* Updated "WC tested up to" header to 6.3
* Updated compatibility info to WordPress 5.9

= 5.1.26 =
* Updated "WC tested up to" header to 6.0
* Restricted the state to be sent in the gateway request to 40 characters
* Restricted the country to be sent in the gateway request to 60 characters
* Restricted the zipcode to be sent in the gateway request to 20 characters
* Restricted the phone number to be sent in the gateway request to 25 characters
* Restricted the email address to be sent in the gateway request to 255 characters

= 5.1.25 =
* Updated "WC tested up to" header to 5.7
* Updated compatibility info to WordPress 5.8

= 5.1.24 =
* Updated "WC tested up to" header to 5.5

= 5.1.23 =
* Updated "WC tested up to" header to 5.3
* Updated Freemius SDK to 2.4.2

= 5.1.22 =
* Updated "WC tested up to" header to 5.1
* Updated compatibility info to WordPress 5.7

= 5.1.21 =
* Fixed notice for undefined line items
* Updated "WC tested up to" header to 5.0

= 5.1.20 =
* Updated Freemius SDK to 2.4.1
* Updated minimum WC version to 3.3
* Updated "WC tested up to" header to 4.9
* Fixed invalid line item ID issue
* Added filter on error message displayed at checkout

= 5.1.19 =
* Fixed "Pending Review" orders being marked as paid
* Updated "WC tested up to" header to 4.8
* Tested with WordPress 5.6

= 5.1.18 =
* Updated "WC tested up to" header to 4.6
* Compatible to WordPress 5.5+
* Updated Freemius SDK to 2.4.0

= 5.1.17 =
* Updated "WC tested up to" header to 4.4

= 5.1.16 =
* Fixed invalid line item ID issue and updated min WC version to 3.3
* Added initiation log for capture and void transactions

= 5.1.15 =
* Added descriptive error messages
* Updated "WC tested up to" header to 4.3

= 5.1.14 =
* Updated "WC tested up to" header to 4.2
* Added Pre-upgrade notice on pricing page

= 5.1.13 =
* Fixed order line items
* Print failed transaction response reason in order notes

= 5.1.12 =
* Sanitized user input in POST variable

= 5.1.11 =
* Added filters for Authorize.Net request parameters and transaction POST URL

= 5.1.10 =
* Updated "WC tested up to" header to 4.0

= 5.1.9 =
* Restricted the line item id to be sent in the gateway request to 31 characters

= 5.1.8 =
* Updated "WC tested up to" header to 3.9
* Restricted the city to be sent in the gateway request to 40 characters
* Restricted the address to be sent in the gateway request to 60 characters
* Restricted the company to be sent in the gateway request to 50 characters
* Restricted the first name, last name to be sent in the gateway request to 50 characters
* Restricted the order description to be sent in the gateway request to 255 characters

= 5.1.7 =
* Updated "WC tested up to" header to 3.8

= 5.1.6 =
* Fixed order status not changing to Failed on decline

= 5.1.5 =
* Made compatible with WooCommerce Sequential Order Numbers Pro

= 5.1.4 =
* Restricted the line items to be sent in the gateway request to 30

= 5.1.3 =
* Updated "WC tested up to" header to 3.7

= 5.1.2 =
* Updated Freemius SDK to 2.3.0

= 5.1.1 =
* Sanitized line items to prevent invalid characters

= 5.1.0 - MAJOR UPDATE =
* Reverted back to Authorize.Net AIM code
* Removed the use of Authorize.Net SDK since it is not GPL licensed

= 5.0.3 =
* Fixed long item names in line items throwing an error

= 5.0.2 =
* Added line item data to gateway requests
* Added shipping and tax amounts to gateway requests

= 5.0.1 =
* Updated "WC tested up to" header to 3.6
* Replaced deprecated function "reduce_order_stock" with "wc_reduce_stock_levels"

= 5.0.0 - MAJOR UPDATE =
* Updated transaction methods to Payment Transactions API and implemented Authorize.Net SDK
* Removed Authorize.Net AIM code
* Added Freemius integration for analytics, upgrade and support

= 4.0.4 =
* Fixed issue with shipping field values

= 4.0.3 =
* Fixed PHP notices
* Changed logging method
* Removed deprecated script code
* Updated post meta saving method
* Added "Refund" transaction feature
* Added shipping fields to gateway request
* Added JCB, Diners Club in Allowed Card types option
* Prevented the "state" parameter from being sent in "capture", "void" or "credit" transactions

= 4.0.2 =
* Changed plugin description

= 4.0.1 =
* Added GDPR privacy support
* Fixed false negative on SSL warning notice in admin
* Added "minimum required" and "tested upto" headers for version check in WooCommerce 3.4

= 4.0.0 =
* Added "authorize only" option
* Added logging option
* Added option to restrict card types
* Added test mode option and made HTTPS mandatory for live mode
* Passed billing details to "Pay for Order" page
* Complete overhaul of the plugin with massive improvements to the code base

= 3.5.2 =
* Updated transaction endpoint URL

= 3.5.1 =
* Made "Order Received" link dynamic
* Included customer IP in the data sent to the gateway
* Added POT file for translation

= 3.5 =
* Fixed compatibility issues with other payment gateway plugins

= 3.2.1 =
* Compatible to WooCommerce 2.3.x
* Compatible to WordPress 4.x

= 3.0 =
* Compatible to WooCommerce 2.2.2
* Compatible to WordPress 4.0

= 2.0 =
* Compatible to WooCommerce 2.1.1
