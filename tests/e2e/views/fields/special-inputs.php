<?php
/**
 * Special Inputs - Kitchen Sink
 *
 * Color, Checkbox, Radio with all their traits.
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Color;
use PinkCrab\Form_Components\Element\Field\Input\Checkbox;
use PinkCrab\Form_Components\Element\Field\Input\Radio;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-special-inputs">

	<!-- ===== COLOR ===== -->
	<div class="e2e-color-section">
		<?php $this->component( new Input_Component(
			Color::make( 'color_basic' )
				->label( 'Color' )
				->add_class( 'e2e-color-basic' )
				->set_existing( '#ff5733' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Color::make( 'color_full' )
				->label( 'Full Color' )
				->add_class( 'e2e-color-full' )
				->autocomplete( 'off' )
				->datalist_items( array( '#ff0000', '#00ff00', '#0000ff' ) )
				->disabled( true )
				->required( true )
				->set_existing( '#00ff00' )
		) ); ?>
	</div>

		<!-- Color with notification, before/after, wrapper data -->
		<?php $this->component( new Input_Component(
			Color::make( 'color_extras' )
				->label( 'Color Extras' )
				->add_class( 'e2e-color-extras' )
				->info_notification( 'Pick a brand color' )
				->before( '<span class="color-swatch">Swatch</span>' )
				->after( '<span class="color-hex">#hex</span>' )
				->wrapper_data( 'type', 'color-picker' )
		) ); ?>
	</div>

	<!-- ===== CHECKBOX ===== -->
	<div class="e2e-checkbox-section">
		<?php $this->component( new Input_Component(
			Checkbox::make( 'checkbox_unchecked' )
				->label( 'Unchecked' )
				->add_class( 'e2e-checkbox-unchecked' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Checkbox::make( 'checkbox_checked' )
				->label( 'Checked' )
				->add_class( 'e2e-checkbox-checked' )
				->checked( true )
		) ); ?>

		<?php $this->component( new Input_Component(
			Checkbox::make( 'checkbox_disabled' )
				->label( 'Disabled Checked' )
				->add_class( 'e2e-checkbox-disabled' )
				->checked( true )
				->disabled( true )
		) ); ?>
	</div>

		<!-- Checkbox with value and notification -->
		<?php $this->component( new Input_Component(
			Checkbox::make( 'checkbox_value' )
				->label( 'Checkbox With Value' )
				->add_class( 'e2e-checkbox-value' )
				->value( 'agree' )
				->checked( true )
				->warning_notification( 'Please confirm' )
				->before( '<span class="cb-before">Terms</span>' )
		) ); ?>
	</div>

	<!-- ===== RADIO ===== -->
	<div class="e2e-radio-section">
		<?php $this->component( new Input_Component(
			Radio::make( 'radio_unchecked' )
				->label( 'Unchecked Radio' )
				->add_class( 'e2e-radio-unchecked' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Radio::make( 'radio_checked' )
				->label( 'Checked Radio' )
				->add_class( 'e2e-radio-checked' )
				->checked( true )
		) ); ?>

		<?php $this->component( new Input_Component(
			Radio::make( 'radio_disabled' )
				->label( 'Disabled Radio' )
				->add_class( 'e2e-radio-disabled' )
				->checked( true )
				->disabled( true )
		) ); ?>
		<!-- Radio with value and notification -->
		<?php $this->component( new Input_Component(
			Radio::make( 'radio_value' )
				->label( 'Radio With Value' )
				->add_class( 'e2e-radio-value' )
				->value( 'option_a' )
				->checked( true )
				->error_notification( 'Must select' )
		) ); ?>
	</div>

</div>
