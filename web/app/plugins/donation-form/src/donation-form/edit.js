import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';

export default function Edit() {
	return (
		<div { ...useBlockProps() }>
			<p><strong>{ __( 'Donation Form', 'donation-form' ) }</strong></p>
			<p>{ __( 'The live donation form (name, email, address, amount, and card field) will appear here on the published page.', 'donation-form' ) }</p>
		</div>
	);
}
