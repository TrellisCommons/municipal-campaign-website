<?php
wp_enqueue_script(
	'donation-form-stripe-js',
	'https://js.stripe.com/v3/',
	array(),
	null,
	true
);

wp_localize_script(
	'create-block-donation-form-view-script',
	'donationFormData',
	array(
		'publishableKey' => getenv( 'STRIPE_PUBLISHABLE_KEY' ),
		'restUrl'        => esc_url_raw( rest_url( 'donation-form/v1/create-payment-intent' ) ),
		'nonce'          => wp_create_nonce( 'wp_rest' ),
	)
);
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<form class="donation-form" id="donation-form">
		<div class="donation-form__field">
			<label for="donation-form-amount">Donation Amount (USD)</label>
			<input type="number" id="donation-form-amount" name="amount" min="1" step="1" value="25" required />
		</div>
		<div class="donation-form__field">
			<label for="donation-form-name">Full Name</label>
			<input type="text" id="donation-form-name" name="name" required />
		</div>
		<div class="donation-form__field">
			<label for="donation-form-email">Email Address</label>
			<input type="email" id="donation-form-email" name="email" required />
		</div>
		<div class="donation-form__field">
			<label for="donation-form-address">Address</label>
			<input type="text" id="donation-form-address" name="address" required />
		</div>
		<div class="donation-form__field">
			<label for="donation-form-city">City</label>
			<input type="text" id="donation-form-city" name="city" required />
		</div>
		<div class="donation-form__field">
			<label for="donation-form-postal">Postal Code</label>
			<input type="text" id="donation-form-postal" name="postal" required />
		</div>
		<div class="donation-form__field">
			<label>Card Details</label>
			<div id="donation-form-card-element"></div>
		</div>
		<div id="donation-form-errors" role="alert"></div>
		<button type="submit" id="donation-form-submit">Donate</button>
	</form>
	<div id="donation-form-success" style="display:none;">Thank you for your donation!</div>
</div>
