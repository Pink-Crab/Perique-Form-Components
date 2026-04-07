<?php
/**
 * Number Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Number;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Number::make( 'quantity' )
			->label( 'Quantity' )
			->set_existing( '42' )
	) ); ?>
</div>

<div class="doc-example" id="range">
	<p class="doc-example-title">With Min / Max / Step</p>
	<?php $this->component( new Input_Component(
		Number::make( 'age' )
			->label( 'Age' )
			->min( 1 )
			->max( 120 )
			->step( 1 )
			->set_existing( '25' )
	) ); ?>
</div>

<div class="doc-example" id="placeholder">
	<p class="doc-example-title">Placeholder</p>
	<?php $this->component( new Input_Component(
		Number::make( 'amount' )
			->label( 'Amount' )
			->placeholder( '0-1000' )
			->min( 0 )
			->max( 1000 )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Number::make( 'wrapped_num' )
			->label( 'Price' )
			->before( '<span style="color:#6b7280;font-size:13px;">Enter amount in GBP</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Excluding VAT</span>' )
	) ); ?>
</div>
