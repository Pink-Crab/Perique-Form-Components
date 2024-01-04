<?php

declare(strict_types=1);

/**
 * Unit tests for the Text Input
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
use PinkCrab\Form_Components\Element\Field\Input\Text;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Single_Value, Autocomplete, Pattern, Datalist, Placeholder, Disabled, Read_Only, Required, Length};

/**
 * @group unit
 * @group element
 * @group input
 * @group text
 */
class Test_Text extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/**
 * @inheritDoc
*/
	public function get_class_under_test(): string {
		return Text::class;
	}

	/**
 * @testdox A Text input should return an input type of "TEXT"
*/
	public function test_type(): void {
		$text = new Text( 'test' );
		$this->assertEquals( 'text', $text->get_input_type() );
	}

	/**
 * @testdox A Text input should return a type of text_input
*/
	public function test_element_type(): void {
		$text = new Text( 'test' );
		$this->assertEquals( 'text_input', $text->get_type() );
	}

	/**
 * @testdox By default the text field should sanitize values as strings.
*/
	public function test_default_sanitizer(): void {
		$text = new Text( 'test' );
		$this->assertEquals( Sanitize::TEXT, $text->get_sanitizer() );
	}

	/**
 * @testdox A Text field should allow a single value to be set
*/
	public function test_uses_single_value(): void {
		$text = new Text( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $text ) );
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
			'maxlength'     => array( 'maxlength' ),
			'minlength'     => array( 'minlength' ),
			'pattern'       => array( 'pattern' ),
			'placeholder'   => array( 'placeholder' ),
			'readonly'      => array( 'readonly' ),
			'required'      => array( 'required' ),
			'size'          => array( 'size' ),
		);
	}

	/**
	 * @testdox This input field has all attributes as defined by the shared traits
	 * @dataProvider attribute_methods
	 */
	public function test_has_attributes( string $method ): void {
		$text = new Text( 'test' );
		$this->assertTrue( method_exists( $text, $method ) );
	}
}
