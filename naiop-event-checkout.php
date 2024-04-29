<?php

/**
 * Plugin Name: NAIOP Event Checkout
 * Description: NAIOP Event Checkout
 * Author: Scott Dohei
 * Version: 1.3.0
 * Plugin URI: https://github.com/naiopedmonton/naiop-event-checkout
 * GitHub Plugin URI: https://github.com/naiopedmonton/naiop-event-checkout
 * Text Domain: naiop-event-checkout
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/license/gpl-2.0.txt
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

add_action('woocommerce_blocks_loaded',
	function() {
		__experimental_woocommerce_blocks_register_checkout_field(
			array(
				'id'            => 'naiop/diet',
				'label'         => 'Dietary restrictions or preferences',
				'location'      => 'additional',
				'required'      => false,
				'attributes'    => array(
					'autocomplete' => 'dietary-restrictions'
				),
			),
		);
		__experimental_woocommerce_blocks_register_checkout_field(
			array(
				'id'            => 'naiop/partner-id',
				'label'         => 'NAIOP Edmonton Partner',
				'location'      => 'additional',
				'required'      => false,
				'attributes'    => array(
					'autocomplete' => 'partner-name'
				),
			),
		);

		/*add_action('_experimental_woocommerce_blocks_sanitize_additional_field',
			function ( $field_value, $field_key ) {
				if ( 'naiop/partner-id' === $field_key ) {}
				return $field_value;
			}, 10, 2);

		add_action('__experimental_woocommerce_blocks_validate_additional_field',
			function ( WP_Error $errors, $field_key, $field_value ) {
				if ( 'naiop/partner-id' === $field_key ) {
					//$errors->add('invalid_partner', 'Please check partner');
				}
				return $error;
			}, 10, 3);*/
	}
);

add_action('__experimental_woocommerce_blocks_validate_location_address_fields',
	function ( \WP_Error $errors, $fields, $group ) {
		if ( $fields['namespace/gov-id'] !== $fields['namespace/confirm-gov-id'] ) {
			$errors->add( 'gov_id_mismatch', 'Please ensure your government ID matches the confirmation.' );
		}
	}, 10, 3);



add_filter('woocommerce_locate_template', 'locate_order_email_template');
function naiop_custom_sidebar_panels($template, $template_name, $template_path) {
	if ($template_name === "emails/customer-processing-order.php") {
		$template = 'wp-content/plugins/naiop-event-checkout/woocommerce/emails/customer-processing-order.php';
	}
    return $template;
}