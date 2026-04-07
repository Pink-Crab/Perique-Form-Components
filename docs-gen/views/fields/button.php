<?php
/**
 * Button - Documentation Examples
 */

use PinkCrab\Form_Components\Component\Field\Button_Component;
use PinkCrab\Form_Components\Element\Button;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic (type: button)</p>
	<?php $this->component( new Button_Component(
		Button::make( 'btn' )
			->type( 'button' )
			->text( 'Click Me' )
	) ); ?>
</div>

<div class="doc-example" id="submit">
	<p class="doc-example-title">type( 'submit' )</p>
	<?php $this->component( new Button_Component(
		Button::make( 'submit_btn' )
			->type( 'submit' )
			->text( 'Submit Form' )
	) ); ?>
</div>

<div class="doc-example" id="reset">
	<p class="doc-example-title">type( 'reset' )</p>
	<?php $this->component( new Button_Component(
		Button::make( 'reset_btn' )
			->type( 'reset' )
			->text( 'Reset Form' )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">disabled()</p>
	<?php $this->component( new Button_Component(
		Button::make( 'disabled_btn' )
			->type( 'button' )
			->text( 'Disabled' )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="data-attrs">
	<p class="doc-example-title">data()</p>
	<?php $this->component( new Button_Component(
		Button::make( 'action_btn' )
			->type( 'button' )
			->text( 'Save Draft' )
			->data( 'action', 'save-draft' )
			->data( 'target', 'form-1' )
	) ); ?>
</div>

<div class="doc-example" id="custom-class">
	<p class="doc-example-title">add_class()</p>
	<?php $this->component( new Button_Component(
		Button::make( 'styled_btn' )
			->type( 'button' )
			->text( 'Custom Class' )
			->add_class( 'my-button-class' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">before() / after()</p>
	<?php $this->component( new Button_Component(
		Button::make( 'wrapped_btn' )
			->type( 'button' )
			->text( 'Wrapped Button' )
			->before( '<span style="color:#6b7280;font-size:13px;">Action:</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Click to proceed</span>' )
	) ); ?>
</div>
