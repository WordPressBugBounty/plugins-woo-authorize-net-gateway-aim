<?php
/*
Plugin Name: WooCommerce Authorize.Net Gateway
Plugin URI: https://pledgedplugins.com/products/authorize-net-payment-gateway-woocommerce/
Description: A payment gateway for Authorize.Net. An Authorize.Net account and a server with cURL, SSL support, and a valid SSL certificate is required (for security reasons) for this gateway to function. Requires WC 3.3+
Version: 6.1.19
Author: Pledged Plugins
Author URI: https://pledgedplugins.com
Text Domain: wc-authnet
Domain Path: /languages
WC requires at least: 3.3
WC tested up to: 10.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Requires Plugins: woocommerce

	Copyright: © Pledged Plugins.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'wc_authnet_fs' ) ) {
	wc_authnet_fs()->set_basename( false, __FILE__ );
} else {
	if ( ! function_exists( 'wc_authnet_fs' ) ) {

		// Create a helper function for easy SDK access.
		function wc_authnet_fs() {
			global $wc_authnet_fs;

			if ( ! isset( $wc_authnet_fs ) ) {
				// Include Freemius SDK.
				require_once dirname( __FILE__ ) . '/freemius/start.php';

				$wc_authnet_fs = fs_dynamic_init( array(
					'id'             => '3348',
					'slug'           => 'woo-authorize-net-gateway-aim',
					'premium_slug'   => 'woo-authorize-net-gateway-enterprise',
					'type'           => 'plugin',
					'public_key'     => 'pk_bbbcfbaa9049689829ae3f0c2021c',
					'is_premium'     => false,
					'has_addons'     => false,
					'has_paid_plans' => true,
					'menu'           => array(
						'slug'    => 'authnet',
						'support' => false,
						'parent'  => array(
							'slug' => 'woocommerce',
						),
					),
					'is_live'        => true,
				) );
			}

			return $wc_authnet_fs;
		}

		// Init Freemius.
		wc_authnet_fs();

		// Signal that SDK was initiated.
		do_action( 'wc_authnet_fs_loaded' );
	}

	define( 'WC_AUTHNET_VERSION', '6.1.19' );
	define( 'WC_AUTHNET_MIN_PHP_VER', '5.6.0' );
	define( 'WC_AUTHNET_MIN_WC_VER', '3.3' );
	define( 'WC_AUTHNET_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
	define( 'WC_AUTHNET_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
	define( 'WC_AUTHNET_MAIN_FILE', __FILE__ );

	/**
	 * Main Authorize.Net class which sets the gateway up for us
	 */
	class WC_Authnet {

		/**
		 * @var WC_Authnet Singleton The reference the *Singleton* instance of this class
		 */
		private static $instance;

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @return WC_Authnet Singleton The *Singleton* instance.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Flag to indicate whether or not we need to load code for / support subscriptions.
		 *
		 * @var bool
		 */
		private $subscription_support_enabled = false;

		/**
		 * Flag to indicate whether or not we need to load support for pre-orders.
		 *
		 * @since 3.0.3
		 *
		 * @var bool
		 */
		private $pre_order_enabled = false;

		/**
		 * Notices (array)
		 * @var array
		 */
		public $notices = array();

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'before_woocommerce_init', function() {
				// Declaring HPOS feature compatibility
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
				}
				// Declaring cart and checkout blocks compatibility
				if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
				}
			} );

			// Actions
			add_action( 'admin_init', array( $this, 'check_environment' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'init_environment' ) );

			wc_authnet_fs()->add_filter( 'templates/checkout.php', array( $this, 'checkout_notice' ) );
			wc_authnet_fs()->add_filter( 'templates/pricing.php', array( $this, 'checkout_notice' ) );
		}

		public function submenu_setup() {
			add_submenu_page( 'woocommerce', 'WooCommerce Authorize.Net Gateway', 'Authorize.Net', 'manage_options', 'authnet', array( $this, 'submenu_page' ) );
		}

		public function submenu_page() {
			?>
			<div class="wrap">
				<h1>WooCommerce Authorize.Net Gateway</h1>
				<h3><?php _e( 'About Authorize.Net', 'wc-authnet' ); ?></h3>
				<p><?php printf( __( 'As a leading payment gateway, %sAuthorize.Net%s is trusted by more than 430,000 merchants, handling more than 1 billion transactions and $149 billion in payments every year. Authorize.Net has been working with merchants and small businesses since 1996 and will offer you a credit card payment solution that works for your business and lets you focus on what you love best.', 'wc-authnet' ), '<a href="https://reseller.authorize.net/application/?resellerId=100678" target="_blank">', '</a>' ); ?></p>
				<h3><?php _e( 'About this WooCommerce Extension', 'wc-authnet' ); ?></h3>
				<p><?php _e( 'This extension enables you to use the Authorize.Net payment gateway to accept payments via credit cards directly on checkout on your WooCommerce powered WordPress e-commerce website without redirecting customers away to the gateway website.', 'wc-authnet' ); ?></p>
				<p>
					<a class="button" href="<?php echo esc_url( $this->settings_url() ); ?>">
						<?php _e( 'Settings', 'wc-authnet' ); ?>
					</a>
					<a class="button" href="<?php echo wc_authnet_fs()->contact_url(); ?>">
						<?php _e( 'Support', 'wc-authnet' ); ?>
					</a>
				</p>
				<h3><?php _e( 'License', 'wc-authnet' ); ?></h3>
				<p><?php printf( __( 'You are using our %1$sFREE PRO%2$s version of the extension. Here are the features you will get access to if you upgrade to the %1$sENTERPRISE%2$s version:', 'wc-authnet' ), '<strong>', '</strong>' ); ?></p>
				<ol>
					<li><strong><?php _e( 'Process Subscriptions:', 'wc-authnet' );	?></strong>
						<?php printf( __( 'Use with %1$sWooCommerce Subscriptions%2$s extension to %3$screate and manage products with recurring payments%4$s — payments that will give you residual revenue you can track and count on.', 'wc-authnet' ), '<a href="https://woocommerce.com/products/woocommerce-subscriptions/" target="_blank">', '</a>', '<strong>', '</strong>' ); ?>
					</li>
					<li><strong><?php _e( 'Setup Pre-Orders:', 'wc-authnet' ); ?></strong>
						<?php printf( __( 'Use with %1$sWooCommerce Pre-Orders%2$s extension so customers can order products before they’re available by submitting their card details. The card is then automatically charged when the pre-order is available.', 'wc-authnet' ), '<a href="https://woocommerce.com/products/woocommerce-pre-orders/" target="_blank">', '</a>' ); ?>
					</li>
					<li><strong><?php _e( 'Pay via Saved Cards:', 'wc-authnet' ); ?></strong>
						<?php _e( 'Enable option to use saved card details on the gateway servers for quicker checkout. No sensitive card data is stored on the website!', 'wc-authnet' ); ?>
					</li>
					<li><strong><?php _e( 'ACH Payments:', 'wc-authnet' ); ?></strong>
						<?php _e( 'Fully supports eCheck payments via ACH network.', 'wc-authnet' ); ?>
					</li>
					<li><strong><?php _e( 'One Click Upsells:', 'wc-authnet' ); ?></strong>
						<?php printf( __( 'Compatible with %1$sFunnelKit (formerly WooFunnels) One Click Upsells%2$s.', 'wc-authnet' ), '<a href="https://funnelkit.com/woocommerce-one-click-upsells-upstroke/" target="_blank">', '</a>' ); ?>
					</li>
				</ol>
				<?php $upgrade_label = __( 'Upgrade to Enterprise!', 'wc-authnet' ); ?>
				<p>
					<a class="button button-primary" href="<?php echo wc_authnet_fs()->get_upgrade_url(); ?>">
						<strong><?php echo $upgrade_label; ?></strong>
					</a>
				</p>
			</div>
			<?php
		}

		public function settings_url() {
			return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=authnet' );
		}

		/**
		 * Add relevant links to plugins page
		 *
		 * @param array $links
		 *
		 * @return array
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . esc_url( $this->settings_url() ) . '">' . __( 'Settings', 'wc-authnet' ) . '</a>',
				'<a href="' . wc_authnet_fs()->contact_url() . '">' . __( 'Support', 'wc-authnet' ) . '</a>',
				'<a href="' . admin_url( 'admin.php?page=authnet' ) . '">' . __( 'About', 'wc-authnet' ) . '</a>'
			);

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Init localisations and files
		 */
		public function init_environment() {

			// Don't hook anything else in the plugin if we're in an incompatible environment
			if ( self::get_environment_warning() ) {
				return;
			}

			if ( ! class_exists( 'WC_Authnet_API' ) ) {
				include_once( dirname( __FILE__ ) . '/includes/class-wc-authnet-api.php' );
			}

			// Init the gateway itself
			$this->init_gateways();

			// required files
			require_once( dirname( __FILE__ ) . '/includes/class-wc-gateway-authnet-logger.php' );
			require_once( dirname( __FILE__ ) . '/includes/class-wc-authnet-api.php' );

			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ), 11 );
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'admin_menu', array( $this, 'submenu_setup' ), 80 );

			$free_api_method = WC_Authnet_API::get_free_api_method();

			if ( $free_api_method == 'aim' ) {
				if( version_compare( WC_VERSION, '8.4.0', '<' ) ) {
					add_action( 'woocommerce_order_status_processing', array( $this, 'capture_payment_aim' ), 10, 2 );
					add_action( 'woocommerce_order_status_completed', array( $this, 'capture_payment_aim' ), 10, 2 );
				} else {
					add_action( 'woocommerce_order_status_processing', array( $this, 'capture_payment_aim' ), 10, 3 );
					add_action( 'woocommerce_order_status_completed', array( $this, 'capture_payment_aim' ), 10, 3 );
				}
				add_action( 'woocommerce_order_status_cancelled', array( $this, 'cancel_payment_aim' ) );
				add_action( 'woocommerce_order_status_refunded', array( $this, 'cancel_payment_aim' ) );
			} else {
				if( version_compare( WC_VERSION, '8.4.0', '<' ) ) {
					add_action( 'woocommerce_order_status_processing', array( $this, 'capture_payment' ), 10, 2 );
					add_action( 'woocommerce_order_status_completed', array( $this, 'capture_payment' ), 10, 2 );
				} else {
					add_action( 'woocommerce_order_status_processing', array( $this, 'capture_payment' ), 10, 3 );
					add_action( 'woocommerce_order_status_completed', array( $this, 'capture_payment' ), 10, 3 );
				}
				add_action( 'woocommerce_order_status_cancelled', array( $this, 'cancel_payment' ) );
				add_action( 'woocommerce_order_status_refunded', array( $this, 'cancel_payment' ) );
			}

		}

		/**
		 * Allow this class and other classes to add slug keyed notices (to avoid duplication)
		 */
		public function add_admin_notice( $slug, $class, $message ) {
			$this->notices[ $slug ] = array(
				'class'   => $class,
				'message' => $message,
			);
		}

		/**
		 * The backup sanity check, in case the plugin is activated in a weird way,
		 * or the environment changes after activation. Also handles upgrade routines.
		 */
		public function check_environment() {

			if ( ! defined( 'IFRAME_REQUEST' ) && WC_AUTHNET_VERSION !== get_option( 'wc_authnet_version', '4.0.4' ) ) {
				$this->install();
				do_action( 'woocommerce_authnet_updated' );
			}

			$environment_warning = self::get_environment_warning();
			if ( $environment_warning && is_plugin_active( plugin_basename( __FILE__ ) ) ) {
				$this->add_admin_notice( 'bad_environment', 'error', $environment_warning );
			}

			if ( ! class_exists( 'WC_Gateway_Authnet' ) ) {
				return;
			}
			if ( ! class_exists( 'WC_Authnet_API' ) ) {
				include_once( dirname( __FILE__ ) . '/includes/class-wc-authnet-api.php' );
			}

			// Check if secret key present. Otherwise prompt, via notice, to go to setting.
			$secret = WC_Authnet_API::get_transaction_key();
			if ( empty( $secret ) && ! ( isset( $_GET['page'], $_GET['section'] ) && 'wc-settings' === $_GET['page'] && 'authnet' === $_GET['section'] ) ) {
				$setting_link = esc_url( $this->settings_url() );
				$this->add_admin_notice( 'prompt_connect', 'notice notice-warning', sprintf( __( 'Authorize.Net is almost ready. To get started, <a href="%s">set your Authorize.Net account keys</a>.', 'wc-authnet' ), $setting_link ) );
			}

			if ( class_exists( 'WC_Subscriptions' ) && function_exists( 'wcs_create_renewal_order' ) ) {
				$this->subscription_support_enabled = true;
			}
			if ( class_exists( 'WC_Pre_Orders_Order' ) ) {
				$this->pre_order_enabled = true;
			}
		}

		/**
		 * Updates the plugin version in db
		 *
		 * @return bool
		 * @version 3.1.0
		 * @since 3.1.0
		 */
		private static function _update_plugin_version() {
			delete_option( 'wc_authnet_version' );
			update_option( 'wc_authnet_version', WC_AUTHNET_VERSION );

			return true;
		}

		/**
		 * Handles upgrade routines.
		 *
		 * @since 3.1.0
		 * @version 3.1.0
		 */
		public function install() {
			if ( ! defined( 'WC_AUTHNET_INSTALLING' ) ) {
				define( 'WC_AUTHNET_INSTALLING', true );
			}

			$this->_update_plugin_version();
		}

		/**
		 * Checks the environment for compatibility problems.  Returns a string with the first incompatibility
		 * found or false if the environment has no problems.
		 */
		static function get_environment_warning() {

			if ( version_compare( phpversion(), WC_AUTHNET_MIN_PHP_VER, '<' ) ) {
				$message = __( 'WooCommerce Authorize.Net - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'wc-authnet' );

				return sprintf( $message, WC_AUTHNET_MIN_PHP_VER, phpversion() );
			}

			if ( ! defined( 'WC_VERSION' ) ) {
				return __( 'WooCommerce Authorize.Net requires WooCommerce to be activated to work.', 'wc-authnet' );
			}

			if ( version_compare( WC_VERSION, WC_AUTHNET_MIN_WC_VER, '<' ) ) {
				$message = __( 'WooCommerce Authorize.Net - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', 'wc-authnet' );

				return sprintf( $message, WC_AUTHNET_MIN_WC_VER, WC_VERSION );
			}

			if ( ! function_exists( 'curl_init' ) ) {
				return __( 'WooCommerce Authorize.Net - cURL is not installed.', 'wc-authnet' );
			}

			return false;
		}

		/**
		 * Display any notices we've collected thus far (e.g. for connection, disconnection)
		 */
		public function admin_notices() {

			foreach ( $this->notices as $notice ) {
				echo "<div class='" . esc_attr( $notice['class'] ) . "'><p>";
				echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) );
				echo '</p></div>';
			}
		}

		public function checkout_notice( $html ) {
			$notices     = array();
			$notice_html = '';

			if ( ! $this->subscription_support_enabled ) {
				$notices[] = __( 'To process subscription payments using Authorize.Net you will need the <a target="_blank" href="https://woocommerce.com/products/woocommerce-subscriptions/">WooCommerce Subscriptions</a> extension installed and running. Please continue with your purchase if you are not setting up subscriptions or will install WooCommerce Subscriptions later.', 'wc-authnet' );
			}

			if ( ! empty( $notices ) ) {
				$notice_html = "<div class='notice notice-warning' style='margin:50px 0 -30px;'>";
				if ( ! $this->subscription_support_enabled ) {
					$notice_html .= __( '<h3>WooCommerce Subscriptions Not Detected!</h3>', 'wc-authnet' );
				}
				foreach ( $notices as $notice ) {
					$notice_html .= wpautop( $notice );
				}
				$notice_html .= '</div>';
			}

			return $notice_html . $html;
		}

		/**
		 * Initialize the gateway. Called very early - in the context of the plugins_loaded action
		 *
		 * @since 1.0.0
		 */
		public function init_gateways() {
			if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
				return;
			}

			// Includes
			if ( is_admin() ) {
				require_once( dirname( __FILE__ ) . '/includes/class-wc-authnet-privacy.php' );
			}

			$free_api_method = WC_Authnet_API::get_free_api_method();

			if ( $free_api_method == 'aim' ) {
				include_once( dirname( __FILE__ ) . '/includes/aim/class-wc-gateway-authnet.php' );
			} else {
				include_once( dirname( __FILE__ ) . '/includes/class-wc-gateway-authnet.php' );
			}

			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateways' ) );
		}

		/**
		 * Add the gateways to WooCommerce
		 *
		 * @since 1.0.0
		 */
		public function add_gateways( $methods ) {
			$methods[] = 'WC_Gateway_Authnet';

			return $methods;
		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wc-authnet', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Capture payment when the order is changed from on-hold to complete or processing
		 *
		 * @param int $order_id
		 * @param $order
		 * @param $status_transition
		 */
		public function capture_payment( $order_id, $order, $status_transition = array() ) {

			if ( $order->get_payment_method() == 'authnet' ) {
				$charge   = $order->get_meta( '_authnet_charge_id' );
				$captured = $order->get_meta( '_authnet_charge_captured' );

				$gateway = new WC_Gateway_Authnet();

				if ( apply_filters( 'wc_authnet_capture_on_status_change', $gateway->capture_on_status_change, $order, $status_transition ) && $charge && $captured == 'no' ) {

					WC_Authnet_API::log( "Info: Beginning capture payment for order {$order_id} for the amount of {$order->get_total()}" );

					$order_total = $order->get_total();

					if ( 0 < $order->get_total_refunded() ) {
						$order_total = $order_total - $order->get_total_refunded();
					}

					$args = array(
						'refId'              => $order->get_id(),
						'transactionRequest' => array(
							'transactionType' => 'priorAuthCaptureTransaction',
							'amount'          => $order_total,
							'currencyCode'    => $gateway->get_payment_currency( $order->get_id() ),
							'refTransId'      => $order->get_transaction_id(),
						),
					);
					$args = apply_filters( 'wc_authnet_capture_payment_request_args', $args, $order );

					$response = WC_Authnet_API::execute( 'createTransactionRequest', $args );

					if ( is_wp_error( $response ) ) {
						if( $order->get_meta( '_authnet_capture_failed' ) == 'yes' ) {
							$order->add_order_note( sprintf( __( "<strong>Unable to capture charge!</strong> Please <strong>DO NOT FULFIL THE ORDER</strong> if the amount cannot be captured in the gateway account manually or by changing the status. In that case, set status to Failed manually and do not fulfil. \n\nAuthorize.Net failure reason: %s \n\n", 'wc-authnet' ), $response->get_error_code()  . ' - ' . $response->get_error_message() ) );
						} else {
							$order->update_status( 'failed', sprintf( __( "<strong>Unable to capture charge!</strong> The order status is set to <strong>Failed</strong> the first time to draw your attention. If the next attempt fails, your intended order status will still take place. \n\nPlease double-check that the amount is captured in the gateway account before fulfilling the order. \n\nAuthorize.Net failure reason: %s \n\n", 'wc-authnet' ), $response->get_error_code()  . ' - ' . $response->get_error_message() ) );
							$order->update_meta_data( '_authnet_capture_failed', 'yes' );
							$order->save();
						}
					} else {
						$trx_response = $response['transactionResponse'];

						if ( ! $gateway->capture && $order->get_meta( '_authnet_fds_hold' ) == 'yes' ) {
							$order->update_meta_data( '_authnet_fds_hold', 'no' );
							$order->save();
							self::capture_payment( $order_id, $order, $status_transition );

							return;
						}

						// Process valid response.
						$complete_message = sprintf( __( 'Authorize.Net charge captured for %s (Charge ID: %s).', 'wc-authnet' ), wc_price( $args['transactionRequest']['amount'], array( 'currency' => $args['transactionRequest']['currencyCode'] ) ), $trx_response['transId'] );
						$order->add_order_note( $complete_message );
						WC_Authnet_API::log( 'Success: ' . wp_strip_all_tags( $complete_message ) );

						$order->update_meta_data( '_authnet_charge_captured', 'yes' );
						$order->update_meta_data( 'Authorize.Net Payment ID', $trx_response['transId'] );

						$order->set_transaction_id( $trx_response['transId'] );
						$order->save();
					}

				}
			}
		}

		/**
		 * Cancel pre-auth on refund/cancellation
		 *
		 * @param int $order_id
		 */
		public function cancel_payment( $order_id ) {

			$order = wc_get_order( $order_id );

			if ( $order->get_payment_method() == 'authnet' ) {
				$charge          = $order->get_meta( '_authnet_charge_id' );
				$charge_captured = $order->get_meta( '_authnet_charge_captured' );

				if ( $charge && $charge_captured == 'no' ) {

					WC_Authnet_API::log( "Info: Beginning cancel payment for order {$order_id} for the amount of {$order->get_total()}" );

					$args = array(
						'refId'              => $order->get_id(),
						'transactionRequest' => array(
							'transactionType' => 'voidTransaction',
							'refTransId'      => $order->get_transaction_id(),
						),
					);
					$args = apply_filters( 'wc_authnet_cancel_payment_request_args', $args, $order );

					$response = WC_Authnet_API::execute( 'createTransactionRequest', $args );

					if ( is_wp_error( $response ) ) {
						$order->update_meta_data( '_authnet_void', 'failed' );
						$order->add_order_note( __( 'Unable to refund charge!', 'wc-authnet' ) . ' ' . $response->get_error_message() );
					} else {
						$trx_response   = $response['transactionResponse'];

						$cancel_message = sprintf( __( 'Authorize.Net charge refunded (Charge ID: %s).', 'wc-authnet' ), $trx_response['transId'] );
						$order->add_order_note( $cancel_message );
						WC_Authnet_API::log( 'Success: ' . $cancel_message );

						$order->delete_meta_data( '_authnet_charge_captured' );
						$order->delete_meta_data( '_authnet_charge_id' );
					}

					$order->save();

				}
			}
		}

		/**
		 * Capture payment when the order is changed from on-hold to complete or processing for AIM
		 *
		 * @param int $order_id
		 * @param $order
		 * @param $status_transition
		 */
		public function capture_payment_aim( $order_id, $order, $status_transition = array() ) {

			if ( $order->get_payment_method() == 'authnet' ) {
				$charge   = $order->get_meta( '_authnet_charge_id' );
				$captured = $order->get_meta( '_authnet_charge_captured' );

				$gateway = new WC_Gateway_Authnet();

				if ( apply_filters( 'wc_authnet_capture_on_status_change', $gateway->capture_on_status_change, $order, $status_transition ) && $charge && $captured == 'no' ) {

					$gateway->log( "Info: Beginning capture payment for order {$order_id} for the amount of {$order->get_total()}" );

					$order_total = $order->get_total();
					if ( 0 < $order->get_total_refunded() ) {
						$order_total = $order_total - $order->get_total_refunded();
					}

					$args = array(
						'x_amount'	 	  => $order_total,
						'x_trans_id' 	  => $order->get_transaction_id(),
						'x_type' 	 	  => 'PRIOR_AUTH_CAPTURE',
						'x_currency_code' => $gateway->get_payment_currency( $order_id ),
					);
					$args = apply_filters( 'wc_authnet_capture_payment_request_args', $args, $order );

					$response = $gateway->authnet_request( $args );

					if ( is_wp_error( $response ) ) {
						if( $order->get_meta( '_authnet_capture_failed' ) == 'yes' ) {
							$order->add_order_note( sprintf( __( "<strong>Unable to capture charge!</strong> Please <strong>DO NOT FULFIL THE ORDER</strong> if the amount cannot be captured in the gateway account manually or by changing the status. In that case, set status to Failed manually and do not fulfil. \n\nAuthorize.Net failure reason: %s \n\n", 'wc-authnet' ), $response->get_error_code()  . ' - ' . $response->get_error_message() ) );
						} else {
							$order->update_status( 'failed', sprintf( __( "<strong>Unable to capture charge!</strong> The order status is set to <strong>Failed</strong> the first time to draw your attention. If the next attempt fails, your intended order status will still take place. \n\nPlease double-check that the amount is captured in the gateway account before fulfilling the order. \n\nAuthorize.Net failure reason: %s \n\n", 'wc-authnet' ), $response->get_error_code()  . ' - ' . $response->get_error_message() ) );
							$order->update_meta_data( '_authnet_capture_failed', 'yes' );
							$order->save();
						}
					} else {
						if ( ! $gateway->capture && $order->get_meta( '_authnet_fds_hold' ) == 'yes' ) {
							$order->update_meta_data( '_authnet_fds_hold', 'no' );
							$order->save();
							self::capture_payment_aim( $order_id, $order, $status_transition );
							return;
						}

						$complete_message = sprintf( __( 'Authorize.Net charge captured for %s (Charge ID: %s).', 'wc-authnet' ), wc_price( $args['x_amount'], array( 'currency' => $args['x_currency_code'] ) ), $response['transaction_id'] );
						$order->add_order_note( $complete_message );
						$gateway->log( 'Success: ' . wp_strip_all_tags( $complete_message ) );

						$order->update_meta_data( '_authnet_charge_captured', 'yes' );
						$order->update_meta_data( 'Authorize.Net Payment ID', $response['transaction_id'] );

						$order->set_transaction_id( $response['transaction_id'] );
						$order->save();
					}

				}
			}
		}

		/**
		 * Cancel pre-auth on refund/cancellation for AIM
		 *
		 * @param int $order_id
		 */
		public function cancel_payment_aim( $order_id ) {

			$order = wc_get_order( $order_id );

			if ( $order->get_payment_method() == 'authnet' ) {
				$charge          = $order->get_meta( '_authnet_charge_id' );
				$charge_captured = $order->get_meta( '_authnet_charge_captured' );

				if ( $charge && $charge_captured == 'no' ) {

					$gateway = new WC_Gateway_Authnet();

					$gateway->log( "Info: Beginning cancel payment for order {$order_id} for the amount of {$order->get_total()}" );

					$args = array(
						'x_amount'   => $order->get_total(),
						'x_trans_id' => $order->get_transaction_id(),
						'x_type'     => 'VOID',
					);
					$args = apply_filters( 'wc_authnet_cancel_payment_request_args', $args, $order );

					$response = $gateway->authnet_request( $args );

					if ( is_wp_error( $response ) ) {
						$order->update_meta_data( '_authnet_void', 'failed' );
						$order->add_order_note( __( 'Unable to refund charge!', 'wc-authnet' ) . ' ' . $response->get_error_message() );
					} else {
						$cancel_message = sprintf( __( "Authorize.Net charge refunded (Charge ID: %s).", 'wc-authnet' ), $response['transaction_id'] );
						$order->add_order_note( $cancel_message );
						$gateway->log( "Success: $cancel_message" );

						$order->delete_meta_data( '_authnet_charge_captured' );
						$order->delete_meta_data( '_authnet_charge_id' );
					}

					$order->save();

				}
			}
		}

	}

	$GLOBALS['wc_authnet'] = WC_Authnet::get_instance();

	// Hook in Blocks integration. This action is called in a callback on plugins loaded, so current Authnet plugin class
	// implementation is too late.
	add_action( 'woocommerce_blocks_loaded', 'woocommerce_gateway_authnet_woocommerce_block_support' );

	function woocommerce_gateway_authnet_woocommerce_block_support() {
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			require_once dirname( __FILE__ ) . '/includes/class-wc-authnet-blocks-support.php';
			// priority is important here because this ensures this integration is
			// registered before the WooCommerce Blocks built-in Authnet registration.
			// Blocks code has a check in place to only register if 'authnet' is not
			// already registered.
			add_action(
				'woocommerce_blocks_payment_method_type_registration',
				function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {

					$container = Automattic\WooCommerce\Blocks\Package::container();
					// registers as shared instance.
					$container->register(
						WC_Authnet_Blocks_Support::class,
						function() {
							return new WC_Authnet_Blocks_Support();
						}
					);
					$payment_method_registry->register(
						$container->get( WC_Authnet_Blocks_Support::class )
					);
				},
				5
			);
		}
	}
}
