<?php
/**
 * Groups - Kitchen Sink
 *
 * Checkbox_Group and Radio_Group with all their traits.
 */

use PinkCrab\Form_Components\Component\Field\Checkbox_Group_Component;
use PinkCrab\Form_Components\Component\Field\Radio_Group_Component;
use PinkCrab\Form_Components\Element\Field\Group\Checkbox_Group;
use PinkCrab\Form_Components\Element\Field\Group\Radio_Group;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-groups">

	<!-- ===== CHECKBOX GROUP ===== -->
	<div class="e2e-checkbox-group-section">

		<!-- Basic with pre-checked -->
		<?php $this->component( new Checkbox_Group_Component(
			Checkbox_Group::make( 'checkbox_colours' )
				->label( 'Favourite Colours' )
				->options( array(
					'red'   => 'Red',
					'green' => 'Green',
					'blue'  => 'Blue',
				) )
				->selected( array( 'red', 'blue' ) )
		) ); ?>

		<!-- Disabled -->
		<?php $this->component( new Checkbox_Group_Component(
			Checkbox_Group::make( 'checkbox_disabled' )
				->label( 'Disabled Group' )
				->options( array(
					'a' => 'Option A',
					'b' => 'Option B',
				) )
				->selected( array( 'a' ) )
				->disabled( true )
		) ); ?>

		<!-- Notification, before/after, wrapper data -->
		<?php $this->component( new Checkbox_Group_Component(
			Checkbox_Group::make( 'checkbox_extras' )
				->label( 'Checkbox Extras' )
				->options( array( 'p' => 'P', 'q' => 'Q' ) )
				->info_notification( 'Select at least one' )
				->before( '<span class="cb-group-before">Options:</span>' )
				->after( '<span class="cb-group-after">End</span>' )
				->wrapper_data( 'group', 'checkboxes' )
		) ); ?>

		<!-- Custom style -->
		<?php
		$custom_checkbox = new Checkbox_Group( 'checkbox_custom_style', new E2E_Custom_Style() );
		$custom_checkbox->label( 'Custom Styled' )
			->options( array( 'x' => 'X', 'y' => 'Y' ) )
			->selected( array( 'y' ) );
		$this->component( new Checkbox_Group_Component( $custom_checkbox ) );
		?>
	</div>

	<!-- ===== RADIO GROUP ===== -->
	<div class="e2e-radio-group-section">

		<!-- Basic with pre-selected -->
		<?php $this->component( new Radio_Group_Component(
			Radio_Group::make( 'radio_size' )
				->label( 'Size' )
				->options( array(
					'small'  => 'Small',
					'medium' => 'Medium',
					'large'  => 'Large',
				) )
				->selected( 'medium' )
		) ); ?>

		<!-- Disabled -->
		<?php $this->component( new Radio_Group_Component(
			Radio_Group::make( 'radio_disabled' )
				->label( 'Disabled Radio' )
				->options( array(
					'on'  => 'On',
					'off' => 'Off',
				) )
				->selected( 'on' )
				->disabled( true )
		) ); ?>

		<!-- Required -->
		<?php $this->component( new Radio_Group_Component(
			Radio_Group::make( 'radio_required' )
				->label( 'Required Radio' )
				->options( array(
					'yes' => 'Yes',
					'no'  => 'No',
				) )
				->required( true )
		) ); ?>

		<!-- Notification, before/after -->
		<?php $this->component( new Radio_Group_Component(
			Radio_Group::make( 'radio_extras' )
				->label( 'Radio Extras' )
				->options( array( 'm' => 'M', 'n' => 'N' ) )
				->error_notification( 'Must choose' )
				->before( '<span class="radio-group-before">Pick:</span>' )
				->after( '<span class="radio-group-after">Done</span>' )
				->wrapper_data( 'group', 'radios' )
		) ); ?>

		<!-- Custom style -->
		<?php
		$custom_radio = new Radio_Group( 'radio_custom_style', new E2E_Custom_Style() );
		$custom_radio->label( 'Custom Styled' )
			->options( array( 'p' => 'P', 'q' => 'Q' ) )
			->selected( 'p' );
		$this->component( new Radio_Group_Component( $custom_radio ) );
		?>
	</div>

</div>
