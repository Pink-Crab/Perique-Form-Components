<?php
/**
 * Numeric Inputs - Kitchen Sink
 *
 * Number and Range with all their traits.
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Number;
use PinkCrab\Form_Components\Element\Field\Input\Range;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-numeric-inputs">

	<!-- ===== NUMBER ===== -->
	<div class="e2e-number-section">
		<?php $this->component( new Input_Component(
			Number::make( 'number_basic' )
				->label( 'Number' )
				->add_class( 'e2e-number-basic' )
				->set_existing( '42' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Number::make( 'number_full' )
				->label( 'Full Number' )
				->add_class( 'e2e-number-full' )
				->min( 0 )
				->max( 1000 )
				->step( 5 )
				->autocomplete( 'off' )
				->datalist_items( array( '10', '50', '100', '500' ) )
				->required( true )
				->readonly( true )
				->placeholder( '0-1000' )
				->set_existing( '250' )
		) ); ?>

		<?php
		$custom_number = new Number( 'number_custom_style', new E2E_Custom_Style() );
		$custom_number->label( 'Custom Number' )->add_class( 'e2e-number-custom' )->set_existing( '99' );
		$this->component( new Input_Component( $custom_number ) );
		?>
	</div>

		<!-- Number with notification, before/after, wrapper attrs -->
		<?php $this->component( new Input_Component(
			Number::make( 'number_extras' )
				->label( 'Number Extras' )
				->add_class( 'e2e-number-extras' )
				->error_notification( 'Out of range' )
				->before( '<span class="number-before">$</span>' )
				->after( '<span class="number-after">.00</span>' )
				->id( 'custom-number-id' )
				->wrapper_id( 'custom-number-wrapper' )
				->wrapper_data( 'currency', 'usd' )
				->data( 'step-size', '5' )
		) ); ?>

		<!-- Number no wrapper -->
		<?php $this->component( new Input_Component(
			Number::make( 'number_no_wrapper' )
				->add_class( 'e2e-number-no-wrapper' )
				->show_wrapper( false )
		) ); ?>
	</div>

	<!-- ===== RANGE ===== -->
	<div class="e2e-range-section">
		<?php $this->component( new Input_Component(
			Range::make( 'range_basic' )
				->label( 'Range' )
				->add_class( 'e2e-range-basic' )
				->min( 0 )
				->max( 100 )
				->set_existing( '50' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Range::make( 'range_full' )
				->label( 'Full Range' )
				->add_class( 'e2e-range-full' )
				->min( 0 )
				->max( 200 )
				->step( 10 )
				->autocomplete( 'off' )
				->datalist_items( array( '0', '50', '100', '150', '200' ) )
				->required( true )
				->set_existing( '100' )
		) ); ?>
		<!-- Range with notification and wrapper attrs -->
		<?php $this->component( new Input_Component(
			Range::make( 'range_extras' )
				->label( 'Range Extras' )
				->add_class( 'e2e-range-extras' )
				->info_notification( 'Slide to adjust' )
				->before( '<span class="range-min">0</span>' )
				->after( '<span class="range-max">200</span>' )
				->wrapper_data( 'type', 'slider' )
		) ); ?>
	</div>

</div>
