<?php

declare(strict_types=1);

/**
 * Unit tests for the Date Input
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
use PinkCrab\Form_Components\Element\Field\Input\Date;

/**
 * @group unit
 * @group element
 * @group input
 */
class Test_Date extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Date::class;
	}
	
	/** @testdox A Date input should return an input type of "email" */
	public function test_type(): void {
		$time = new Date( 'test' );
		$this->assertEquals( 'date', $time->get_input_type() );
	}

	/** @testdox A Date input should return a type of email_input */
	public function test_element_type(): void {
		$time = new Date( 'test' );
		$this->assertEquals( 'date_input', $time->get_type() );
	}

	/** @testdox By default the Date field should sanitize values as integer using intval. */
	public function test_default_sanitizer(): void {
		$time = new Date( 'test' );
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
            'autocomplete' => array( 'autocomplete' ),
            'datalist_item' => array( 'datalist_item' ),
            'min' => array( 'min' ),
            'max' => array( 'max' ),
            'step' => array( 'step' ),
            'readonly' => array( 'readonly' ),
            'required' => array( 'required' ),
        );
    }

    /** 
     * @testdox This input field has all attributes as defined by the shared traits
     * @dataProvider attribute_methods
     */
    public function test_has_attributes( string $method ): void {
        $date = new Date( 'test' );
        $this->assertTrue( method_exists( $date, $method ) );
    }


	####################################################################
	######                     FIELD SPECIFIC                     ######
	####################################################################

	/**
	 * Data provider for valid date values.
	 * 
	 * @return array<string, array{0:string,1:bool}>
	 */
	public function sanitizer_format_data(): array {
		return array(
			// Valid dates
			'valid_date' => array( '1983-12-12', true ),
			'valid_date_with_leading_zero' => array( '1983-12-01', true ),

			// Invalid dates
			'invalid_date_without_leading_zero_on_day' => array( '1983-12-1', false ),
			'invalid_date_without_leading_zero_on_month' => array( '1983-1-12', false ),
			'invalid_date_without_leading_zero_on_year' => array( '198-12-12', false ),
			'invalid_date_with_leading_zero_on_year' => array( '01983-12-12', false ),
			'invalid_date_with_none_numerical_month' => array( '1983-aa-12', false ),
			'invalid_date_with_none_numerical_day' => array( '1983-12-aa', false ),
			'invalid_date_with_none_numerical_year' => array( 'aa83-12-12', false ), 
		);
	}
	
	/** 
	 * @testdox By default the sanitizer format should be set to the standard HTML spec for this input type.
	 * @dataProvider sanitizer_format_data */
	public function test_default_sanitizer_format($value, $expected): void {
		$week = new Date( 'test' );
		$this->assertEquals( 
			$expected, 
			$week->set_existing( $value )->get_value() === $value );
	}

	/** @testdox It should be possible to set the step per day with a simple helper. */
	public function test_step_per_week(): void {
		$week = new Date( 'test' );
		$week->step_by_days( 2 );
		$this->assertEquals( 2, $week->get_step() );
	}

	/** @testdox It should be possible to set the step per week with a simple helper. */
	public function test_step_per_month(): void {
		$week = new Date( 'test' );
		$week->step_by_weeks( 2 );
		$this->assertEquals( 14, $week->get_step() );
	}


}
