<?php
/**
 * Color Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Color;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Color::make( 'brand_color' )
			->label( 'Brand Colour' )
	) ); ?>
</div>

<div class="doc-example" id="value">
	<p class="doc-example-title">With Value</p>
	<?php $this->component( new Input_Component(
		Color::make( 'theme_color' )
			->label( 'Theme Colour' )
			->set_existing( '#3b82f6' )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">Disabled</p>
	<?php $this->component( new Input_Component(
		Color::make( 'locked_color' )
			->label( 'Locked Colour' )
			->set_existing( '#dc2626' )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">With Notification</p>
	<?php $this->component( new Input_Component(
		Color::make( 'info_color' )
			->label( 'Accent Colour' )
			->set_existing( '#3b82f6' )
			->info_notification( 'Choose your brand accent colour.' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Color::make( 'wrapped_color' )
			->label( 'Background' )
			->set_existing( '#f3f4f6' )
			->before( '<span style="color:#6b7280;font-size:13px;">Pick a background colour</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Used across all pages</span>' )
	) ); ?>
</div>
