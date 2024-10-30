<?php
/**
 * WC_Gateway_Coinspaid class.
 *
 * @package WooCommerce\Gateways
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Gateway_Coinspaid class.
 */
class WC_Gateway_Coinspaid extends WC_Payment_Gateway {

	/**
	 * Pluggin id
	 *
	 * @var string
	 */
	public $id = 'coinspaid';

	/**
	 * Completed status
	 */
	const STATUS_COMPLETED = 'completed';

	/**
	 * Order
	 *
	 * @var wc_order
	 */
	protected $wc_order;

	/**
	 * WC_Gateway_Coinspaid construct
	 */
	public function __construct() {
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);
		add_action( 'woocommerce_thankyou', array( $this, 'woocommerce_auto_complete_order' ) );

		$plugin_dir               = plugin_dir_url( __FILE__ );
		$this->form_fields        = $this->fields();
		$this->title              = __( 'Pay by crypto', 'coinspaid' );
		$this->icon               = apply_filters( 'woocommerce_gateway_icon', plugin_dir_url( __DIR__ ) . 'assets/logo.svg', $this->id );
		$this->method_description = __( 'Payment provider that allows to use CoinsPaid for payments', 'coinspaid' );
		$this->supports           = array( 'products', 'refunds' );
		$this->init_settings();
	}

	/**
	 * Get title
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Admin options
	 *
	 * @return array
	 */
	public function fields() {

		$fields = array(
			'enabled'            => array(
				'title'   => __( 'Enabled', 'coinspaid' ),
				'type'    => 'checkbox',
				'default' => '',
			),
			'public_key'         => array(
				'title'   => __( 'Public key', 'coinspaid' ),
				'type'    => 'password',
				'default' => '',
			),
			'secret_key'         => array(
				'title'   => __( 'Secret Key', 'coinspaid' ),
				'type'    => 'password',
				'default' => '',
			),
			'time_restriction'   => array(
				'title'   => __( 'Time restriction', 'coinspaid' ),
				'type'    => 'checkbox',
				'default' => '',
			),
			'sandbox_enabled'    => array(
				'title'   => __( 'Sandbox enabled', 'coinspaid' ),
				'type'    => 'checkbox',
				'default' => '',
			),
			'sandbox_public_key' => array(
				'title'   => __( 'Sandbox Public key', 'coinspaid' ),
				'type'    => 'password',
				'default' => '',
			),
			'sandbox_secret_key' => array(
				'title'   => __( 'Sandbox Secret Key', 'coinspaid' ),
				'type'    => 'password',
				'default' => '',
			),
		);

		return $fields;
	}

	/**
	 * Admin option wrapper
	 *
	 * @return void
	 */
	public function admin_options() {

		echo '<div class="container-plugin">

			<div class="container-plugin-main">
				<div class="row-logo">

				</div>
				<table class="form-table">
					';
		$this->generate_settings_html();
		$admin_options = '
					<tfoot>
					</tfoot>
				</table>
			</div>
		</div>
		<style>
			@media only screen and (min-width: 960px) {
				.container-plugin-main {
					padding: 40px;
					background: #f0f0f1;
				}

				.woocommerce-save-button {
					font-size: 18px !important;
					margin-left: 35px !important;
				}

				.row-logo {
					background-image: url("' . plugins_url( '../assets/logo.svg', __FILE__ ) . '");
					background-size: contain;
					background-repeat: no-repeat;
					width: 300px;
					height: 100px;
					margin-right: 150px;
				}
			}

			@media only screen and (max-width: 960px) {
				p.submit {
					text-align: center !important;
				}

				.woocommerce-save-button {
					font-size: 18px !important;
				}
			}
		</style>';

		$allowed_html = array(
			'style'    => array(),
			'a'        => array(
				'href'  => true,
				'title' => true,
			),
			'legend'   => array(),
			'fieldset' => array(),
			'label'    => array(
				'class' => true,
			),
			'input'    => array(
				'class' => true,
			),
			'tfoot'    => array(),
			'tr'       => array(),
			'th'       => array(
				'scope' => true,
				'class' => true,
			),
			'td'       => array(
				'scope' => true,
				'class' => true,
			),
			'strong'   => array(),
			'table'    => array(
				'class' => true,
			),
			'div'      => array(
				'class' => true,
			),
		);
		echo wp_kses( $admin_options, $allowed_html );
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$titles = array();
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product = wc_get_product( $cart_item['product_id'] );
			array_push( $titles, $product->get_title() );
		}

		$order      = wc_get_order( $order_id );
		$foreign_id = 'CP' . wp_rand( 100000, 999999 ) . '_' . $order_id;

		$params = array(
			'timer'       => CoinspaidSettings::is_time_restriction(),
			'title'       => (string) '#' . $order_id,
			'foreign_id'  => $foreign_id,
			'currency'    => $order->get_currency(),
			'amount'      => $order->get_total(),
			'url_success' => $order->get_checkout_order_received_url(),
			'url_failed'  => $order->get_checkout_payment_url( true ),
			'email_user'  => $order->get_billing_email(),
			'description' => implode( ';', $titles ),
		);

		$coinspaid = new CoinspaidPayment(
			CoinspaidSettings::get_public_key(),
			CoinspaidSettings::get_secret_key(),
			CoinspaidSettings::get_env()
		);

		$coinspaid->create_invoice( $params );
		if ( $coinspaid->has_errors() ) {
			return array(
				'result' => 'failed',
				'errors' => $coinspaid->get_errors(),
			);
		}
		$redirect_url = $coinspaid->get_redirect_link();

		if ( $redirect_url ) {
			// Remove cart.
			WC()->cart->empty_cart();

			return array(
				'result'   => 'success',
				'redirect' => $redirect_url,
			);
		}

		return array(
			'result' => 'failed',
		);
	}

	/**
	 * Complete order
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function woocommerce_auto_complete_order( $order_id ) {
		wc_reduce_stock_levels( $order_id );
		WC()->cart->empty_cart();
		$order = wc_get_order( $order_id );
		$order->update_status( self::STATUS_COMPLETED );
		exit();
	}

	/**
	 * Refund order
	 *
	 * @param int    $order_id Order id.
	 * @param float  $amount Order amount.
	 * @param string $reason Reason.
	 *
	 * @return bool
	 * @throws WP_Error Error.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		if ( isset( $result['errors'] ) ) {
			$message = 'Error: ' . ( $result['errors'] ?? wp_json_encode( $result ) );

			return new WP_Error( 'coinspaid_error', $message );
		}

		return true;
	}

	/**
	 * Callback payment
	 *
	 * @return void
	 */
	public function coinspaid_callback() {
		$data_json     = file_get_contents( 'php://input' );
		$response_data = json_decode( $data_json, true );
		$headers       = getallheaders();

		$coinspaid = new CoinspaidPayment(
			CoinspaidSettings::get_public_key(),
			CoinspaidSettings::get_secret_key(),
			CoinspaidSettings::get_env()
		);
		$coinspaid->coinspaid_callback( $response_data, $headers );
	}
}
