<?php
/**
 * File Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\File;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		File::make( 'upload' )
			->label( 'Upload File' )
	) ); ?>
</div>
