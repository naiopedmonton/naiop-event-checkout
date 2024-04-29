<?php
/**
 * Plugin Name:       NAIOP Event Checkout
 * Description:       NAIOP Event Checkout
 * GitHub Plugin URI:   https://github.com/TODO *****
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Scott Dohei
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       naiop-event-checkout
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_naiop_event_checkout_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_naiop_event_checkout_block_init' );

add_action(
	'woocommerce_blocks_loaded',
	function() {
        __experimental_woocommerce_blocks_register_checkout_field(
			array(
				'id'            => 'naiop/partner',
				'label'         => 'NAIOP EPartner?',
                'type'          => 'checkbox',
				'location'      => 'additional',
				'required'      => true,
				'attributes'    => array(),
			),
		);
		__experimental_woocommerce_blocks_register_checkout_field(
			array(
				'id'            => 'namespace/partner-id',
				'label'         => 'NAIOP Edmonton Partner',
				'location'      => 'additional',
				'required'      => true,
				'attributes'    => array(
					'autocomplete' => 'partner-id'
				),
			),
		);
		/*__experimental_woocommerce_blocks_register_checkout_field(
			array(
				'id'            => 'namespace/confirm-gov-id',
				'label'         => 'Confirm government ID',
				'location'      => 'additional',
				'required'      => true,
				'attributes'    => array(
					'autocomplete' => 'government-id',
					'pattern'      => '[A-Z0-9]{5}', // A 5-character string of capital letters and numbers.
					'title'        => 'Confirm your 5-digit Government ID',
				),
			),
		);*/

		add_action(
			'_experimental_woocommerce_blocks_sanitize_additional_field',
			function ( $field_value, $field_key ) {
				/*if ( 'namespace/gov-id' === $field_key || 'namespace/confirm-gov-id' === $field_key ) {
					$field_value = str_replace( ' ', '', $field_key );
					$field_value = strtoupper( $field_value );
				}*/
				return $field_value;
			},
			10,
			2
		);

		add_action(
		'__experimental_woocommerce_blocks_validate_additional_field',
			function ( WP_Error $errors, $field_key, $field_value ) {
				/*if ( 'namespace/gov-id' === $field_key ) {
					$match = preg_match( '/[A-Z0-9]{5}/', $field_value );
					if ( 0 === $match || false === $match ) {
						$errors->add( 'invalid_gov_id', 'Please ensure your government ID matches the correct format.' );
					}
				}*/
				return $error;
			},
			10,
			3
		);
	}
);

add_action(
	'__experimental_woocommerce_blocks_validate_location_address_fields',
	function ( \WP_Error $errors, $fields, $group ) {
		if ( $fields['namespace/gov-id'] !== $fields['namespace/confirm-gov-id'] ) {
			$errors->add( 'gov_id_mismatch', 'Please ensure your government ID matches the confirmation.' );
		}
	},
	10,
	3
);
