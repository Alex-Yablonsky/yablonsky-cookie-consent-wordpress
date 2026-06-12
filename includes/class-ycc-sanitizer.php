<?php
/**
 * Settings sanitization helpers.
 *
 * @package YablonskyCookieConsent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitizer helper class.
 */
class YCC_Sanitizer {

	/**
	 * Sanitize checkbox value.
	 *
	 * @param array  $input Input array.
	 * @param string $key   Setting key.
	 * @return int
	 */
	public static function checkbox( $input, $key ) {
		return isset( $input[ $key ] ) ? 1 : 0;
	}

	/**
	 * Sanitize text value.
	 *
	 * @param array  $input   Input array.
	 * @param string $key     Setting key.
	 * @param string $default Default value.
	 * @return string
	 */
	public static function text( $input, $key, $default = '' ) {
		if ( ! isset( $input[ $key ] ) ) {
			return $default;
		}

		return sanitize_text_field( wp_unslash( $input[ $key ] ) );
	}

	/**
	 * Sanitize textarea value.
	 *
	 * @param array  $input   Input array.
	 * @param string $key     Setting key.
	 * @param string $default Default value.
	 * @return string
	 */
	public static function textarea( $input, $key, $default = '' ) {
		if ( ! isset( $input[ $key ] ) ) {
			return $default;
		}

		return sanitize_textarea_field( wp_unslash( $input[ $key ] ) );
	}

	/**
	 * Sanitize URL.
	 *
	 * @param array  $input Input array.
	 * @param string $key   Setting key.
	 * @return string
	 */
	public static function url( $input, $key ) {
		if ( ! isset( $input[ $key ] ) ) {
			return '';
		}

		return esc_url_raw( wp_unslash( $input[ $key ] ) );
	}

	/**
	 * Sanitize number with min and max.
	 *
	 * @param array  $input   Input array.
	 * @param string $key     Setting key.
	 * @param int    $min     Minimum value.
	 * @param int    $max     Maximum value.
	 * @param int    $default Default value.
	 * @return int
	 */
	public static function number( $input, $key, $min, $max, $default ) {
		if ( ! isset( $input[ $key ] ) ) {
			return $default;
		}

		$value = absint( $input[ $key ] );

		if ( $value < $min || $value > $max ) {
			return $default;
		}

		return $value;
	}

	/**
	 * Sanitize select value.
	 *
	 * @param array  $input   Input array.
	 * @param string $key     Setting key.
	 * @param array  $allowed Allowed values.
	 * @param string $default Default value.
	 * @return string
	 */
	public static function select( $input, $key, $allowed, $default ) {
		if ( ! isset( $input[ $key ] ) ) {
			return $default;
		}

		$value = sanitize_key( wp_unslash( $input[ $key ] ) );

		if ( ! in_array( $value, $allowed, true ) ) {
			return $default;
		}

		return $value;
	}

	/**
	 * Sanitize CSS text.
	 *
	 * @param array  $input Input array.
	 * @param string $key   Setting key.
	 * @return string
	 */
	public static function css( $input, $key ) {
		if ( ! isset( $input[ $key ] ) ) {
			return '';
		}

		$value = wp_unslash( $input[ $key ] );
		$value = wp_strip_all_tags( $value );

		return trim( $value );
	}

	/**
	 * Sanitize Google Tag Manager container ID.
	 *
	 * @param array  $input Input array.
	 * @param string $key   Setting key.
	 * @return string
	 */
	public static function gtm_id( $input, $key ) {
		if ( ! isset( $input[ $key ] ) ) {
			return '';
		}

		$value = strtoupper( sanitize_text_field( wp_unslash( $input[ $key ] ) ) );

		if ( '' === $value ) {
			return '';
		}

		if ( ! preg_match( '/^GTM-[A-Z0-9]+$/', $value ) ) {
			return '';
		}

		return $value;
	}
}
