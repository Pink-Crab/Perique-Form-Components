<?php
/**
 * Tel Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Tel;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Tel::make( 'phone' )
			->label( 'Phone Number' )
			->placeholder( '+44 7700 900000' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Tel::make( 'wrapped_tel' )
			->label( 'Phone' )
			->before( '<span style="color:#6b7280;font-size:13px;">Include country code</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">e.g. +44 7700 900000</span>' )
	) ); ?>
</div>
