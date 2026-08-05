document.addEventListener( 'DOMContentLoaded', function () {
	const form = document.getElementById( 'donation-form' );
	if ( ! form || typeof Stripe === 'undefined' || ! window.donationFormData ) {
		return;
	}

	const stripe = Stripe( window.donationFormData.publishableKey );
	const elements = stripe.elements();
	const cardElement = elements.create( 'card' );
	cardElement.mount( '#donation-form-card-element' );

	const errorsDiv = document.getElementById( 'donation-form-errors' );
	const submitButton = document.getElementById( 'donation-form-submit' );
	const successDiv = document.getElementById( 'donation-form-success' );

	form.addEventListener( 'submit', async function ( event ) {
		event.preventDefault();
		errorsDiv.textContent = '';
		submitButton.disabled = true;
		submitButton.textContent = 'Processing…';

		const amount = document.getElementById( 'donation-form-amount' ).value;
		const name = document.getElementById( 'donation-form-name' ).value;
		const email = document.getElementById( 'donation-form-email' ).value;
		const address = document.getElementById( 'donation-form-address' ).value;
		const city = document.getElementById( 'donation-form-city' ).value;
		const postal = document.getElementById( 'donation-form-postal' ).value;

		try {
			const response = await fetch( window.donationFormData.restUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': window.donationFormData.nonce,
				},
				body: JSON.stringify( { amount, name, email } ),
			} );

			const data = await response.json();

			if ( ! response.ok || ! data.clientSecret ) {
				throw new Error( data.message || 'Unable to start payment.' );
			}

			const result = await stripe.confirmCardPayment( data.clientSecret, {
				payment_method: {
					card: cardElement,
					billing_details: {
						name,
						email,
						address: {
							line1: address,
							city,
							postal_code: postal,
						},
					},
				},
			} );

			if ( result.error ) {
				throw new Error( result.error.message );
			}

			form.style.display = 'none';
			successDiv.style.display = 'block';
		} catch ( err ) {
			errorsDiv.textContent = err.message;
			submitButton.disabled = false;
			submitButton.textContent = 'Donate';
		}
	} );
} );
