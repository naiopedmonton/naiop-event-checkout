<?php

/**
 * Plugin Name: NAIOP Edmonton customizations
 * Description: NAIOP Edmonton customizations
 * Author: Scott Dohei
 * Version: 4.1.0
 * Plugin URI: https://github.com/naiopedmonton/naiop-event-checkout
 * GitHub Plugin URI: https://github.com/naiopedmonton/naiop-event-checkout
 * Text Domain: naiop-event-checkout
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/license/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* 2024-06-02: redirect /single-post/beyondbrickandmortar */
add_action( 'init', 'beyond_brick_redirect' );
function beyond_brick_redirect() {
    add_rewrite_rule( 'single-post/beyondbrickandmortar$', 'beyond-brick-and-mortar', 'top' );
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

add_filter('woocommerce_locate_template', 'locate_order_email_template', 10, 4);
function locate_order_email_template($template, $template_name, $template_path) {
	if ('customer-processing-order.php' === basename($template)){
		$template = trailingslashit(plugin_dir_path( __FILE__ )) . 'woocommerce/emails/customer-processing-order.php';
	}
	if ('admin-new-order.php' === basename($template)){
		$template = trailingslashit(plugin_dir_path( __FILE__ )) . 'woocommerce/emails/admin-new-order.php';
	}
	return $template;
}

function event_registration_fields($count) {
	for ($x = 0; $x < $count; $x++) {
		echo '<p class="form-row form-row-first validate-required">';
			echo '<label for="naiop_event_fname' . $x . '">First Name&nbsp;<abbr class="required" title="required">*</abbr></label>';
			echo '<span class="woocommerce-input-wrapper">';
				echo '<input type="text" class="input-text " name="naiop_event_fname[]" id="naiop_event_fname' . $x . '" placeholder="" value="" autocomplete="naiop_event_fname">';
			echo '</span>';
		echo '</p>';
		echo '<p class="form-row form-row-first validate-required">';
			echo '<label for="naiop_event_lname' . $x . '">Last Name&nbsp;<abbr class="required" title="required">*</abbr></label>';
			echo '<span class="woocommerce-input-wrapper">';
				echo '<input type="text" class="input-text " name="naiop_event_lname[]" id="naiop_event_lname' . $x . '" placeholder="" value="" autocomplete="naiop_event_lname">';
			echo '</span>';
		echo '</p>';
		echo '<p class="form-row validate-required">';
			echo '<label for="naiop_event_email' . $x . '">Email&nbsp;<abbr class="required" title="required">*</abbr></label>';
			echo '<span class="woocommerce-input-wrapper">';
				echo '<input type="text" class="input-text " name="naiop_event_email[]" id="naiop_event_email' . $x . '" placeholder="" value="" autocomplete="naiop_event_email">';
			echo '</span>';
		echo '</p>';
		echo '<p class="form-row">';
			echo '<label for="naiop_event_diet' . $x . '">Dietary Restrictions (optional)</label>';
			echo '<span class="woocommerce-input-wrapper">';
				echo '<input type="text" class="input-text " name="naiop_event_diet[]" id="naiop_event_diet' . $x . '" placeholder="" value="" autocomplete="naiop_event_diet">';
			echo '</span>';
		echo '</p>';
	}
}

function course_demographic_check($value) {
	echo '<p class="form-row check-row">';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<label class="checkbox">';
				echo '<input type="checkbox" name="naiop_demo[]" value="' . $value . '">';
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
	echo '<p class="form-row" id="naiop_company">';
		echo '<label for="naiop_company">Company (optional)</label>';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<input type="text" class="input-text" name="naiop_company" id="naiop_company" placeholder="" value="" autocomplete="reg-company">';
		echo '</span>';
	echo '</p>';

	echo '<p class="form-row" id="naiop_reca_id">';
		echo '<label for="naiop_reca_id">RECA CON-ID (optional)</label>';
		echo '<span class="woocommerce-input-wrapper">';
			echo '<input type="text" class="input-text " name="naiop_reca_id" id="naiop_reca_id" placeholder="" value="" autocomplete="reca-con-id">';
		echo '</span>';
	echo '</p>';
}

function count_cart_event_registrations() {
	$event_registrations = 0;
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$event_registrations += count_cart_item_event_registrations($cart_item);
	}
	return $event_registrations;
}

