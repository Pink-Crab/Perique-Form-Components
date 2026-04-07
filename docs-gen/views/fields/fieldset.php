<?php
/**
 * Fieldset - Documentation Examples
 */

use PinkCrab\Form_Components\Component\Form\Fieldset_Component;
use PinkCrab\Form_Components\Element\Fieldset;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Email;
use PinkCrab\Form_Components\Element\Field\Input\Tel;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic with legend()</p>
	<?php $this->component( new Fieldset_Component(
		Fieldset::make( 'personal' )
			->legend( 'Personal Information' )
			->fields(
				Text::make( 'first_name' )->label( 'First Name' )->required( true ),
				Text::make( 'last_name' )->label( 'Last Name' )->required( true ),
				Email::make( 'contact_email' )->label( 'Email' )
			)
	) ); ?>
</div>

<div class="doc-example" id="no-legend">
	<p class="doc-example-title">Without Legend</p>
	<?php $this->component( new Fieldset_Component(
		Fieldset::make( 'address' )
			->fields(
				Text::make( 'street' )->label( 'Street' ),
				Text::make( 'city' )->label( 'City' ),
				Text::make( 'postcode' )->label( 'Postcode' )
			)
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">disabled()</p>
	<?php $this->component( new Fieldset_Component(
		Fieldset::make( 'disabled_section' )
			->legend( 'Disabled Section' )
			->disabled( true )
			->fields(
				Text::make( 'disabled_name' )->label( 'Name' ),
				Tel::make( 'disabled_phone' )->label( 'Phone' )
			)
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">before() / after()</p>
	<?php $this->component( new Fieldset_Component(
		Fieldset::make( 'wrapped_fs' )
			->legend( 'Contact Details' )
			->before( '<p style="color:#6b7280;font-size:13px;margin:0 0 8px;">Please fill in your details below.</p>' )
			->after( '<p style="color:#6b7280;font-size:13px;margin:8px 0 0;">All fields are optional.</p>' )
			->fields(
				Text::make( 'company' )->label( 'Company' ),
				Tel::make( 'phone' )->label( 'Phone' )
			)
	) ); ?>
</div>

<div class="doc-example" id="wrapper-attrs">
	<p class="doc-example-title">wrapper_data() / add_wrapper_class()</p>
	<?php $this->component( new Fieldset_Component(
		Fieldset::make( 'data_fs' )
			->legend( 'Preferences' )
			->wrapper_data( 'section', 'preferences' )
			->add_wrapper_class( 'highlighted-fieldset' )
			->fields(
				Text::make( 'language' )->label( 'Language' ),
				Text::make( 'timezone' )->label( 'Timezone' )
			)
	) ); ?>
</div>
