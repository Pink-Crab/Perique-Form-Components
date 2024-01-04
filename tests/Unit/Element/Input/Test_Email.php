<?php

declare(strict_types=1);

/**
 * Unit tests for the Email Input
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
use PinkCrab\Form_Components\Element\Field\Input\Email;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Single_Value, Multiple, Datalist, Placeholder, Read_Only, Disabled, Length, Required};

/**
 * @group unit
 * @group element
 * @group input
 * @group email
 */
class Test_Email extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/**
 * @inheritDoc
*/
	public function get_class_under_test(): string {
		return Email::class;
	}

	/**
 * @testdox A Email input should return an input type of "email"
*/
	public function test_type(): void {
		$email = new Email( 'test' );
		$this->assertEquals( 'email', $email->get_input_type() );
	}

	/**
 * @testdox A Email input should return a type of email_input
*/
	public function test_element_type(): void {
		$email = new Email( 'test' );
		$this->assertEquals( 'email_input', $email->get_type() );
	}

	/**
 * @testdox By default the Email field should sanitize values as integer using intval.
*/
	public function test_default_sanitizer(): void {
		$email = new Email( 'test' );
		$this->assertInstanceOf( Closure::class, $email->get_sanitizer() );
	}

	/**
	 * @testdox All values passed to the Email field should be sanitized using the default sanitizer.
	 * @dataProvider data_provider
	 */
	public function test_default_sanitizer_applied( $expected, $value ): void {
		$email = new Email( 'test' );
		$this->assertEquals( $expected, $email->sanitize( $value ) );
	}

	/**
	 * Data provider for test_default_sanitizer_applied
	 *
	 * @return array
	 */
	public function data_provider(): array {
		return array(
			'single email thats clean'               => array( 'g@g.com', 'g@g.com' ),
			'single email with spaces'               => array( 'g@g.com', '    g@g.com     ' ),
			'two emails that are clean'              => array( 'a@a.com,b@b.com', 'a@a.com,b@b.com' ),
			'two emails with spaces'                 => array( 'a@a.com,b@b.com', '    a@a.com  ,      b@b.com    ' ),
			'three emails but one not a valid email' => array( 'c@c.com', 'f@@.com,c@c.com,g.com' ),
			'returns empty string if not string'     => array( '', 123 ),
		);
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
			'multiple'      => array( 'multiple' ),
		);
	}

	/**
	 * @testdox This input field has all attributes as defined by the shared traits
	 * @dataProvider attribute_methods
	 */
	public function test_has_attributes( string $method ): void {
		$url = new Email( 'test' );
		$this->assertTrue( method_exists( $url, $method ) );
	}
}
