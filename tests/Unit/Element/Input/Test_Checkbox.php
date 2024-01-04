<?php

declare(strict_types=1);

/**
 * Unit tests for the Checkbox Input
 * Extends Abstract_Input
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Input;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Field\Input\Checkbox;
use PinkCrab\Form_Components\Tests\Fixtures\Mock_Objects\Stringable_Stub;
/**
 * @group unit
 * @group element
 * @group input
 * @group checkbox
 */
class Test_Checkbox extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/**
 * @inheritDoc
*/
	public function get_class_under_test(): string {
		return Checkbox::class;
	}

	/**
 * @testdox A Checkbox input should return an input type of "email"
*/
	public function test_type(): void {
		$checkbox = new Checkbox( 'test' );
		$this->assertEquals( 'checkbox', $checkbox->get_input_type() );
	}

	/**
 * @testdox A Checkbox input should return a type of email_input
*/
	public function test_element_type(): void {
		$checkbox = new Checkbox( 'test' );
		$this->assertEquals( 'checkbox_input', $checkbox->get_type() );
	}

	/**
 * @testdox By default the Checkbox field should sanitize values as integer using intval.
*/
	public function test_default_sanitizer(): void {
		$checkbox = new Checkbox( 'test' );
		$this->assertNull( $checkbox->get_sanitizer() );
	}

	####################################################################
	######                     FIELD SPECIFIC                     ######
	####################################################################

	/**
	 * Data provider for ensuring the value is set as a string.
	 *
	 * @return array<string, array{0:string,1:string}>
	 */
	public function data_provider(): array {
		return array(
			// array{expected, value}
			'String'     => array( 'a_string', 'a_string' ),
			'Integer'    => array( '1', 1 ),
			'Float'      => array( '1.1', 1.1 ),
			'Stringable' => array( 'stringable', new Stringable_Stub( 'stringable' ) ),
			'Bool|True'  => array( '1', true ),
			'Bool|False' => array( '', false ),
			'Null'       => array( '', null ),
		);
	}

	/**
	 * @testdox All values passed to the Checkbox field should be sanitized using the default sanitizer.
	 * @dataProvider data_provider
	 */
	public function test_value_sanitized( string $expected, $value ): void {
		$checkbox = new Checkbox( 'test' );
		$checkbox->value( $value );
		$this->assertEquals( $expected, $checkbox->get_value() );
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
			'checked'  => array( 'checked' ),
			'disabled' => array( 'disabled' ),
		);
	}

	/**
	 * @testdox This input field has all attributes as defined by the shared traits
	 * @dataProvider attribute_methods
	 */
	public function test_has_attributes( string $method ): void {
		$checkbox = new Checkbox( 'test' );
		$this->assertTrue( method_exists( $checkbox, $method ) );
	}
}
