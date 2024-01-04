<?php

declare(strict_types=1);

/**
 * Unit tests for the Number Input
 * Extends Abstract_Input
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Input;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field\Input\Number;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Single_Value, Range, Placeholder, Autocomplete, Datalist, Required, Read_Only};

/**
 * @group unit
 * @group element
 * @group input
 * @group number
 */
class Test_Number extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/**
 * @inheritDoc
*/
	public function get_class_under_test(): string {
		return Number::class;
	}

	/**
 * @testdox A Number input should return an input type of "number"
*/
	public function test_type(): void {
		$number = new Number( 'test' );
		$this->assertEquals( 'number', $number->get_input_type() );
	}

	/**
 * @testdox A Number input should return a type of number_input
*/
	public function test_element_type(): void {
		$number = new Number( 'test' );
		$this->assertEquals( 'number_input', $number->get_type() );
	}

	/**
 * @testdox By default the Number field should sanitize values as integer using intval.
*/
	public function test_default_sanitizer(): void {
		$number = new Number( 'test' );
		$this->assertEquals( Sanitize::NUMBER, $number->get_sanitizer() );
	}

	/**
 * @testdox A Number field should allow a single value to be set
*/
	public function test_uses_single_value(): void {
		$number = new Number( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $number ) );
	}

	/**
 * @testdox A Number field should allow the use of the Autocomplete attribute.
*/
	public function test_uses_autocomplete(): void {
		$number = new Number( 'test' );
		$this->assertTrue( usesTrait( Autocomplete::class )( $number ) );
	}

	/**
 * @testdox A Number field should allow the use of the Datalist attribute
*/
	public function test_uses_datalist(): void {
		$number = new Number( 'test' );
		$this->assertTrue( usesTrait( Datalist::class )( $number ) );
	}

	/**
 * @testdox A Number field should allow the use of the Placeholder attribute
*/
	public function test_uses_placeholder(): void {
		$number = new Number( 'test' );
		$this->assertTrue( usesTrait( Placeholder::class )( $number ) );
	}

	/**
 * @testdox A Number field should allow the use of the Range attribute
*/
	public function test_uses_range(): void {
		$number = new Number( 'test' );
		$this->assertTrue( usesTrait( Range::class )( $number ) );
	}

	/**
 * @testdox A Number field should allow the use of the Required attribute
*/
	public function test_uses_required(): void {
		$number = new Number( 'test' );
		$this->assertTrue( usesTrait( Required::class )( $number ) );
	}

	/**
 * @testdox A Number field should allow the use of the Read_Only attribute
*/
	public function test_uses_read_only(): void {
		$number = new Number( 'test' );
		$this->assertTrue( usesTrait( Read_Only::class )( $number ) );
	}
}
