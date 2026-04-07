<?php
/**
 * Range Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Range;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Range::make( 'volume' )
			->label( 'Volume' )
			->min( 0 )
			->max( 100 )
			->set_existing( '50' )
	) ); ?>
</div>

<div class="doc-example" id="step">
	<p class="doc-example-title">With Step</p>
	<?php $this->component( new Input_Component(
		Range::make( 'brightness' )
			->label( 'Brightness' )
			->min( 0 )
			->max( 100 )
			->step( 10 )
			->set_existing( '70' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Range::make( 'labeled_range' )
			->label( 'Opacity' )
			->min( 0 )
			->max( 100 )
			->set_existing( '75' )
			->before( '<span style="color:#6b7280;font-size:13px;">0%</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">100%</span>' )
	) ); ?>
</div>
