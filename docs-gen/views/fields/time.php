<?php
/**
 * Time Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Time;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Time::make( 'meeting_time' )
			->label( 'Meeting Time' )
	) ); ?>
</div>

<div class="doc-example" id="value">
	<p class="doc-example-title">With Value</p>
	<?php $this->component( new Input_Component(
		Time::make( 'start_time' )
			->label( 'Start Time' )
			->set_existing( '14:30' )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">Disabled</p>
	<?php $this->component( new Input_Component(
		Time::make( 'locked_time' )
			->label( 'Locked Time' )
			->set_existing( '09:00' )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">With Notification</p>
	<?php $this->component( new Input_Component(
		Time::make( 'notif_time' )
			->label( 'Time' )
			->info_notification( 'Business hours: 09:00 - 17:00' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Time::make( 'wrapped_time' )
			->label( 'Appointment' )
			->before( '<span style="color:#6b7280;font-size:13px;">Select appointment time</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Times shown in GMT</span>' )
	) ); ?>
</div>
