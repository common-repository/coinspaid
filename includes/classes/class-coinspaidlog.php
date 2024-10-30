<?php
/**
 * Description Coinspaid logged class
 *
 * @package       COINSPAID Payment Module for WooCommerce
 * @copyright     (c) 2022 COINSPAID. All rights reserved.
 * @license       BSD 2 License
 */

/**
 * Logging class
 */
class CoinspaidLog {

	/**
	 * Log error
	 *
	 * @param bool   $enable Enabled.
	 * @param string $source Source.
	 * @param array  $data Data for log.
	 */
	public static function log( $enable = false, $source = 'coinspaid-payment', $data = array() ) {
		if ( ! $enable ) {
			return;
		}

		if ( is_array( $data ) ) {
			$data = wp_json_encode( $data );
		}

		$logger = wc_get_logger();

		$logger->info( $data, array( 'source' => $source ) );

	}
}
