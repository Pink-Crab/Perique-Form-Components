<?php
/**
 * Text Input - Kitchen Sink
 *
 * Exercises every feature the Text input supports.
 * Uses components directly - no factory.
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Text;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-text-input">

	<!-- Basic: label, value, placeholder, custom class -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_basic' )
			->label( 'Basic Text' )
			->set_existing( 'Hello World' )
			->add_class( 'e2e-text-basic' )
			->placeholder( 'Enter text...' )
	) ); ?>

	<!-- Datalist -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_datalist' )
			->label( 'With Datalist' )
			->add_class( 'e2e-text-datalist' )
			->datalist_items( array( 'Apple', 'Banana', 'Cherry', 'Date' ) )
	) ); ?>

	<!-- Before/After wrapper content -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_wrapped' )
			->label( 'Wrapped Text' )
			->add_class( 'e2e-text-wrapped' )
			->before( '<span class="before-content">Before</span>' )
			->after( '<span class="after-content">After</span>' )
	) ); ?>

	<!-- No wrapper -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_no_wrapper' )
			->add_class( 'e2e-text-no-wrapper' )
			->show_wrapper( false )
	) ); ?>

	<!-- Issue #23: before/after must render even when show_wrapper(false). -->
	<div id="text-no-wrapper-with-adornments-host">
	<?php $this->component( new Input_Component(
		Text::make( 'text_no_wrapper_adornments' )
			->add_class( 'e2e-text-no-wrapper-adornments' )
			->show_wrapper( false )
			->before( '<span class="e2e-no-wrap-before">BEFORE_NO_WRAP</span>' )
			->after( '<span class="e2e-no-wrap-after">AFTER_NO_WRAP</span>' )
	) ); ?>
	</div>

	<!-- Issue #23: nested-array form name (PHP repeater style) must be preserved verbatim. -->
	<?php $this->component( new Input_Component(
		Text::make( 'wm_loc_coordinates[0][latlong]' )
			->label( 'Bracketed Name' )
			->add_class( 'e2e-text-bracketed-name' )
	) ); ?>

	<!-- Data attributes -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_data_attrs' )
			->label( 'Data Attributes' )
			->add_class( 'e2e-text-data-attrs' )
			->data( 'custom-key', 'custom-value' )
			->data( 'another', 'value2' )
	) ); ?>

	<!-- Custom wrapper class -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_wrapper_class' )
			->label( 'Custom Wrapper' )
			->add_class( 'e2e-text-wrapper-class' )
			->add_wrapper_class( 'my-custom-wrapper' )
	) ); ?>

	<!-- Disabled -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_disabled' )
			->label( 'Disabled' )
			->add_class( 'e2e-text-disabled' )
			->set_existing( 'Cannot edit' )
			->disabled( true )
	) ); ?>

	<!-- Readonly -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_readonly' )
			->label( 'Readonly' )
			->add_class( 'e2e-text-readonly' )
			->set_existing( 'Read only value' )
			->readonly( true )
	) ); ?>

	<!-- Required -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_required' )
			->label( 'Required' )
			->add_class( 'e2e-text-required' )
			->required( true )
	) ); ?>

	<!-- Pattern -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_pattern' )
			->label( 'Pattern' )
			->add_class( 'e2e-text-pattern' )
			->pattern( '[A-Za-z]{3,}' )
	) ); ?>

	<!-- Min/Max Length -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_length' )
			->label( 'Length Limits' )
			->add_class( 'e2e-text-length' )
			->minlength( 3 )
			->maxlength( 50 )
	) ); ?>

	<!-- Input Mode -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_inputmode' )
			->label( 'Input Mode' )
			->add_class( 'e2e-text-inputmode' )
			->inputmode( 'numeric' )
	) ); ?>

	<!-- Spellcheck -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_spellcheck' )
			->label( 'Spellcheck Off' )
			->add_class( 'e2e-text-spellcheck' )
			->spellcheck( 'false' )
	) ); ?>

	<!-- Size -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_size' )
			->label( 'Size 30' )
			->add_class( 'e2e-text-size' )
			->size( 30 )
	) ); ?>

	<!-- Autocomplete -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_autocomplete' )
			->label( 'Autocomplete' )
			->add_class( 'e2e-text-autocomplete' )
			->autocomplete( 'given-name' )
	) ); ?>

	<!-- Custom ID -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_custom_id' )
			->label( 'Custom ID' )
			->add_class( 'e2e-text-custom-id' )
			->id( 'my-custom-id' )
	) ); ?>

	<!-- Raw attribute and attributes -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_raw_attrs' )
			->label( 'Raw Attributes' )
			->add_class( 'e2e-text-raw-attrs' )
			->attribute( 'tabindex', '5' )
			->attributes( array( 'aria-label' => 'Custom aria label', 'title' => 'Custom title' ) )
	) ); ?>

	<!-- Custom wrapper ID -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_wrapper_id' )
			->label( 'Custom Wrapper ID' )
			->add_class( 'e2e-text-wrapper-id' )
			->wrapper_id( 'my-wrapper-id' )
	) ); ?>

	<!-- Wrapper data attributes -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_wrapper_data' )
			->label( 'Wrapper Data' )
			->add_class( 'e2e-text-wrapper-data' )
			->wrapper_data( 'section', 'form-top' )
			->wrapper_data( 'index', '3' )
	) ); ?>

	<!-- Notification: info -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_notification_info' )
			->label( 'Info Notification' )
			->add_class( 'e2e-text-notif-info' )
			->info_notification( 'This is an info message' )
	) ); ?>

	<!-- Notification: error -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_notification_error' )
			->label( 'Error Notification' )
			->add_class( 'e2e-text-notif-error' )
			->error_notification( 'This field has an error' )
	) ); ?>

	<!-- Notification: success -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_notification_success' )
			->label( 'Success Notification' )
			->add_class( 'e2e-text-notif-success' )
			->success_notification( 'Success!' )
	) ); ?>

	<!-- Notification: warning -->
	<?php $this->component( new Input_Component(
		Text::make( 'text_notification_warning' )
			->label( 'Warning Notification' )
			->add_class( 'e2e-text-notif-warning' )
			->warning_notification( 'Be careful' )
	) ); ?>

	<!-- Custom Style -->
	<?php
	$custom_text = new Text( 'text_custom_style', new E2E_Custom_Style() );
	$custom_text->label( 'Custom Styled' )
		->add_class( 'e2e-text-custom-style' )
		->set_existing( 'Styled value' );
	$this->component( new Input_Component( $custom_text ) );
	?>

</div>
