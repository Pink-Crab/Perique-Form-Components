<?php
/**
 * Date Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Date;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Date::make( 'birthday' )
			->label( 'Date of Birth' )
	) ); ?>
</div>

<div class="doc-example" id="value">
	<p class="doc-example-title">With Value</p>
	<?php $this->component( new Input_Component(
		Date::make( 'event_date' )
			->label( 'Event Date' )
			->set_existing( '2026-06-15' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Date::make( 'wrapped_date' )
			->label( 'Event Date' )
			->before( '<span style="color:#6b7280;font-size:13px;">Select your preferred date</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Format: YYYY-MM-DD</span>' )
	) ); ?>
</div>
