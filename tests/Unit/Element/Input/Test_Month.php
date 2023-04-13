<?php

declare(strict_types=1);

/**
 * Unit tests for the Month Input
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
use PinkCrab\Form_Components\Element\Field\Input\Month;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Range, Autocomplete, Placeholder, Datalist, Single_Value, Required, Read_Only};

/**
 * @group unit
 * @group element
 * @group input
 */
class Test_Month extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Month::class;
	}

	/** @testdox A Month input should return an input type of "email" */
	public function test_type(): void {
		$month = new Month( 'test' );
		$this->assertEquals( 'month', $month->get_input_type() );
	}

	/** @testdox A Month input should return a type of email_input */
	public function test_element_type(): void {
		$month = new Month( 'test' );
		$this->assertEquals( 'month_input', $month->get_type() );
	}

	/** @testdox By default the Month field should sanitize values as integer using intval. */
	public function test_default_sanitizer(): void {
		$month = new Month( 'test' );
		$this->assertInstanceOf( Closure::class, $month->get_sanitizer() );
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
        $month = new Month( 'test' );
        $this->assertTrue( method_exists( $month, $method ) );
    }


	####################################################################
	######                     FIELD SPECIFIC                     ######
	####################################################################

		/**
	 * Data provider for valid month values.
	 * 
	 * @return array<string, array{0:string,1:bool}>
	 */
	public function sanitizer_format_data(): array {
		return array(
			// Valid dates
			'valid_month' => array( '1983-12', true ),
			'valid_month_with_leading_zero' => array( '1983-02', true ),

			// Invalid dates
			'invalid_month_without_leading_zero' => array( '1983-2', false ),
			'invalid_year_as_non_numerical' => array( 'YEAR-12', false ),
			'invalid_month_as_non_numerical' => array( '1983-MONTH', false ),
			'invalid_month_as_less_than_0' => array( '1983--1', false ),
			'invalid_month_as_greater_than_12' => array( '1983-13', false ),
		);
	}
	
	/** 
	 * @testdox By default the sanitizer format should be set to the standard HTML spec for this input type.
	 * @dataProvider sanitizer_format_data */
	public function test_default_sanitizer_format($value, $expected): void {
		$month = new Month( 'test' );
		$this->assertEquals( 
			$expected, 
			$month->set_existing( $value )->get_value() === $value );
	}

	/** @testdox It should be possible to set the step by months with a simple helper */
	public function test_step_by_months(): void {
		$month = new Month( 'test' );
		$month->step_by_months( 2 );
		$this->assertEquals( 2, $month->get_step() );
	}
}
