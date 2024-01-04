<?php

declare(strict_types=1);

/**
 * Unit tests for the Tel Input
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
use PinkCrab\Form_Components\Element\Field\Input\Tel;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Single_Value, Autocomplete, Pattern, Datalist, Placeholder, Disabled, Read_Only, Required, Length};

/**
 * @group unit
 * @group element
 * @group input
 * @group tel
 */
class Test_Tel extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/**
 * @inheritDoc
*/
	public function get_class_under_test(): string {
		return Tel::class;
	}

	/**
 * @testdox A Tel input should return an input type of "tel"
*/
	public function test_type(): void {
		$tel = new Tel( 'test' );
		$this->assertEquals( 'tel', $tel->get_input_type() );
	}

	/**
 * @testdox A Tel input should return a type of tel_input
*/
	public function test_element_type(): void {
		$tel = new Tel( 'test' );
		$this->assertEquals( 'tel_input', $tel->get_type() );
	}

	/**
 * @testdox By default the tel field should sanitize values as strings.
*/
	public function test_default_sanitizer(): void {
		$tel = new Tel( 'test' );
		$this->assertEquals( Sanitize::TEXT, $tel->get_sanitizer() );
	}

	/**
 * @testdox A Tel field should allow a single value to be set
*/
	public function test_uses_single_value(): void {
		$tel = new Tel( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $tel ) );
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
		$tel = new Tel( 'test' );
		$this->assertTrue( method_exists( $tel, $method ) );
	}
}
