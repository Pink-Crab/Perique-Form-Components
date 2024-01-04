<?php

declare(strict_types=1);

/**
 * Unit tests for the Time Input
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
use PinkCrab\Form_Components\Element\Field\Input\Time;
use PinkCrab\Form_Components\Element\Field\Attribute\{Range, Autocomplete, Placeholder, Datalist, Single_Value, Required, Read_Only};

/**
 * @group unit
 * @group element
 * @group input
 * @group time
 */
class Test_Time extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/**
 * @inheritDoc
*/
	public function get_class_under_test(): string {
		return Time::class;
	}

	/**
 * @testdox A Time input should return an input type of "time"
*/
	public function test_type(): void {
		$time = new Time( 'test' );
		$this->assertEquals( 'time', $time->get_input_type() );
	}

	/**
 * @testdox A Time input should return a type of time_input
*/
	public function test_element_type(): void {
		$time = new Time( 'test' );
		$this->assertEquals( 'time_input', $time->get_type() );
	}

	/**
 * @testdox By default the Time field should sanitize values as integer using intval.
*/
	public function test_default_sanitizer(): void {
		$time = new Time( 'test' );
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
		$time = new Time( 'test' );
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
			'valid_with_seconds'                          => array( '12:12:12', true ),
			'valid_without_seconds'                       => array( '11:11', true ),

			// Invalid times
			'invalid_time_too_many_hours_with_seconds'    => array( '25:00:00', false ),
			'invalid_time_too_many_minutes_with_seconds'  => array( '00:60:00', false ),
			'invalid_time_too_many_seconds_with_seconds'  => array( '00:00:60', false ),
			'invalid_time_too_many_hours_without_seconds' => array( '25:00', false ),
			'invalid_time_too_many_minutes_without_seconds' => array( '00:60', false ),
		);
	}

	/**
	 * @testdox By default the sanitizer format should be set to the standard HTML spec for this input type.
	 * @dataProvider sanitizer_format_data
*/
	public function test_default_sanitizer_format( $value, $expected ): void {
		$time = new Time( 'test' );

		$sanitized = $time->set_existing( $value )->get_value();

		// If $value doesn't contain seconds, add :00.
		if ( ! preg_match( '/\d{2}:\d{2}:\d{2}/', $value ) ) {
			$value .= ':00';
		}

		$this->assertEquals( $expected, $sanitized === $value );
	}

	/**
 * @testdox It should be possible to set the step value of a time field in seconds
*/
	public function test_set_step(): void {
		$time = new Time( 'test' );
		$time->step_by_seconds( 60 );
		$this->assertEquals( 60, $time->get_step() );
	}

	/**
 * @testdox It should be possible to set the step value of a time field in minutes
*/
	public function test_set_step_minutes(): void {
		$time = new Time( 'test' );
		$time->step_by_minutes( 60 );
		$this->assertEquals( 3600, $time->get_step() );
	}

	/**
 * @testdox It should be possible to set the step value of a time field in hours
*/
	public function test_set_step_hours(): void {
		$time = new Time( 'test' );
		$time->step_by_hours( 24 );
		$this->assertEquals( 86400, $time->get_step() );
	}
}
