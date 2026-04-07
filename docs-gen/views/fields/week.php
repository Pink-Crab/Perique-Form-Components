<?php
/**
 * Week Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Week;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Week::make( 'report_week' )
			->label( 'Report Week' )
	) ); ?>
</div>

<div class="doc-example" id="value">
	<p class="doc-example-title">With Value</p>
	<?php $this->component( new Input_Component(
		Week::make( 'current_week' )
			->label( 'Current Week' )
			->set_existing( '2026-W14' )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">Disabled</p>
	<?php $this->component( new Input_Component(
		Week::make( 'locked_week' )
			->label( 'Locked' )
			->set_existing( '2026-W01' )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">With Notification</p>
	<?php $this->component( new Input_Component(
		Week::make( 'notif_week' )
			->label( 'Week' )
			->set_existing( '2026-W52' )
			->warning_notification( 'This is a holiday week.' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Week::make( 'wrapped_week' )
			->label( 'Sprint Week' )
			->before( '<span style="color:#6b7280;font-size:13px;">Select sprint period</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Sprints run Monday to Friday</span>' )
	) ); ?>
</div>
