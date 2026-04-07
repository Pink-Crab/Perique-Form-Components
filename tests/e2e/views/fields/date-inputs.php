<?php
/**
 * Date & Time Inputs - Kitchen Sink
 *
 * Date, Time, Datetime, Month, Week with all their traits.
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Date;
use PinkCrab\Form_Components\Element\Field\Input\Time;
use PinkCrab\Form_Components\Element\Field\Input\Datetime;
use PinkCrab\Form_Components\Element\Field\Input\Month;
use PinkCrab\Form_Components\Element\Field\Input\Week;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-date-inputs">

	<!-- ===== DATE ===== -->
	<div class="e2e-date-section">
		<?php $this->component( new Input_Component(
			Date::make( 'date_basic' )
				->label( 'Date' )
				->add_class( 'e2e-date-basic' )
				->set_existing( '2026-01-15' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Date::make( 'date_full' )
				->label( 'Full Date' )
				->add_class( 'e2e-date-full' )
				->min( '2020-01-01' )
				->max( '2030-12-31' )
				->step( 7 )
				->autocomplete( 'bday' )
				->datalist_items( array( '2026-01-01', '2026-06-15', '2026-12-25' ) )
				->required( true )
				->readonly( true )
				->disabled( true )
				->set_existing( '2026-06-15' )
		) ); ?>
	</div>

		<!-- Date with notification, before/after, wrapper attrs -->
		<?php $this->component( new Input_Component(
			Date::make( 'date_extras' )
				->label( 'Date Extras' )
				->add_class( 'e2e-date-extras' )
				->warning_notification( 'Date in the past' )
				->before( '<span class="date-icon">Cal</span>' )
				->after( '<span class="date-hint">YYYY-MM-DD</span>' )
				->id( 'custom-date-id' )
				->wrapper_data( 'format', 'iso' )
		) ); ?>
	</div>

	<!-- ===== TIME ===== -->
	<div class="e2e-time-section">
		<?php $this->component( new Input_Component(
			Time::make( 'time_basic' )
				->label( 'Time' )
				->add_class( 'e2e-time-basic' )
				->set_existing( '14:30' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Time::make( 'time_full' )
				->label( 'Full Time' )
				->add_class( 'e2e-time-full' )
				->min( '09:00' )
				->max( '17:00' )
				->step( 900 )
				->autocomplete( 'off' )
				->datalist_items( array( '09:00', '12:00', '17:00' ) )
				->required( true )
				->readonly( true )
				->disabled( true )
				->inputmode( 'numeric' )
				->set_existing( '12:00' )
		) ); ?>
	</div>

		<!-- Time with notification and before/after -->
		<?php $this->component( new Input_Component(
			Time::make( 'time_extras' )
				->label( 'Time Extras' )
				->add_class( 'e2e-time-extras' )
				->info_notification( 'Business hours only' )
				->before( '<span class="time-icon">Clock</span>' )
				->wrapper_data( 'period', 'business' )
		) ); ?>
	</div>

	<!-- ===== DATETIME ===== -->
	<div class="e2e-datetime-section">
		<?php $this->component( new Input_Component(
			Datetime::make( 'datetime_basic' )
				->label( 'DateTime' )
				->add_class( 'e2e-datetime-basic' )
				->set_existing( '2026-01-15T14:30' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Datetime::make( 'datetime_full' )
				->label( 'Full DateTime' )
				->add_class( 'e2e-datetime-full' )
				->min( '2020-01-01T00:00' )
				->max( '2030-12-31T23:59' )
				->step( 3600 )
				->autocomplete( 'off' )
				->datalist_items( array( '2026-01-01T09:00', '2026-06-15T12:00' ) )
				->required( true )
				->readonly( true )
				->set_existing( '2026-06-15T12:00' )
		) ); ?>
	</div>

		<!-- Datetime with notification -->
		<?php $this->component( new Input_Component(
			Datetime::make( 'datetime_extras' )
				->label( 'Datetime Extras' )
				->add_class( 'e2e-datetime-extras' )
				->success_notification( 'Valid datetime' )
				->after( '<span class="datetime-tz">UTC</span>' )
		) ); ?>
	</div>

	<!-- ===== MONTH ===== -->
	<div class="e2e-month-section">
		<?php $this->component( new Input_Component(
			Month::make( 'month_basic' )
				->label( 'Month' )
				->add_class( 'e2e-month-basic' )
				->set_existing( '2026-01' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Month::make( 'month_full' )
				->label( 'Full Month' )
				->add_class( 'e2e-month-full' )
				->autocomplete( 'off' )
				->min( '2020-01' )
				->max( '2030-12' )
				->step( 3 )
				->datalist_items( array( '2026-01', '2026-06', '2026-12' ) )
				->required( true )
				->readonly( true )
				->disabled( true )
				->inputmode( 'numeric' )
				->set_existing( '2026-06' )
		) ); ?>
	</div>

		<!-- Month with notification -->
		<?php $this->component( new Input_Component(
			Month::make( 'month_extras' )
				->label( 'Month Extras' )
				->add_class( 'e2e-month-extras' )
				->error_notification( 'Expired month' )
		) ); ?>
	</div>

	<!-- ===== WEEK ===== -->
	<div class="e2e-week-section">
		<?php $this->component( new Input_Component(
			Week::make( 'week_basic' )
				->label( 'Week' )
				->add_class( 'e2e-week-basic' )
				->set_existing( '2026-W03' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Week::make( 'week_full' )
				->label( 'Full Week' )
				->add_class( 'e2e-week-full' )
				->autocomplete( 'off' )
				->min( '2020-W01' )
				->max( '2030-W52' )
				->step( 2 )
				->datalist_items( array( '2026-W01', '2026-W26', '2026-W52' ) )
				->required( true )
				->readonly( true )
				->disabled( true )
				->inputmode( 'numeric' )
				->set_existing( '2026-W26' )
		) ); ?>
		<!-- Week with notification -->
		<?php $this->component( new Input_Component(
			Week::make( 'week_extras' )
				->label( 'Week Extras' )
				->add_class( 'e2e-week-extras' )
				->warning_notification( 'Holiday week' )
		) ); ?>
	</div>

</div>
