<?php
/**
 * Text Input - Documentation Screenshots
 * Only visually distinct examples.
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Text;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Text::make( 'username' )
			->label( 'Username' )
	) ); ?>
</div>

<div class="doc-example" id="value">
	<p class="doc-example-title">With Value</p>
	<?php $this->component( new Input_Component(
		Text::make( 'fullname' )
			->label( 'Full Name' )
			->set_existing( 'John Smith' )
	) ); ?>
</div>

<div class="doc-example" id="placeholder">
	<p class="doc-example-title">Placeholder</p>
	<?php $this->component( new Input_Component(
		Text::make( 'search_query' )
			->label( 'Search' )
			->placeholder( 'Type to search...' )
	) ); ?>
</div>

<div class="doc-example" id="required">
	<p class="doc-example-title">Required</p>
	<?php $this->component( new Input_Component(
		Text::make( 'email_addr' )
			->label( 'Email Address' )
			->required( true )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">Disabled</p>
	<?php $this->component( new Input_Component(
		Text::make( 'locked_field' )
			->label( 'Locked Field' )
			->set_existing( 'Cannot edit' )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="notification-error">
	<p class="doc-example-title">Error Notification</p>
	<?php $this->component( new Input_Component(
		Text::make( 'name_error' )
			->label( 'Name' )
			->required( true )
			->error_notification( 'This field is required.' )
	) ); ?>
</div>

<div class="doc-example" id="notification-warning">
	<p class="doc-example-title">Warning Notification</p>
	<?php $this->component( new Input_Component(
		Text::make( 'name_warning' )
			->label( 'Display Name' )
			->set_existing( 'ab' )
			->warning_notification( 'Name is very short.' )
	) ); ?>
</div>

<div class="doc-example" id="notification-success">
	<p class="doc-example-title">Success Notification</p>
	<?php $this->component( new Input_Component(
		Text::make( 'name_success' )
			->label( 'Username' )
			->set_existing( 'johndoe' )
			->success_notification( 'Username is available!' )
	) ); ?>
</div>

<div class="doc-example" id="notification-info">
	<p class="doc-example-title">Info Notification</p>
	<?php $this->component( new Input_Component(
		Text::make( 'name_info' )
			->label( 'Slug' )
			->info_notification( 'Only lowercase letters and hyphens.' )
	) ); ?>
</div>

<div class="doc-example" id="datalist">
	<p class="doc-example-title">Datalist</p>
	<?php $this->component( new Input_Component(
		Text::make( 'fruit' )
			->label( 'Favourite Fruit' )
			->datalist_items( array( 'Apple', 'Banana', 'Cherry', 'Date', 'Elderberry' ) )
			->placeholder( 'Start typing...' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Text::make( 'wrapped_field' )
			->label( 'Amount' )
			->before( '<span style="color:#6b7280;font-size:13px;">Enter the amount below</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Amount in GBP</span>' )
	) ); ?>
</div>
