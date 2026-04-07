<?php
/**
 * Radio Group - Documentation Examples
 */

use PinkCrab\Form_Components\Component\Field\Radio_Group_Component;
use PinkCrab\Form_Components\Element\Field\Group\Radio_Group;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Radio_Group_Component(
		Radio_Group::make( 'size' )
			->label( 'Size' )
			->options( array(
				'small'  => 'Small',
				'medium' => 'Medium',
				'large'  => 'Large',
			) )
	) ); ?>
</div>

<div class="doc-example" id="selected">
	<p class="doc-example-title">selected()</p>
	<?php $this->component( new Radio_Group_Component(
		Radio_Group::make( 'plan' )
			->label( 'Plan' )
			->options( array(
				'free' => 'Free',
				'pro'  => 'Professional',
				'ent'  => 'Enterprise',
			) )
			->selected( 'pro' )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">disabled()</p>
	<?php $this->component( new Radio_Group_Component(
		Radio_Group::make( 'locked_choice' )
			->label( 'Status' )
			->options( array(
				'active'   => 'Active',
				'inactive' => 'Inactive',
			) )
			->selected( 'active' )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">error_notification()</p>
	<?php $this->component( new Radio_Group_Component(
		Radio_Group::make( 'required_choice' )
			->label( 'Shipping Method' )
			->options( array(
				'standard' => 'Standard (3-5 days)',
				'express'  => 'Express (1-2 days)',
				'next_day' => 'Next Day',
			) )
			->error_notification( 'Please select a shipping method.' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">before() / after()</p>
	<?php $this->component( new Radio_Group_Component(
		Radio_Group::make( 'payment' )
			->label( 'Payment Method' )
			->options( array(
				'card'   => 'Credit Card',
				'paypal' => 'PayPal',
				'bank'   => 'Bank Transfer',
			) )
			->before( '<span style="color:#6b7280;font-size:13px;">Select payment method:</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">All payments are processed securely.</span>' )
	) ); ?>
</div>
