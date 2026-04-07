<?php
/**
 * Checkbox Group - Documentation Examples
 */

use PinkCrab\Form_Components\Component\Field\Checkbox_Group_Component;
use PinkCrab\Form_Components\Element\Field\Group\Checkbox_Group;
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic</p>
	<?php $this->component( new Checkbox_Group_Component(
		Checkbox_Group::make( 'colours' )
			->label( 'Favourite Colours' )
			->options( array(
				'red'   => 'Red',
				'green' => 'Green',
				'blue'  => 'Blue',
			) )
	) ); ?>
</div>

<div class="doc-example" id="selected">
	<p class="doc-example-title">selected()</p>
	<?php $this->component( new Checkbox_Group_Component(
		Checkbox_Group::make( 'languages' )
			->label( 'Languages' )
			->options( array(
				'php'  => 'PHP',
				'js'   => 'JavaScript',
				'go'   => 'Go',
				'rust' => 'Rust',
			) )
			->selected( array( 'php', 'js' ) )
	) ); ?>
</div>

<div class="doc-example" id="disabled">
	<p class="doc-example-title">disabled()</p>
	<?php $this->component( new Checkbox_Group_Component(
		Checkbox_Group::make( 'locked_choices' )
			->label( 'Locked Choices' )
			->options( array(
				'a' => 'Option A',
				'b' => 'Option B',
				'c' => 'Option C',
			) )
			->selected( array( 'a', 'c' ) )
			->disabled( true )
	) ); ?>
</div>

<div class="doc-example" id="notification">
	<p class="doc-example-title">info_notification()</p>
	<?php $this->component( new Checkbox_Group_Component(
		Checkbox_Group::make( 'features' )
			->label( 'Features' )
			->options( array(
				'sso'  => 'Single Sign-On',
				'2fa'  => 'Two Factor Auth',
				'api'  => 'API Access',
			) )
			->info_notification( 'Select at least one feature.' )
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">before() / after()</p>
	<?php $this->component( new Checkbox_Group_Component(
		Checkbox_Group::make( 'topics' )
			->label( 'Topics' )
			->options( array(
				'tech'    => 'Technology',
				'science' => 'Science',
				'art'     => 'Art',
			) )
			->before( '<span style="color:#6b7280;font-size:13px;">Choose your interests:</span>' )
			->after( '<span style="color:#6b7280;font-size:13px;">You can change these later.</span>' )
	) ); ?>
</div>
