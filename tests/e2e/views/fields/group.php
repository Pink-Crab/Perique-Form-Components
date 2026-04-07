<?php
/**
 * Group - Kitchen Sink
 *
 * Exercises the Group element wrapping child fields in a <div>.
 */

use PinkCrab\Form_Components\Component\Component_Factory;
use PinkCrab\Form_Components\Element\Group;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Email;

require_once __DIR__ . '/_custom-style.php';

$factory = Component_Factory::instance();
?>
<div id="e2e-group">

	<!-- Basic group with fields -->
	<?php $this->component( $factory->from_element(
		Group::make( 'basic_group' )
			->fields(
				Text::make( 'group_name' )->label( 'Name' ),
				Email::make( 'group_email' )->label( 'Email' )
			)
	) ); ?>

	<!-- Group with before/after -->
	<?php $this->component( $factory->from_element(
		Group::make( 'wrapped_group' )
			->before( '<div class="group-header">Section Header</div>' )
			->after( '<div class="group-footer">Section Footer</div>' )
			->fields(
				Text::make( 'wrapped_field' )->label( 'Field' )
			)
	) ); ?>

	<!-- Group with wrapper data -->
	<?php $this->component( $factory->from_element(
		Group::make( 'data_group' )
			->wrapper_data( 'section', 'personal' )
			->add_wrapper_class( 'custom-group-class' )
			->fields(
				Text::make( 'data_field' )->label( 'Data Field' )
			)
	) ); ?>

</div>
