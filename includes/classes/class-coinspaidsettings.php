<?php
/**
 * Description Coinspaid setting class
 *
 * @package       COINSPAID Payment Module for WooCommerce
 * @copyright     (c) 2022 COINSPAID. All rights reserved.
 * @license       BSD 2 License
 */

/**
 * Settings class
 */
class CoinspaidSettings {

	const PLUGIN_NAME = 'woocommerce_coinspaid_settings';

	/**
	 * Return public key
	 */
	public static function get_public_key() {
		$plugin_options = get_option( self::PLUGIN_NAME );

		if ( self::is_sandbox() ) {
			if ( ! empty( $plugin_options['sandbox_public_key'] ) ) {
				return $plugin_options['sandbox_public_key'];
			}
		} else {

			if ( ! empty( $plugin_options['public_key'] ) ) {
				return $plugin_options['public_key'];
			}
		}

		return false;
	}

	/**
	 * Return secret key
	 */
	public static function get_secret_key() {
		$plugin_options = get_option( self::PLUGIN_NAME );

		if ( self::is_sandbox() ) {
			if ( ! empty( $plugin_options['sandbox_secret_key'] ) ) {
				return $plugin_options['sandbox_secret_key'];
			}
		} else {
			if ( ! empty( $plugin_options['secret_key'] ) ) {
				return $plugin_options['secret_key'];
			}
		}

		return false;
	}

	/**
	 * Is enable logging
	 */
	public static function is_logging() {
		$plugin_options = get_option( self::PLUGIN_NAME );
		$is_logging     = false;
		if ( ! empty( $plugin_options['enabled_log'] ) && 'yes' === $plugin_options['enabled_log'] ) {
			$is_logging = true;
		}

		return $is_logging;
	}

	/**
	 * Is enable sandbox
	 */
	public static function is_sandbox() {
		$plugin_options = get_option( self::PLUGIN_NAME );
		$is_sandbox     = false;
		if ( ! empty( $plugin_options['sandbox_enabled'] ) && 'yes' === $plugin_options['sandbox_enabled'] ) {
			$is_sandbox = true;
		}

		return $is_sandbox;
	}

	/**
	 * Get env
	 */
	public static function get_env() {
		$plugin_options = get_option( self::PLUGIN_NAME );
		$env            = 'production';
		if ( ! empty( $plugin_options['sandbox_enabled'] ) && 'yes' === $plugin_options['sandbox_enabled'] ) {
			$env = 'sandbox';
		}

		return $env;
	}

	/**
	 * Is time restriction
	 */
	public static function is_time_restriction() {
		$plugin_options      = get_option( self::PLUGIN_NAME );
		$is_time_restriction = false;
		if ( ! empty( $plugin_options['time_restriction'] ) && 'yes' === $plugin_options['time_restriction'] ) {
			$is_time_restriction = true;
		}

		return $is_time_restriction;
	}
}

