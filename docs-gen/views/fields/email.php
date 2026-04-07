<?php
/**
 * Email Input - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Email;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Input_Component(
		Email::make( 'email' )
			->label( 'Email Address' )
			->placeholder( 'you@example.com' )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">With Notification</p>
	<?php $this->component( new Input_Component(
		Email::make( 'invalid_email' )
			->label( 'Email' )
			->set_existing( 'not-an-email' )
			->error_notification( 'Please enter a valid email address.' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">Before / After</p>
	<?php $this->component( new Input_Component(
		Email::make( 'wrapped_email' )
			->label( 'Email' )
			->before( '<span style="color:#6b7280;font-size:13px;">We will never share your email</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">Used for account recovery</span>' )
	) ); ?>
</div>
