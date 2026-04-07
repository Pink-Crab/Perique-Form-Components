<?php
/**
 * Notifications - Documentation Examples
 *
 * Shows all four notification types across different field types.
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Component\Field\Select_Component;
use PinkCrab\Form_Components\Component\Field\Textarea_Component;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Select;
use PinkCrab\Form_Components\Element\Field\Textarea;
?>
<div class="doc-example" id="error">
	<p class="doc-example-title">error_notification()</p>
	<?php $this->component( new Input_Component(
		Text::make( 'error_field' )
			->label( 'Username' )
			->required( true )
			->error_notification( 'This field is required.' )
	) ); ?>
</div>

<div class="doc-example" id="warning">
	<p class="doc-example-title">warning_notification()</p>
	<?php $this->component( new Input_Component(
		Text::make( 'warning_field' )
			->label( 'Display Name' )
			->set_existing( 'ab' )
			->warning_notification( 'Name is very short, consider using a longer name.' )
	) ); ?>
</div>

<div class="doc-example" id="success">
	<p class="doc-example-title">success_notification()</p>
	<?php $this->component( new Input_Component(
		Text::make( 'success_field' )
			->label( 'Username' )
			->set_existing( 'johndoe' )
			->success_notification( 'Username is available!' )
	) ); ?>
</div>

<div class="doc-example" id="info">
	<p class="doc-example-title">info_notification()</p>
	<?php $this->component( new Input_Component(
		Text::make( 'info_field' )
			->label( 'Slug' )
			->info_notification( 'Only lowercase letters, numbers, and hyphens.' )
	) ); ?>
</div>

<div class="doc-example" id="select-notification">
	<p class="doc-example-title">On Select</p>
	<?php $this->component( new Select_Component(
		Select::make( 'category' )
			->label( 'Category' )
			->options( array( '' => 'Select...' ) )
			->error_notification( 'Please select a category.' )
	) ); ?>
</div>

<div class="doc-example" id="textarea-notification">
	<p class="doc-example-title">On Textarea</p>
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'feedback' )
			->label( 'Feedback' )
			->set_existing( 'Hi' )
			->warning_notification( 'Please provide more detail (minimum 20 characters).' )
			->rows( 3 )
	) ); ?>
</div>
