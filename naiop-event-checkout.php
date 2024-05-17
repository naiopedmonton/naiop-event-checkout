<?php

/**
 * Plugin Name: NAIOP Event Checkout
 * Description: NAIOP Event Checkout
 * Author: Scott Dohei
 * Version: 2.6.0
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
add_action('init', 'create_block_naiop_event_checkout_block_init');

add_action('wp_enqueue_scripts', 'naiop_checkout_scripts');
function naiop_checkout_scripts() {
	wp_register_style('naiop-checkout', plugins_url('style.css', __FILE__ ));
    wp_enqueue_style('naiop-checkout');
}

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


function event_registration_fields($count) {
	for ($x = 0; $x < $count; $x++) {
		echo '<p class="form-row form-row-first validate-required" id="registration_name">';
			echo '<label for="name">Name&nbsp;<abbr class="required" title="required">*</abbr></label>';
			echo '<span class="woocommerce-input-wrapper">';
				echo '<input type="text" class="input-text " name="reg_name[' . $x . ']" id="name" placeholder="" value="" autocomplete="given-name">';
			echo '</span>';
		echo '</p>';
		echo '<p class="form-row form-row-first validate-required" id="registration_email">';
			echo '<label for="email">Email&nbsp;<abbr class="required" title="required">*</abbr></label>';
			echo '<span class="woocommerce-input-wrapper">';
				echo '<input type="text" class="input-text " name="reg_email[' . $x . ']" id="email" placeholder="" value="" autocomplete="given-name">';
			echo '</span>';
		echo '</p>';
	}
}

function course_demographic_check($value) {
	echo '<p class="form-row check-row">';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<label class="checkbox">';
				echo '<input type="checkbox" name="naiop_demo" value="' . $value . '">';
				echo ' ' . $value;
			echo '</label>';
		echo '</span>';
	echo '</p>';
}

function course_fields() {
	echo '<p>Please wait 2-3 business days for us to contact you with next steps.</p>';
	echo '<p class="form-row" style="margin-bottom:0;">';
		echo '<label for="demo">I am (select all that apply):&nbsp;<abbr class="required" title="required">*</abbr></label>';
		echo '<div style="clear: both; padding-left: 2rem;">';
			course_demographic_check("Employed at a brokerage");
			course_demographic_check("Employed in commercial real estate");
			course_demographic_check("Employed in an industry related to commercial real estate");
			course_demographic_check("Working outside of real estate and looking to change careers");
			course_demographic_check("A post-secondary student");
			course_demographic_check("Other");
		echo '</div>';
	echo '</p>';

	echo '<div class="form-row" style="margin-bottom:0;">';
		echo '<label for="broker">Are you planning to take the RECA licensing exam (to become a broker)? Please note, if you select ‘yes’ you must already have a RECA CON-ID prior to purchasing this course and will need to provide the number below. Don’t have a RECA CON-ID yet? Get yours <a href="https://public.myreca.ca/">here</a>.&nbsp;<abbr class="required" title="required">*</abbr></label>';
		echo '<div style="clear: both; padding-left: 2rem; display: flex; gap: 20px">';
			echo '<label class="checkbox">';
				echo '<input type="radio" name="naiop_broker" value="Yes"> Yes';
			echo '</label>';
			echo '<label class="checkbox">';
				echo '<input type="radio" name="naiop_broker" value="No"> No';
			echo '</label>';
		echo '</div>';
	echo '</div>';

	echo '<p class="form-row form-row-first validate-required" id="naiop_fname">';
		echo '<label for="naiop_fname">First Name&nbsp;<abbr class="required" title="required">*</abbr></label>';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<input type="text" class="input-text " name="naiop_fname" id="naiop_fname" placeholder="" value="" autocomplete="reg-fname">';
		echo '</span>';
	echo '</p>';
	echo '<p class="form-row form-row-last validate-required" id="naiop_lname">';
		echo '<label for="naiop_lname">Last Name&nbsp;<abbr class="required" title="required">*</abbr></label>';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<input type="text" class="input-text" name="naiop_lname" id="naiop_lname" placeholder="" value="" autocomplete="reg-lname">';
		echo '</span>';
	echo '</p>';
	echo '<p class="form-row validate-required" id="naiop_email">';
		echo '<label for="naiop_email">Email&nbsp;<abbr class="required" title="required">*</abbr></label>';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<input type="text" class="input-text" name="naiop_email" id="naiop_email" placeholder="" value="" autocomplete="reg-email">';
		echo '</span>';
	echo '</p>';
	echo '<p class="form-row validate-required" id="naiop_phone">';
		echo '<label for="naiop_phone">Phone&nbsp;<abbr class="required" title="required">*</abbr></label>';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<input type="text" class="input-text" name="naiop_phone" id="naiop_phone" placeholder="" value="" autocomplete="reg-phone">';
		echo '</span>';
	echo '</p>';
	echo '<p class="form-row validate-required" id="naiop_company">';
		echo '<label for="naiop_company">Company (optional)</label>';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<input type="text" class="input-text" name="naiop_company" id="naiop_company" placeholder="" value="" autocomplete="reg-company">';
		echo '</span>';
	echo '</p>';

	echo '<p class="form-row validate-required" id="naiop_reca_id">';
		echo '<label for="naiop_reca_id">RECA CON-ID (optional)</label>';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<input type="text" class="input-text " name="naiop_reca_id" id="naiop_reca_id" placeholder="" value="" autocomplete="reca-con-id">';
		echo '</span>';
	echo '</p>';
}

function is_event_cart_item($cart_item) {
	return false;
	//return ($cart_item['quantity'] > 0);
}

function is_course_cart_item($cart_item) {
	$course_product_ids = array(955299, 6576, 6578); //dohei-test, prod, prod
	return ($cart_item['quantity'] > 0 && in_array($cart_item['product_id'], $course_product_ids));
}

function cart_requires_course_registration() {
	$course_in_cart = false;
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		if (is_course_cart_item($cart_item)) {
			return true;
		}
	}
	return false;
}

add_filter('woocommerce_checkout_after_customer_details', 'naiop_checkout_end', 10);
function naiop_checkout_end() {
	echo "<div class='col2-set' id='naiop-registration'>";
		$course_in_cart = false;
		$event_registrations = 0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$course_in_cart = is_course_cart_item($cart_item);
			$event_registrations += (is_event_cart_item($cart_item) ? $cart_item['quantity'] : 0);
		}

		if ($course_in_cart) {
			echo '<h3>Course Registration</h3>';
			course_fields();
		}

		if ($event_registrations > 0) {
			echo '<h3>Event Registration</h3>';
			event_registration_fields($event_registrations);
		}
	echo "</div>";
}

add_action('woocommerce_cart_calculate_fees', 'discount_real_estate_bundle', 25, 1);
function discount_real_estate_bundle($cart) {
	if (is_admin() && ! defined( 'DOING_AJAX' )) return;

	$course_count = 0;
	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
		if (is_course_cart_item($cart_item)) {
			$course_count++;
		}
	}

	if ($course_count >= 2) {
		$cart->add_fee(__('Real Estate Bundle Discount', 'naiop-checkout'), -99 );
	}
}

add_action( 'woocommerce_after_checkout_validation', 'naiop_checkout_validation', 20, 2 );
function naiop_checkout_validation($data, $errors) {
	if (cart_requires_course_registration()) {
		if (!isset($_POST['naiop_demo']) || strlen($_POST["naiop_demo"]) <= 0) {
			$errors->add('validation', __('Please indicate your current employment.'));
		}
		if (!isset($_POST['naiop_broker']) || strlen($_POST["naiop_broker"]) <= 0) {
			$errors->add('validation', __('Please indicate whether or not you intend to take the RECA licensing exam.'));
		}
		if (strlen($_POST["naiop_fname"]) <= 0) {
			$errors->add('validation', __('Please enter a first name for Course Registration.'));
		}
		if (strlen($_POST["naiop_lname"]) <= 0) {
			$errors->add('validation', __('Please enter a last name for Course Registration.'));
		}
		if (strlen($_POST["naiop_email"]) <= 0) {
			$errors->add('validation', __('Please enter an email address for Course Registration.'));
		}
		if (strlen($_POST["naiop_phone"]) <= 0) {
			$errors->add('validation', __('Please enter a phone number for Course Registration.'));
		}
	}
	//error_log(print_r($data, true));
	//error_log(print_r($_POST, true));
}
