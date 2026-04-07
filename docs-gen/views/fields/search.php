<?php
/**
 * Search Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Search;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Search::make( 'search' )
			->label( 'Search' )
			->placeholder( 'Search...' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Search::make( 'wrapped_search' )
			->label( 'Search' )
			->before( '<span style="color:#6b7280;font-size:13px;">Search the documentation</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Use quotes for exact match</span>' )
	) ); ?>
</div>
