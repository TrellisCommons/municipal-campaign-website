<?php
/**
 * Plugin Name:       Donation Form
 * Description:       Example block scaffolded with Create Block tool.
 * Version:           0.1.0
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       donation-form
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
 * based on the registered block metadata. Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function create_block_donation_form_block_init() {
	wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
}
add_action( 'init', 'create_block_donation_form_block_init' );

/**
 * Register REST route for creating a Stripe PaymentIntent.
 */
function donation_form_register_routes() {
	register_rest_route(
		'donation-form/v1',
		'/create-payment-intent',
		array(
			'methods'             => 'POST',
			'callback'            => 'donation_form_create_payment_intent',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'donation_form_register_routes' );

/**
 * Create a Stripe PaymentIntent and return its client secret.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response
 */
function donation_form_create_payment_intent( WP_REST_Request $request ) {
	$secret_key = getenv( 'STRIPE_SECRET_KEY' );

	if ( ! $secret_key ) {
		return new WP_REST_Response( array( 'message' => 'Stripe is not configured.' ), 500 );
	}

	$amount = absint( $request->get_param( 'amount' ) );
	$name   = sanitize_text_field( $request->get_param( 'name' ) );
	$email  = sanitize_email( $request->get_param( 'email' ) );

	if ( $amount < 1 || empty( $name ) || empty( $email ) ) {
		return new WP_REST_Response( array( 'message' => 'Missing or invalid donation details.' ), 400 );
	}

	try {
		\Stripe\Stripe::setApiKey( $secret_key );

		$intent = \Stripe\PaymentIntent::create(
			array(
				'amount'   => $amount * 100, // Stripe expects cents.
				'currency' => 'usd',
				'receipt_email' => $email,
				'metadata' => array(
					'donor_name' => $name,
				),
			)
		);

		return new WP_REST_Response( array( 'clientSecret' => $intent->client_secret ), 200 );
	} catch ( \Exception $e ) {
		return new WP_REST_Response( array( 'message' => $e->getMessage() ), 500 );
	}
}
