<?php

declare(strict_types=1);

/**
 * Unit tests for the Hidden Input
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
use PinkCrab\Form_Components\Element\Field\Input\Hidden;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Single_Value, Autocomplete, Pattern, Datalist, Placeholder, Disabled, Read_Only, Required, Length};

/**
 * @group unit
 * @group element
 * @group input
 * @group hidden
 */
class Test_Hidden extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/**
 * @inheritDoc
*/
	public function get_class_under_test(): string {
		return Hidden::class;
	}

	/**
 * @testdox A Text input should return an input type of "TEXT"
*/
	public function test_type(): void {
		$text = new Hidden( 'test' );
		$this->assertEquals( 'hidden', $text->get_input_type() );
	}

	/**
 * @testdox A Text input should return a type of text_input
*/
	public function test_element_type(): void {
		$text = new Hidden( 'test' );
		$this->assertEquals( 'hidden_input', $text->get_type() );
	}

	/**
 * @testdox A Text field should allow a single value to be set
*/
	public function test_uses_single_value(): void {
		$text = new Hidden( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $text ) );
	}

}
