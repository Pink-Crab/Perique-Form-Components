<?php
/**
 * Month Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Month;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Month::make( 'birth_month' )
			->label( 'Birth Month' )
	) ); ?>
</div>

<div class="doc-example" id="value">
	<p class="doc-example-title">With Value</p>
	<?php $this->component( new Input_Component(
		Month::make( 'expiry' )
			->label( 'Expiry Month' )
			->set_existing( '2026-06' )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">Disabled</p>
	<?php $this->component( new Input_Component(
		Month::make( 'locked_month' )
			->label( 'Locked' )
			->set_existing( '2026-01' )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">With Notification</p>
	<?php $this->component( new Input_Component(
		Month::make( 'expired_month' )
			->label( 'Expiry' )
			->set_existing( '2020-01' )
			->error_notification( 'This month has expired.' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Month::make( 'wrapped_month' )
			->label( 'Billing Month' )
			->before( '<span style="color:#6b7280;font-size:13px;">Select billing period</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Charges applied at month end</span>' )
	) ); ?>
</div>
