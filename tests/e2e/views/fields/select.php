<?php
/**
 * Select - Kitchen Sink
 *
 * Basic, pre-selected, optgroups, multiple, disabled, required, size, autocomplete, custom style.
 */

use PinkCrab\Form_Components\Component\Field\Select_Component;
use PinkCrab\Form_Components\Element\Field\Select;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-select">

	<!-- Basic flat options -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_basic' )
			->label( 'Basic Select' )
			->add_class( 'e2e-select-basic' )
			->options( array(
				''      => 'Choose an option',
				'one'   => 'Option One',
				'two'   => 'Option Two',
				'three' => 'Option Three',
			) )
	) ); ?>

	<!-- Pre-selected value -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_preselected' )
			->label( 'Pre-selected' )
			->add_class( 'e2e-select-preselected' )
			->options( array(
				'a' => 'Alpha',
				'b' => 'Bravo',
				'c' => 'Charlie',
			) )
			->set_existing( 'b' )
	) ); ?>

	<!-- Optgroups -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_optgroups' )
			->label( 'Grouped Select' )
			->add_class( 'e2e-select-optgroups' )
			->optgroup( 'Fruits', array(
				'apple'  => 'Apple',
				'banana' => 'Banana',
			) )
			->optgroup( 'Vegetables', array(
				'carrot' => 'Carrot',
				'pea'    => 'Pea',
			) )
	) ); ?>

	<!-- Disabled -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_disabled' )
			->label( 'Disabled Select' )
			->add_class( 'e2e-select-disabled' )
			->options( array( 'x' => 'Disabled Option' ) )
			->disabled( true )
	) ); ?>

	<!-- Required -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_required' )
			->label( 'Required Select' )
			->add_class( 'e2e-select-required' )
			->options( array(
				'' => 'Select...',
				'y' => 'Yes',
				'n' => 'No',
			) )
			->required( true )
	) ); ?>

	<!-- Size -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_size' )
			->label( 'Size Select' )
			->add_class( 'e2e-select-size' )
			->options( array(
				'a' => 'A',
				'b' => 'B',
				'c' => 'C',
				'd' => 'D',
				'e' => 'E',
			) )
			->size( 3 )
	) ); ?>

	<!-- Autocomplete -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_autocomplete' )
			->label( 'Autocomplete Select' )
			->add_class( 'e2e-select-autocomplete' )
			->options( array( 'gb' => 'United Kingdom', 'us' => 'United States' ) )
			->autocomplete( 'country' )
	) ); ?>

	<!-- Notification -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_notification' )
			->label( 'Notification Select' )
			->add_class( 'e2e-select-notification' )
			->options( array( '' => 'Pick one', 'a' => 'A' ) )
			->error_notification( 'Selection required' )
	) ); ?>

	<!-- Before/After and wrapper data -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_wrapped' )
			->label( 'Wrapped Select' )
			->add_class( 'e2e-select-wrapped' )
			->options( array( 'x' => 'X' ) )
			->before( '<span class="select-before">Choose:</span>' )
			->after( '<span class="select-after">Done</span>' )
			->wrapper_data( 'field', 'select' )
			->id( 'custom-select-id' )
	) ); ?>

	<!-- No wrapper -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_no_wrapper' )
			->add_class( 'e2e-select-no-wrapper' )
			->options( array( 'z' => 'Z' ) )
			->show_wrapper( false )
	) ); ?>

	<!-- Multiple with pre-selected values -->
	<?php $this->component( new Select_Component(
		Select::make( 'select_multiple' )
			->label( 'Multi Select' )
			->add_class( 'e2e-select-multiple' )
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

	<!-- Custom Style -->
	<?php
	$custom_select = new Select( 'select_custom_style', new E2E_Custom_Style() );
	$custom_select->label( 'Custom Styled' )
		->add_class( 'e2e-select-custom' )
		->options( array( 'x' => 'Option X', 'y' => 'Option Y' ) );
	$this->component( new Select_Component( $custom_select ) );
	?>

</div>
