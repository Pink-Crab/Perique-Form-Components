<?php
/**
 * Checkbox Input - Documentation Examples
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Checkbox;
?>
<div class="doc-example" id="unchecked">
	<p class="doc-example-title">Basic (unchecked)</p>
	<?php $this->component( new Input_Component(
		Checkbox::make( 'agree' )
			->label( 'I agree to the terms' )
	) ); ?>
</div>

<div class="doc-example" id="checked">
	<p class="doc-example-title">checked()</p>
	<?php $this->component( new Input_Component(
		Checkbox::make( 'newsletter' )
			->label( 'Subscribe to newsletter' )
			->checked( true )
	) ); ?>
</div>

<div class="doc-example" id="value">
	<p class="doc-example-title">value()</p>
	<?php $this->component( new Input_Component(
		Checkbox::make( 'opt_in' )
			->label( 'Opt in to marketing' )
			->value( 'yes' )
			->checked( true )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">disabled()</p>
	<?php $this->component( new Input_Component(
		Checkbox::make( 'mandatory' )
			->label( 'Mandatory option' )
			->checked( true )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">warning_notification()</p>
	<?php $this->component( new Input_Component(
		Checkbox::make( 'confirm' )
			->label( 'Confirm your choice' )
			->warning_notification( 'Please confirm to continue.' )
	) ); ?>
</div>
