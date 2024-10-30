<?php
/**
 * Description Coinspaid payment class
 *
 * @package       COINSPAID Payment Module for WooCommerce
 * @copyright     (c) 2022 COINSPAID. All rights reserved.
 * @license       BSD 2 License
 */

/**
 * CoinspaidPayment
 */
class CoinspaidPayment {

	/**
	 * Api url
	 *
	 * @var string[]
	 */
	private $server = array(
		'sandbox'    => 'https://app.sandbox.cryptoprocessing.com/api/v2',
		'production' => 'https://app.cryptoprocessing.com/api/v2',
	);

	/**
	 * Headers
	 *
	 * @var string[]
	 */
	private $headers_key = array(
		'public'    => 'X-Processing-Key',
		'signature' => 'X-Processing-Signature',
	);

	/**
	 * Success status
	 */
	const STATUS_SUCCESS = 'confirmed';

	/**
	 * Pending status
	 */
	const STATUS_PENDING = 'pending';

	/**
	 * Environment
	 *
	 * @var string
	 */
	private $env = '';

	/**
	 * Logged
	 *
	 * @var bool
	 */
	private $log = false;

	/**
	 * Public key
	 *
	 * @var string
	 */
	private $public_key = '';

	/**
	 * Secret key
	 *
	 * @var string
	 */
	private $secret_key = '';

	/**
	 * Errors
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * Response data
	 *
	 * @var array
	 */
	private $response = array();

	/**
	 * Order id
	 *
	 * @var int
	 */
	private $order_id;

	/**
	 * Response status
	 *
	 * @var string
	 */
	private $response_status;

	/**
	 * Generate signature
	 *
	 * @param string $public_key Public key.
	 * @param string $secret_key Secret key.
	 * @param string $env Env.
	 * @param bool   $log Log.
	 */
	public function __construct( string $public_key, string $secret_key, string $env = 'production', bool $log = false ) {
		if ( ! empty( $public_key ) && ! empty( $secret_key ) ) {
			$this->public_key = $public_key;
			$this->secret_key = $secret_key;
		}
		if ( ! empty( $env ) && ( ( 'production' === $env ) || ( 'sandbox' === $env ) ) ) {
			$this->env = $env;
		}

		$this->log = $log;
	}

	/**
	 * Generate signature
	 *
	 * @param array $params_array Data for signature.
	 *
	 * @return string
	 */
	public function generate_signature( array $params_array ): string {
		$request_body = wp_json_encode( $params_array );

		return hash_hmac( 'sha512', $request_body, $this->secret_key );
	}

	/**
	 * Create invoice
	 *
	 * @param array $params Data for invoice.
	 *
	 * @return array|null
	 */
	public function create_invoice( array $params ): ?array {
		$command = '/invoices/create';

		$result = $this->execute( 'POST', $command, $params, true );

		if ( ! empty( $result['data'] ) ) {
			return $result;
		} else {
			$this->errors[] = $result;

			return null;
		}
	}

	/**
	 * Has errors
	 *
	 * @return bool
	 */
	public function has_errors(): bool {
		return (bool) count( $this->errors );
	}

	/**
	 * Array of errors
	 *
	 * @return array
	 */
	public function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Get redirect link
	 *
	 * @return string
	 */
	public function get_redirect_link(): string {
		if ( ! empty( $this->response['data']['url'] ) ) {
			return $this->response['data']['url'];
		}

		return '';
	}

	/**
	 * Get response data
	 *
	 * @return array
	 */
	public function get_response_data(): array {
		if ( ! empty( $this->response ) ) {
			return $this->response;
		}

		return array();
	}

