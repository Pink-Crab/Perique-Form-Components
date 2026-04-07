<?php
/**
 * Password Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Password;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Password::make( 'password' )
			->label( 'Password' )
			->placeholder( 'Enter your password' )
	) ); ?>
</div>

<div class="doc-example" id="value">
	<p class="doc-example-title">With Value (masked)</p>
	<?php $this->component( new Input_Component(
		Password::make( 'current_pw' )
			->label( 'Current Password' )
			->set_existing( 'secretpass' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Password::make( 'wrapped_pw' )
			->label( 'Password' )
			->before( '<span style="color:#6b7280;font-size:13px;">Must be at least 8 characters</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Include uppercase, lowercase and numbers</span>' )
	) ); ?>
</div>
