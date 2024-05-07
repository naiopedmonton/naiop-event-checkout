<?php

/**
 * Plugin Name: NAIOP Event Checkout
 * Description: NAIOP Event Checkout
 * Author: Scott Dohei
 * Version: 1.7.0
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

/*add_action('__experimental_woocommerce_blocks_validate_location_address_fields',
	function ( \WP_Error $errors, $fields, $group ) {
		if ( $fields['namespace/gov-id'] !== $fields['namespace/confirm-gov-id'] ) {
			$errors->add( 'gov_id_mismatch', 'Please ensure your government ID matches the confirmation.' );
		}
	}, 10, 3);*/



/*add_filter('woocommerce_locate_template', 'locate_order_email_template', 10, 3);
function locate_order_email_template($template, $template_name, $template_path) {
	if ($template_name === "emails/customer-processing-order.php") {
		$template = 'wordpress/plugins/naiop-event-checkout/woocommerce/emails/customer-processing-order.php';
	}
    return $template;
}*/

add_filter('woocommerce_locate_template', 'locate_order_email_template', 10, 4);
function locate_order_email_template($template, $template_name, $template_path) {
	if ('customer-processing-order.php' === basename($template)){
		$template = trailingslashit(plugin_dir_path( __FILE__ )) . 'woocommerce/emails/customer-processing-order.php';
	}
	return $template;
}

function event_registration_fields($index) {
	echo '<p class="form-row form-row-first validate-required" id="registration_name">';
		echo '<label for="name">Name&nbsp;<abbr class="required" title="required">*</abbr></label>';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<input type="text" class="input-text " name="reg_name[' . $index . ']" id="name" placeholder="" value="" autocomplete="given-name">';
		echo '</span>';
	echo '</p>';
	echo '<p class="form-row form-row-first validate-required" id="registration_email">';
		echo '<label for="email">Email&nbsp;<abbr class="required" title="required">*</abbr></label>';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<input type="text" class="input-text " name="reg_email[' . $index . ']" id="email" placeholder="" value="" autocomplete="given-name">';
		echo '</span>';
	echo '</p>';
}

add_filter('woocommerce_checkout_after_customer_details', 'naiop_checkout_end', 10);
function naiop_checkout_end() {
	//error_log(print_r($posted, true));
	//error_log('sss'.print_r($something, true));
	echo '<div class="col2-set">';
		echo '<h3>Event Registration</h3>';
		$index = 0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ($cart_item['quantity'] > 0) { // TODO: is an event?
				for ($x = 0; $x < $cart_item['quantity']; $x++) {
					// TODO: multiple seats?
					//event_registration_fields($index);
					$index++;
				}
				echo 'TODO: register ' . $index . ' people here';
			}
		}
	echo '</div>';
}

