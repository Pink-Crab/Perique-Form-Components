<?php
/**
 * Fieldset - Kitchen Sink
 *
 * Exercises the Fieldset element rendering <fieldset> with <legend>.
 */

use PinkCrab\Form_Components\Component\Form\Fieldset_Component;
use PinkCrab\Form_Components\Element\Fieldset;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Email;
use PinkCrab\Form_Components\Element\Field\Input\Tel;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-fieldset">

	<!-- Basic fieldset with legend -->
	<?php $this->component( new Fieldset_Component(
		Fieldset::make( 'personal_info' )
			->legend( 'Personal Information' )
			->fields(
				Text::make( 'first_name' )->label( 'First Name' )->required( true ),
				Text::make( 'last_name' )->label( 'Last Name' )->required( true ),
				Email::make( 'fs_email' )->label( 'Email' )
			)
	) ); ?>

	<!-- Fieldset without legend -->
	<?php $this->component( new Fieldset_Component(
		Fieldset::make( 'address' )
			->fields(
				Text::make( 'street' )->label( 'Street' ),
				Text::make( 'city' )->label( 'City' )
			)
	) ); ?>

	<!-- Disabled fieldset -->
	<?php $this->component( new Fieldset_Component(
		Fieldset::make( 'disabled_section' )
			->legend( 'Disabled Section' )
			->disabled( true )
			->fields(
				Text::make( 'disabled_field' )->label( 'Disabled Field' ),
				Tel::make( 'disabled_tel' )->label( 'Phone' )
			)
	) ); ?>

	<!-- Fieldset with before/after -->
	<?php $this->component( new Fieldset_Component(
		Fieldset::make( 'wrapped_fieldset' )
			->legend( 'Wrapped' )
			->before( '<p class="fs-before">Instructions here</p>' )
			->after( '<p class="fs-after">End of section</p>' )
			->fields(
				Text::make( 'wrapped_fs_field' )->label( 'Field' )
			)
	) ); ?>

	<!-- Fieldset with wrapper data and custom class -->
	<?php $this->component( new Fieldset_Component(
		Fieldset::make( 'data_fieldset' )
			->legend( 'Data Fieldset' )
			->wrapper_data( 'section', 'contact' )
			->add_wrapper_class( 'custom-fieldset-class' )
			->fields(
				Text::make( 'data_fs_field' )->label( 'Contact' )
			)
	) ); ?>

	<!-- Fieldset with custom style -->
	<?php
	$custom_fs = new Fieldset( 'custom_fieldset', new E2E_Custom_Style() );
	$custom_fs->legend( 'Custom Styled' )
		->fields(
			Text::make( 'custom_fs_field' )->label( 'Custom' )
		);
	$this->component( new Fieldset_Component( $custom_fs ) );
	?>

</div>
