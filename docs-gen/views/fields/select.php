<?php
/**
 * Select - Documentation Screenshots
 */

use PinkCrab\Form_Components\Component\Field\Select_Component;
use PinkCrab\Form_Components\Element\Field\Select;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Select_Component(
		Select::make( 'country' )
			->label( 'Country' )
			->options( array(
				''   => '-- Select --',
				'uk' => 'United Kingdom',
				'us' => 'United States',
				'ca' => 'Canada',
				'au' => 'Australia',
			) )
	) ); ?>
</div>

<div class="doc-example" id="selected">
	<p class="doc-example-title">With Selected Value</p>
	<?php $this->component( new Select_Component(
		Select::make( 'role' )
			->label( 'Role' )
			->options( array(
				'admin'      => 'Administrator',
				'editor'     => 'Editor',
				'author'     => 'Author',
				'subscriber' => 'Subscriber',
			) )
			->set_existing( 'editor' )
	) ); ?>
</div>

<div class="doc-example" id="optgroups">
	<p class="doc-example-title">Option Groups</p>
	<?php $this->component( new Select_Component(
		Select::make( 'vehicle' )
			->label( 'Vehicle' )
			->optgroup( 'Cars', array(
				'volvo' => 'Volvo',
				'bmw'   => 'BMW',
				'audi'  => 'Audi',
			) )
			->optgroup( 'Bikes', array(
				'honda'  => 'Honda',
				'yamaha' => 'Yamaha',
			) )
	) ); ?>
</div>

<div class="doc-example" id="size">
	<p class="doc-example-title">size() - visible rows</p>
	<?php $this->component( new Select_Component(
		Select::make( 'visible_list' )
			->label( 'Items (3 visible)' )
			->options( array(
				'a' => 'Alpha',
				'b' => 'Bravo',
				'c' => 'Charlie',
				'd' => 'Delta',
				'e' => 'Echo',
			) )
			->size( 3 )
	) ); ?>
</div>

<div class="doc-example" id="multiple">
	<p class="doc-example-title">multiple() with size()</p>
	<?php $this->component( new Select_Component(
		Select::make( 'languages' )
			->label( 'Languages' )
			->options( array(
				'php'    => 'PHP',
				'js'     => 'JavaScript',
				'python' => 'Python',
				'go'     => 'Go',
				'rust'   => 'Rust',
			) )
			->multiple( true )
			->size( 5 )
			->set_existing( array( 'php', 'go' ) )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">Disabled</p>
	<?php $this->component( new Select_Component(
		Select::make( 'locked_plan' )
			->label( 'Plan' )
			->options( array( 'pro' => 'Professional' ) )
			->set_existing( 'pro' )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">With Notification</p>
	<?php $this->component( new Select_Component(
		Select::make( 'bad_select' )
			->label( 'Category' )
			->options( array( '' => 'Select...', 'bug' => 'Bug', 'feature' => 'Feature' ) )
			->error_notification( 'Please select a category.' )
	) ); ?>
</div>
