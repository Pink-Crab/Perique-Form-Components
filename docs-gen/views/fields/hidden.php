<?php
/**
 * Hidden Input - Documentation Screenshots
 * Hidden inputs have no visible UI - this page documents the HTML output.
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Hidden;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Hidden Input (inspect HTML)</p>
	<?php $this->component( new Input_Component(
		Hidden::make( 'form_id' )
			->set_existing( '42' )
			->show_wrapper( false )
	) ); ?>
	<p style="color:#6b7280;font-size:13px;">A hidden input is rendered above but not visible. Defaults to show_wrapper(false).</p>
</div>
