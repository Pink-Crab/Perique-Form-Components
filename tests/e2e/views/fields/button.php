<?php
/**
 * Button - Kitchen Sink
 *
 * Exercises every feature the Button element supports.
 */

use PinkCrab\Form_Components\Component\Field\Button_Component;
use PinkCrab\Form_Components\Element\Button;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-button">

	<!-- Basic button -->
	<?php $this->component( new Button_Component(
		Button::make( 'btn_basic' )
			->type( 'button' )
			->text( 'Click Me' )
			->add_class( 'e2e-btn-basic' )
	) ); ?>

	<!-- Submit button -->
	<?php $this->component( new Button_Component(
		Button::make( 'btn_submit' )
			->type( 'submit' )
			->text( 'Submit Form' )
			->add_class( 'e2e-btn-submit' )
	) ); ?>

	<!-- Reset button -->
	<?php $this->component( new Button_Component(
		Button::make( 'btn_reset' )
			->type( 'reset' )
			->text( 'Reset Form' )
			->add_class( 'e2e-btn-reset' )
	) ); ?>

	<!-- Disabled button -->
	<?php $this->component( new Button_Component(
		Button::make( 'btn_disabled' )
			->type( 'button' )
			->text( 'Disabled' )
			->add_class( 'e2e-btn-disabled' )
			->disabled( true )
	) ); ?>

	<!-- Button with before/after -->
	<?php $this->component( new Button_Component(
		Button::make( 'btn_wrapped' )
			->type( 'button' )
			->text( 'Wrapped Button' )
			->add_class( 'e2e-btn-wrapped' )
			->before( '<span class="btn-before">Icon</span>' )
			->after( '<span class="btn-after">Help</span>' )
	) ); ?>

	<!-- Button with data attributes -->
	<?php $this->component( new Button_Component(
		Button::make( 'btn_data' )
			->type( 'button' )
			->text( 'Data Button' )
			->add_class( 'e2e-btn-data' )
			->data( 'action', 'save' )
			->data( 'target', 'form-1' )
	) ); ?>

	<!-- Button with custom style -->
	<?php
	$custom_btn = new Button( 'btn_custom_style', new E2E_Custom_Style() );
	$custom_btn->type( 'button' )
		->text( 'Custom Styled' )
		->add_class( 'e2e-btn-custom' );
	$this->component( new Button_Component( $custom_btn ) );
	?>

</div>
