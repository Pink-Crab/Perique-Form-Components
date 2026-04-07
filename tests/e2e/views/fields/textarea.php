<?php
/**
 * Textarea - Kitchen Sink
 *
 * Content, rows/cols, empty, placeholder, disabled, readonly, required,
 * minlength/maxlength, spellcheck, autocomplete, custom style.
 */

use PinkCrab\Form_Components\Component\Field\Textarea_Component;
use PinkCrab\Form_Components\Element\Field\Textarea;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-textarea">

	<!-- Basic with content and rows/cols -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_basic' )
			->label( 'Textarea' )
			->add_class( 'e2e-textarea-basic' )
			->set_existing( 'Default content' )
			->rows( 5 )
			->cols( 40 )
	) ); ?>

	<!-- Empty -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_empty' )
			->label( 'Empty Textarea' )
			->add_class( 'e2e-textarea-empty' )
			->rows( 3 )
	) ); ?>

	<!-- Placeholder -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_placeholder' )
			->label( 'Placeholder' )
			->add_class( 'e2e-textarea-placeholder' )
			->placeholder( 'Type here...' )
	) ); ?>

	<!-- Disabled -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_disabled' )
			->label( 'Disabled' )
			->add_class( 'e2e-textarea-disabled' )
			->set_existing( 'Cannot edit' )
			->disabled( true )
	) ); ?>

	<!-- Readonly -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_readonly' )
			->label( 'Readonly' )
			->add_class( 'e2e-textarea-readonly' )
			->set_existing( 'Read only' )
			->readonly( true )
	) ); ?>

	<!-- Required -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_required' )
			->label( 'Required' )
			->add_class( 'e2e-textarea-required' )
			->required( true )
	) ); ?>

	<!-- Length limits -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_length' )
			->label( 'Length Limits' )
			->add_class( 'e2e-textarea-length' )
			->minlength( 10 )
			->maxlength( 500 )
	) ); ?>

	<!-- Spellcheck -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_spellcheck' )
			->label( 'Spellcheck Off' )
			->add_class( 'e2e-textarea-spellcheck' )
			->spellcheck( 'false' )
	) ); ?>

	<!-- Autocomplete -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_autocomplete' )
			->label( 'Autocomplete' )
			->add_class( 'e2e-textarea-autocomplete' )
			->autocomplete( 'off' )
	) ); ?>

	<!-- Notification -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_notification' )
			->label( 'Notification' )
			->add_class( 'e2e-textarea-notification' )
			->error_notification( 'Too short' )
	) ); ?>

	<!-- Before/After, wrapper data, custom id, data attrs -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_wrapped' )
			->label( 'Wrapped' )
			->add_class( 'e2e-textarea-wrapped' )
			->before( '<span class="ta-before">Description:</span>' )
			->after( '<span class="ta-after">Max 500 chars</span>' )
			->wrapper_data( 'field', 'description' )
			->data( 'editor', 'plain' )
			->id( 'custom-textarea-id' )
	) ); ?>

	<!-- No wrapper -->
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'textarea_no_wrapper' )
			->add_class( 'e2e-textarea-no-wrapper' )
			->show_wrapper( false )
	) ); ?>

	<!-- Custom Style -->
	<?php
	$custom_textarea = new Textarea( 'textarea_custom_style', new E2E_Custom_Style() );
	$custom_textarea->label( 'Custom Styled' )
		->add_class( 'e2e-textarea-custom' )
		->set_existing( 'Styled content' )
		->rows( 4 );
	$this->component( new Textarea_Component( $custom_textarea ) );
	?>

</div>
