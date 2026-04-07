<?php
/**
 * Datetime Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Datetime;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Datetime::make( 'event' )
			->label( 'Event Date & Time' )
	) ); ?>
</div>

<div class="doc-example" id="value">
	<p class="doc-example-title">With Value</p>
	<?php $this->component( new Input_Component(
		Datetime::make( 'scheduled' )
			->label( 'Scheduled For' )
			->set_existing( '2026-06-15T14:30' )
	) ); ?>
</div>

<div class="doc-example" id="required">
	<p class="doc-example-title">Required</p>
	<?php $this->component( new Input_Component(
		Datetime::make( 'required_dt' )
			->label( 'Deadline' )
			->required( true )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Datetime::make( 'wrapped_dt' )
			->label( 'Event' )
			->before( '<span style="color:#6b7280;font-size:13px;">Select date and time</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">All times in UTC</span>' )
	) ); ?>
</div>
