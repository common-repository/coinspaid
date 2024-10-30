<?php
/**
 * Plugin Name: Coinspaid Payment WooCommerce
 * Description: Coinspaid payment plugin for WooCommerce - accepting payments in crypto has never been easier. Your business is unique and so are the solutions you need.
 * Version:           1.0.0
 * Author:            Coinspaid
 * Plugin URI:        https://www.coinspaid.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       coinspaid
 * Domain Path:       /languages
 *
 * @package       COINSPAID Payment Module for WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path( __FILE__ ) . 'includes/classes/class-coinspaidlog.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/classes/class-coinspaidsettings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/classes/class-coinspaidpayment.php';

define( 'COINSPAID_VERSION', '1.0.3' );

/**
 * Class CoinspaidInit
 */
class CoinspaidInit {

	/**
	 * CoinspaidInit construct
	 */
	public function __construct() {
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_coinspaid_gateway_class' ) );
		add_action( 'rest_api_init', array( $this, 'add_coinspaid_callback' ) );
	}

	/**
	 * Geteway coinspaid
	 *
	 * @param array $methods Methods.
	 */
	public function add_coinspaid_gateway_class( array $methods ) {
		require_once __DIR__ . '/includes/class-wc-gateway-coinspaid.php';
		$methods[] = 'WC_Gateway_Coinspaid';

		return $methods;
	}

	/**
	 * Register callback url
	 */
	public function add_coinspaid_callback() {
		require_once __DIR__ . '/includes/class-wc-gateway-coinspaid.php';
		register_rest_route(
			'coinspaid',
			'/callback',
			array(
				'methods'             => 'post',
				'callback'            => array( new WC_Gateway_Coinspaid(), 'coinspaid_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}
}

new CoinspaidInit();