	/**
	 * Execute command
	 *
	 * @param string $method Method.
	 * @param string $command Command.
	 * @param array  $params Params.
	 * @param bool   $json Is json.
	 *
	 * @return array
	 */
	private function execute( string $method, string $command, array $params = array(), bool $json = false ): ?array {
		$this->errors = array();

		if ( $method && $command ) {
			$url            = $this->server[ $this->env ] . $command;
			$args           = array(
				'body'        => wp_json_encode( $params ),
				'timeout'     => '5',
				'redirection' => '5',
				'httpversion' => '1.0',
				'headers'     => array(
					'Accept-Charset'         => 'utf-8',
					'Accept'                 => 'application/json',
					'Content-Type'           => 'application/json',
					'X-Processing-Key'       => $this->public_key,
					'X-Processing-Signature' => $this->generate_signature( $params ),
				),
			);
			$response       = wp_remote_post( $url, $args );
			$body           = json_decode( wp_remote_retrieve_body( $response ), true );
			$status_code    = wp_remote_retrieve_response_code( $response );
			$response_array = array();
			if ( ( $status_code >= 0 ) && ( $status_code < 200 ) ) {
				$this->errors[] = 'Server Not Found (' . $status_code . ')';
			}

			if ( ( $status_code >= 300 ) && ( $status_code < 400 ) ) {
				$this->errors[] = 'Page Redirect (' . $status_code . ')';
			}

			if ( ( $status_code >= 400 ) && ( $status_code < 500 ) ) {
				$this->errors[] = 'Page not found (' . $status_code . ')';
			}

			if ( $status_code >= 500 ) {
				$this->errors[] = 'Server Error (' . $status_code . ')';
			}

			if ( ! empty( $body ) ) {
				$response_array = $body;
			}

			$this->response = $response_array;

			return $response_array;
		}

		return null;
	}

	/**
	 * Build query
	 *
	 * @param array $params Params.
	 * @param bool  $json Is json.
	 *
	 * @return string
	 */
	private function build_query( array $params, bool $json ): string {
		if ( $json ) {
			return wp_json_encode( $params );
		} else {
			return http_build_query( $params );
		}
	}

	/**
	 * Validate callback
	 *
	 * @param array $data Data.
	 * @param array $headers Headers.
	 *
	 * @return bool
	 */
	public function validate_callback( array $data, array $headers ): bool {
		$validate = false;

		$signature = $this->generate_signature( $data );

		if ( ! empty( $headers[ $this->headers_key['public'] ] ) && ! empty( $headers[ $this->headers_key['signature'] ] ) ) {
			$callback_public    = $headers[ $this->headers_key['public'] ];
			$callback_signature = $headers[ $this->headers_key['signature'] ];
			if ( $callback_public === $this->public_key && $callback_signature === $signature ) {
				$validate = true;
			}
		}

		return $validate;
	}

	/**
	 * Get order id from foreign Id
	 *
	 * @param string $foreign_id foreign Id.
	 *
	 * @return int
	 */
	protected function get_order_id( string $foreign_id ): int {
		$foreign = explode( '_', $foreign_id );
		if ( ! empty( $foreign[1] ) ) {
			return (int) $foreign[1];
		}

		return 0;
	}

	/**
	 * Callback payment
	 *
	 * @param array $data Data.
	 * @param array $headers Headers.
	 *
	 * @return void
	 */
	public function coinspaid_callback( array $data, array $headers ) {

		if ( $this->validate_callback( $data, $headers ) ) {

			if ( $this->parse_response( $data ) ) {

				$order = wc_get_order( $this->order_id );
				if ( $order ) {
					if ( self::STATUS_SUCCESS === $this->response_status ) {
						$order->update_status( 'wc-completed' );
					} elseif ( self::STATUS_PENDING === $this->response_status ) {
						if ( $order->get_status() !== 'wc-completed' ) {
							$order->update_status( 'wc-pending' );
						}
					} else {
						$order->update_status( 'wc-failed' );
					}
				} else {
					CoinspaidLog::log( $this->log, 'Callback', 'Failed to parse response' );
				}
			} else {
				CoinspaidLog::log( $this->log, 'Callback', 'Failed to validate request' );
			}
		} else {
			CoinspaidLog::log( $this->log, 'Callback', 'Failed to validate request' );
		}
	}

	/**
	 * Parse callback params from response
	 *
	 * @param array $data Data response.
	 *
	 * @return bool
	 */
	private function parse_response( array $data ): bool {
		$error = false;
		if ( ! empty( $data['foreign_id'] ) ) {
			$this->order_id = $this->get_order_id( $data['foreign_id'] );
		} else {
			$error = true;
			CoinspaidLog::log( $this->log, 'Parse response', 'foreign_id is empty' );
		}
		if ( ! empty( $data['status'] ) ) {
			$this->response_status = $data['status'];
		} else {
			$error = true;
			CoinspaidLog::log( $this->log, 'Parse response', 'status is empty' );
		}

		return ! $error;
	}
}
