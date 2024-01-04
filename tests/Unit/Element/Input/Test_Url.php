<?php

declare(strict_types=1);

/**
 * Unit tests for the Url Input
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
use PinkCrab\Form_Components\Element\Field\Input\Url;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Single_Value, Autocomplete, Pattern, Datalist, Placeholder, Disabled, Read_Only, Required, Length};

/**
 * @group unit
 * @group element
 * @group input
 * @group url
 */
class Test_Url extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/**
 * @inheritDoc
*/
	public function get_class_under_test(): string {
		return Url::class;
	}

	/**
 * @testdox A Url input should return an input type of "url"
*/
	public function test_type(): void {
		$url = new Url( 'test' );
		$this->assertEquals( 'url', $url->get_input_type() );
	}

	/**
 * @testdox A Url input should return a type of url_input
*/
	public function test_element_type(): void {
		$url = new Url( 'test' );
		$this->assertEquals( 'url_input', $url->get_type() );
	}

	/**
 * @testdox By default the url field should sanitize values as strings.
*/
	public function test_default_sanitizer(): void {
		$url = new Url( 'test' );
		$this->assertEquals( Sanitize::TEXT, $url->get_sanitizer() );
	}

	/**
 * @testdox A Url field should allow a single value to be set
*/
	public function test_uses_single_value(): void {
		$url = new Url( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $url ) );
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
		$url = new Url( 'test' );
		$this->assertTrue( method_exists( $url, $method ) );
	}
}
