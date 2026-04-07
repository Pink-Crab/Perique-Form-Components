<?php
/**
 * URL Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Url;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Url::make( 'website' )
			->label( 'Website' )
			->placeholder( 'https://example.com' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Url::make( 'wrapped_url' )
			->label( 'Website' )
			->before( '<span style="color:#6b7280;font-size:13px;">Your public profile URL</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Must start with https://</span>' )
	) ); ?>
</div>
