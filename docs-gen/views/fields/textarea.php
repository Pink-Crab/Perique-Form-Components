<?php
/**
 * Textarea - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Textarea_Component;
use PinkCrab\Form_Components\Element\Field\Textarea;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'message' )
			->label( 'Message' )
			->rows( 4 )
	) ); ?>
</div>

<div class="doc-example" id="value">
	<p class="doc-example-title">With Value</p>
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'bio' )
			->label( 'Bio' )
			->set_existing( 'A software developer with a passion for clean code and good documentation.' )
			->rows( 3 )
	) ); ?>
</div>

<div class="doc-example" id="placeholder">
	<p class="doc-example-title">Placeholder</p>
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'notes' )
			->label( 'Notes' )
			->placeholder( 'Enter your notes here...' )
			->rows( 3 )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">Disabled</p>
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'disabled_ta' )
			->label( 'Disabled' )
			->set_existing( 'This textarea is disabled.' )
			->disabled( true )
			->rows( 2 )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">With Notification</p>
	<?php $this->component( new Textarea_Component(
		Textarea::make( 'short_msg' )
			->label( 'Message' )
			->set_existing( 'Hi' )
			->error_notification( 'Message must be at least 10 characters.' )
			->rows( 3 )
	) ); ?>
</div>
