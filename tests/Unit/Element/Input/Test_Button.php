<?php

declare(strict_types=1);

/**
 * Unit tests for the Button Input
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
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field\Input\Button;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Disabled, Read_Only, Required, Single_Value};

/**
 * @group unit
 * @group element
 * @group input
 */
class Test_Button extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Button::class;
	}

	/** @testdox A Button input should return an input type of "email" */
	public function test_type(): void {
		$button = new Button( 'test' );
		$this->assertEquals( 'button', $button->get_input_type() );
	}

	/** @testdox A Button input should return a type of email_input */
	public function test_element_type(): void {
		$button = new Button( 'test' );
		$this->assertEquals( 'button_input', $button->get_type() );
	}

	/** @testdox By default the Button field should sanitize values as integer using intval. */
	public function test_default_sanitizer(): void {
		$button = new Button( 'test' );
		$this->assertEquals( Sanitize::TEXT, $button->get_sanitizer() );
	}

	####################################################################
	######                     FIELD SPECIFIC                     ######
	####################################################################

	/** @testdox It should be possible to use an alias called text() to set the button value. */
	public function test_text_alias(): void {
		$button = new Button( 'test' );
		$button->text( 'test' );
		$this->assertEquals( 'test', $button->get_value() );
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
			'disabled'      => array( 'disabled' ),
		);
	}

	/**
	 * @testdox This input field has all attributes as defined by the shared traits
	 * @dataProvider attribute_methods
	 */
	public function test_has_attributes( string $method ): void {
		$class = $this->get_class_under_test();
		$instance = new $class( 'test' );
		$this->assertTrue( method_exists( $instance, $method ) );
	}


}
