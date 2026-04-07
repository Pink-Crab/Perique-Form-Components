<?php
/**
 * Radio Input - Documentation Examples
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Radio;
?>
<div class="doc-example" id="unchecked">
	<p class="doc-example-title">Basic (unchecked)</p>
	<?php $this->component( new Input_Component(
		Radio::make( 'option_a' )
			->label( 'Option A' )
			->value( 'a' )
	) ); ?>
</div>

<div class="doc-example" id="checked">
	<p class="doc-example-title">checked()</p>
	<?php $this->component( new Input_Component(
		Radio::make( 'option_b' )
			->label( 'Option B' )
			->value( 'b' )
			->checked( true )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">disabled()</p>
	<?php $this->component( new Input_Component(
		Radio::make( 'locked_option' )
			->label( 'Locked Option' )
			->value( 'locked' )
			->checked( true )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">error_notification()</p>
	<?php $this->component( new Input_Component(
		Radio::make( 'required_radio' )
			->label( 'Choose this option' )
			->value( 'choice' )
			->error_notification( 'You must select an option.' )
	) ); ?>
</div>
