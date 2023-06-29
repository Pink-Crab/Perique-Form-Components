<?php

declare(strict_types=1);

/**
 * Unit tests for the Week Input
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
use PinkCrab\Form_Components\Element\Field\Input\Week;
use PinkCrab\Form_Components\Element\Field\Attribute\{Range, Autocomplete, Placeholder, Datalist, Single_Value, Required, Read_Only};

/**
 * @group unit
 * @group element
 * @group input
 */
class Test_Week extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Week::class;
	}
	
	/** @testdox A Week input should return an input type of "email" */
	public function test_type(): void {
		$week = new Week( 'test' );
		$this->assertEquals( 'week', $week->get_input_type() );
	}

	/** @testdox A Week input should return a type of email_input */
	public function test_element_type(): void {
		$week = new Week( 'test' );
		$this->assertEquals( 'week_input', $week->get_type() );
	}

	/** @testdox By default the Week field should sanitize values as integer using intval. */
	public function test_default_sanitizer(): void {
		$week = new Week( 'test' );
		$this->assertInstanceOf( Closure::class, $week->get_sanitizer() );
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
        $week = new Week( 'test' );
        $this->assertTrue( method_exists( $week, $method ) );
    }


	####################################################################
	######                     FIELD SPECIFIC                     ######
	####################################################################

	/**
	 * Data provider for valid week values.
	 * 
	 * @return array<string, array{0:string,1:bool}>
	 */
	public function sanitizer_format_data(): array {
		return array(
			// Valid dates
			'valid_week' => array( '1983-W24', true ),
			'valid_week_with_leading_zero' => array( '1983-W02', true ),

			// Invalid dates
			'invalid_week_without_leading_zero' => array( '1983-W2', false ),
			'invalid_year_as_non_numerical' => array( 'YEAR-W24', false ),
			'invalid_week_as_non_numerical' => array( '1983-WEEK', false ),
			'invalid_week_as_less_than_0' => array( '1983--1', false ),
			'invalid_week_as_greater_than_53' => array( '1983-W54', false ),
		);
	}
	
	/** 
	 * @testdox By default the sanitizer format should be set to the standard HTML spec for this input type.
	 * @dataProvider sanitizer_format_data */
	public function test_default_sanitizer_format($value, $expected): void {
		$week = new Week( 'test' );
		$this->assertEquals( 
			$expected, 
			$week->set_existing( $value )->get_value() === $value );
	}

	/** @testdox It should be possible to set the step per week with a simple helper. */
	public function test_step_per_week(): void {
		$week = new Week( 'test' );
		$week->step_by_weeks( 2 );
		$this->assertEquals( 2, $week->get_step() );
	}

}