function count_cart_item_event_registrations($cart_item) {
	$event_category = "Events";
	if (function_exists('mt_get_settings')) {
		$options  = mt_get_settings();
		$event_category = $options['naiop_ticket_cat'];
	}
	if (has_term($event_category, 'product_cat', $cart_item['product_id']) && $cart_item['quantity'] > 0) {
		// TODO: lookup seats for this product_id
		$seats_per_ticket = 1;
		return $cart_item['quantity'] * $seats_per_ticket;
	}

	return 0;
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
		if (cart_requires_course_registration()) {
			echo '<h3>Course Registration</h3>';
			course_fields();
		}

		$event_registrations = count_cart_event_registrations();
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
		if (!isset($_POST['naiop_demo']) || !is_array($_POST["naiop_demo"]) || count($_POST["naiop_demo"]) <= 0) {
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

	$event_registrations = count_cart_event_registrations();
	if ($event_registrations > 0) {
		if (!isset($_POST['naiop_event_fname']) || !is_array($_POST["naiop_event_fname"]) || count($_POST["naiop_event_fname"]) < $event_registrations) {
			$errors->add('validation', __('Wrong number of Event Registrations.'));
		}
		foreach ($_POST["naiop_event_fname"] as $name_val) {
			if (strlen(trim($name_val)) <= 0) {
				$errors->add('validation', __('Missing first name for some Event Registrations.'));
				break;
			}
		}

		if (!isset($_POST['naiop_event_lname']) || !is_array($_POST["naiop_event_lname"]) || count($_POST["naiop_event_lname"]) < $event_registrations) {
			$errors->add('validation', __('Wrong number of Event Registrations.'));
		}
		foreach ($_POST["naiop_event_lname"] as $name_val) {
			if (strlen(trim($name_val)) <= 0) {
				$errors->add('validation', __('Missing last name for some Event Registrations.'));
				break;
			}
		}
		
		if (!isset($_POST['naiop_event_email']) || !is_array($_POST["naiop_event_email"]) || count($_POST["naiop_event_email"]) < $event_registrations) {
			$errors->add('validation', __('Wrong number of Event Registrations.'));
		}
		foreach ($_POST["naiop_event_email"] as $name_val) {
			if (strlen(trim($name_val)) <= 0) {
				$errors->add('validation', __('Missing email for some Event Registrations.'));
				break;
			}
		}

		if (!isset($_POST['naiop_event_diet']) || !is_array($_POST["naiop_event_diet"]) || count($_POST["naiop_event_diet"]) < $event_registrations) {
			$errors->add('validation', __('Wrong number of Event Registrations.'));
		}
	}
}

add_action('woocommerce_checkout_create_order', 'update_order_registrations', 22, 2);
function update_order_registrations($order, $data) {
	if (cart_requires_course_registration()) {
		foreach ($_POST["naiop_demo"] as $demo_val) {
			$order->add_meta_data("naiop_demo", $demo_val, false);
		}

		$keys = array("naiop_broker", "naiop_fname", "naiop_lname", "naiop_email", "naiop_phone", "naiop_reca_id", "naiop_company");
		foreach ($keys as $key) {
			$order->update_meta_data($key, isset($_POST[$key]) ? $_POST[$key] : "");
		}
	}

	$event_registrations = count_cart_event_registrations();
	if ($event_registrations > 0) {
		foreach ($_POST["naiop_event_fname"] as $name_val) {
			$order->add_meta_data("naiop_event_fname", $name_val, false);
		}
		foreach ($_POST["naiop_event_lname"] as $name_val) {
			$order->add_meta_data("naiop_event_lname", $name_val, false);
		}
		foreach ($_POST["naiop_event_email"] as $name_val) {
			$order->add_meta_data("naiop_event_email", $name_val, false);
		}
		foreach ($_POST["naiop_event_diet"] as $name_val) {
			$order->add_meta_data("naiop_event_diet", $name_val, false);
		}
	}
}

function debug_echo($s) {
	//error_log("debug echo: " . $s);
	echo $s;
}

function naiop_email_order_registration($order, $sent_to_admin, $plain_text, $email) {
	if (cart_requires_course_registration()) {
		debug_echo('<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding: 0;" width="100%">');
			debug_echo('<tr><td valign="top" style="text-align: left; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;" align="left">');
				debug_echo('<h2 style=\'display: block; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;\'>Course Registration</h2>');
				debug_echo('<div class="address" style="padding: 12px; color: #636363; border: 1px solid #e5e5e5;">');
					// required inputs
					$demos = array();
					foreach ($order->get_meta('naiop_demo', false) as $demo_key => $obj_value) {
						array_push($demos, $obj_value->get_data()['value']);
					}
					debug_echo('<strong>Demographic:</strong> ' . 			implode(", ", $demos) . '<br>');
					debug_echo('<strong>Taking the RECA exam:</strong> ' . 	$order->get_meta('naiop_broker', true) . '<br>');
					debug_echo('<strong>First Name:</strong> ' . 			$order->get_meta('naiop_fname', true) . '<br>');
					debug_echo('<strong>Last Name:</strong> ' . 			$order->get_meta('naiop_lname', true) . '<br>');
					debug_echo('<strong>Email:</strong> ' . 				$order->get_meta('naiop_email', true) . '<br>');
					debug_echo('<strong>Phone:</strong> ' . 				$order->get_meta('naiop_phone', true) . '<br>');
					
					// optional inputs
					$reca_id = $order->get_meta('naiop_reca_id', true);
					if (strlen(trim($reca_id)) > 0) {
						debug_echo('<strong>RECA CON-ID:</strong> ' . 	$reca_id . '<br>');
					}
					$company = $order->get_meta('naiop_company', true);
					if (strlen(trim($company)) > 0) {
						debug_echo('<strong>Company:</strong> ' . 		$company . '<br>');
					}
				debug_echo('</div>');
			debug_echo('</td></tr>');
		debug_echo('</table>');
	}

	$event_registrations = count_cart_event_registrations();
	if ($event_registrations > 0) {
		debug_echo('<table id="registrations" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding: 0;" width="100%">');
			debug_echo('<tr><td valign="top" style="text-align: left; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;" align="left">');
				debug_echo('<h2 style=\'display: block; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;\'>Event Registration</h2>');
				debug_echo('<div class="address" style="padding: 12px; color: #636363; border: 1px solid #e5e5e5;">');
					// required inputs
					$first_name_arr = array();
					foreach ($order->get_meta('naiop_event_fname', false) as $key => $obj_value) {
						array_push($first_name_arr, $obj_value->get_data()['value']);
					}
					$last_name_arr = array();
					foreach ($order->get_meta('naiop_event_lname', false) as $key => $obj_value) {
						array_push($last_name_arr, $obj_value->get_data()['value']);
					}
					$email_arr = array();
					foreach ($order->get_meta('naiop_event_email', false) as $key => $obj_value) {
						array_push($email_arr, $obj_value->get_data()['value']);
					}
					$diet_arr = array();
					foreach ($order->get_meta('naiop_event_diet', false) as $key => $obj_value) {
						array_push($diet_arr, $obj_value->get_data()['value']);
					}

					for ($x = 0; $x < $event_registrations; $x++) {
						if (!isset($first_name_arr[$x]) || !isset($last_name_arr[$x]) || !isset($email_arr[$x])) {
							error_log("Event registration first name is missing @ " . $x . ". Expected " . $event_registrations);
							break;
						}

						debug_echo('<strong>Name:</strong> ' . 		$first_name_arr[$x] . " " . $last_name_arr[$x] . '<br>');
						debug_echo('<strong>Email:</strong> ' . 	$email_arr[$x]);
						$diet = $diet_arr[$x];
						if (strlen($diet) > 0) {
							debug_echo('<br><strong>Dietary Restrictions:</strong> ' . 	$diet);
						}
						if (($x + 1) < $event_registrations) {
							debug_echo('<hr style="border-width: 0;">');
						}
					}
				debug_echo('</div>');
			debug_echo('</td></tr>');
		debug_echo('</table>');
	}
}
add_action('naiop_email_order_registration', 'naiop_email_order_registration', 10, 4);