<?php

declare(strict_types=1);

/**
 * Unit tests for the DateTime Input
 * Extends Abstract_Input
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Input;

use Closure;
use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Field\Input\DateTime;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Range, Autocomplete, Placeholder, Datalist, Single_Value, Required, Read_Only};

/**
 * @group unit
 * @group element
 * @group input
 */
class Test_DateTime extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return DateTime::class;
	}
	
	/** @testdox A DateTime input should return an input type of "email" */
	public function test_type(): void {
		$time = new DateTime( 'test' );
		$this->assertEquals( 'datetime-local', $time->get_input_type() );
	}

	/** @testdox A DateTime input should return a type of email_input */
	public function test_element_type(): void {
		$time = new DateTime( 'test' );
		$this->assertEquals( 'datetime-local_input', $time->get_type() );
	}

	/** @testdox By default the DateTime field should sanitize values as integer using intval. */
	public function test_default_sanitizer(): void {
		$time = new DateTime( 'test' );
		$this->assertInstanceOf( Closure::class, $time->get_sanitizer() );
	}

	####################################################################
	######                    SHARED ATTRIBUTES                   ######
	####################################################################

	/**
	 * The methods that should be defined by the traits to represent shared attributes.
	 *
	 * @return array<string, array<string>>
	 */
	public function attribute_methods(): array {
		return array(
			'autocomplete'  => array( 'autocomplete' ),
			'datalist_item' => array( 'datalist_item' ),
			'min'           => array( 'min' ),
			'max'           => array( 'max' ),
			'step'          => array( 'step' ),
			'readonly'      => array( 'readonly' ),
			'required'      => array( 'required' ),
		);
	}

	/**
	 * @testdox This input field has all attributes as defined by the shared traits
	 * @dataProvider attribute_methods
	 */
	public function test_has_attributes( string $method ): void {
		$time = new DateTime( 'test' );
		$this->assertTrue( method_exists( $time, $method ) );
	}



	####################################################################
	######                     FIELD SPECIFIC                     ######
	####################################################################

	/**
	 * Data provider for valid time values.
	 *
	 * @return array<string, array{0:string,1:bool}>
	 */
	public function sanitizer_format_data(): array {
		return array(
			// Valid times
			'valid_with_seconds'                          => array( '2022-12-23T12:12:12', true, false ),
			'valid_without_seconds'                       => array( '2022-12-23T11:11', true, true ),

			// Invalid times
			'invalid_time_too_many_hours_with_seconds'    => array( '2022-12-23T25:00:00', false, false ),
			'invalid_time_too_many_minutes_with_seconds'  => array( '2022-12-23T00:60:00', false, false ),
			'invalid_time_too_many_seconds_with_seconds'  => array( '2022-12-23T00:00:60', false, false ),
			'invalid_time_too_many_hours_without_seconds' => array( '2022-12-23T25:00', false, true ),
			'invalid_time_too_many_minutes_without_seconds' => array( '2022-12-23T00:60', false, true ),
			'invalid_date_too_many_days'                  => array( '2022-12-32T00:00', false, true ),
			'invalid_date_too_many_months'                => array( '2022-13-23T00:00', false, true ),
			'invalid_date_too_many_years'                 => array( '20220-12-23T00:00', false, true ),
		);
	}

	/**
	 * @testdox By default the sanitizer format should be set to the standard HTML spec for this input type.
	 * @dataProvider sanitizer_format_data */
	public function test_default_sanitizer_format( $value, $expected, $add_seconds ): void {
		$time = new DateTime( 'test' );

		$sanitized = $time->set_existing( $value )->get_value();

		// If we need to add the seconds (based on the format of sanitization)
		if ( $add_seconds ) {
			$value .= ':00';
		}

		$this->assertEquals( $expected, $sanitized === $value );
	}


	/** @testdox It should be possible to set the step value of a time field in seconds */
	public function test_set_step(): void {
		$time = new DateTime( 'test' );
		$time->step_by_seconds( 60 );
		$this->assertEquals( 60, $time->get_step() );
	}

	/** @testdox It should be possible to set the step value of a time field in minutes */
	public function test_set_step_minutes(): void {
		$time = new DateTime( 'test' );
		$time->step_by_minutes( 60 );
		$this->assertEquals( 3600, $time->get_step() );
	}

	/** @testdox It should be possible to set the step value of a time field in hours */
	public function test_set_step_hours(): void {
		$time = new DateTime( 'test' );
		$time->step_by_hours( 24 );
		$this->assertEquals( 86400, $time->get_step() );
	}

	/** @testdox It should be possible to set the step value of a time field in days */
	public function test_set_step_days(): void {
		$time = new DateTime( 'test' );
		$time->step_by_days( 7 );
		$this->assertEquals( 604800, $time->get_step() );
	}

	/** @testdox It should be possible to set the step value of a time field in weeks */
	public function test_set_step_weeks(): void {
		$time = new DateTime( 'test' );
		$time->step_by_weeks( 4 );
		$this->assertEquals( 2419200, $time->get_step() );
	}
	

}
