<?php
/**
 * Raw HTML - Kitchen Sink
 */

use PinkCrab\Form_Components\Component\Field\Raw_HTML_Component;
use PinkCrab\Form_Components\Element\Raw_HTML;
?>
<div id="e2e-raw-html">

	<?php $this->component( new Raw_HTML_Component(
		Raw_HTML::make( 'raw_block' )
			->html( '<div class="e2e-raw-content"><p>Raw HTML Content</p><span>Nested span</span></div>' )
	) ); ?>

	<?php $this->component( new Raw_HTML_Component(
		Raw_HTML::make( 'raw_empty' )
	) ); ?>

</div>
