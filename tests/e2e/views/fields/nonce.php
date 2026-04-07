<?php
/**
 * Nonce - Kitchen Sink
 *
 * Tests the Nonce element + Nonce_Component renders a proper wp_nonce_field.
 */

use PinkCrab\Form_Components\Element\Nonce;
use PinkCrab\Form_Components\Component\Partial\Nonce_Component;
?>
<div id="e2e-nonce">

	<!-- Basic nonce via element -->
	<?php $this->component( new Nonce_Component(
		Nonce::make( 'test_form_action', 'test_form_nonce' )
	) ); ?>

	<!-- Nonce with different action/name -->
	<?php $this->component( new Nonce_Component(
		Nonce::make( 'save_settings', '_settings_nonce' )
	) ); ?>

</div>
