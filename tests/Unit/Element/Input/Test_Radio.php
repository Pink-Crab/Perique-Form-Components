<?php

declare(strict_types=1);

/**
 * Unit tests for the Radio Input
 * Extends Abstract_Input
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Input;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Field\Input\Radio;
use PinkCrab\Form_Components\Tests\Fixtures\Mock_Objects\Stringable_Stub;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Checked, Disabled};

/**
 * @group unit
 * @group element
 * @group input
 */
class Test_Radio extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;
	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Description_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Radio::class;
	}

	/** @testdox A Radio input should return an input type of "radio" */
	public function test_type(): void {
		$radio = new Radio( 'test' );
		$this->assertEquals( 'radio', $radio->get_input_type() );
	}

	/** @testdox A Radio input should return a type of radio_input */
	public function test_element_type(): void {
		$radio = new Radio( 'test' );
		$this->assertEquals( 'radio_input', $radio->get_type() );
	}

	/** @testdox By default the radio field should have no sanitizer. */
	public function test_default_sanitizer(): void {
		$radio = new Radio( 'test' );
		$this->assertNull( $radio->get_sanitizer() );
	}

	####################################################################
	######                     FIELD SPECIFIC                     ######
	####################################################################

	/**
	 * Data provider for ensuring the value is set as a string.
	 *
	 * @return array<string, array{0:string,1:mixed}>
	 */
	public function data_provider(): array {
		return array(
			'String'     => array( 'a_string', 'a_string' ),
			'Integer'    => array( '1', 1 ),
			'Float'      => array( '1.1', 1.1 ),
			'Stringable' => array( 'stringable', new Stringable_Stub( 'stringable' ) ),
		);
	}

	/**
	 * @testdox All values passed to the Radio field should be cast to string.
	 * @dataProvider data_provider
	 */
	public function test_value_cast_to_string( string $expected, $value ): void {
		$radio = new Radio( 'test' );
		$radio->value( $value );
		$this->assertEquals( $expected, $radio->get_value() );
	}

	####################################################################
	######                    CHECKED TRAIT                       ######
	####################################################################

	/** @testdox It should be possible to set a radio as checked */
	public function test_checked_set(): void {
		$radio = new Radio( 'test' );
		$this->assertFalse( $radio->is_checked() );
		$radio->checked();
		$this->assertTrue( $radio->is_checked() );
	}

	/** @testdox It should be possible to uncheck a radio */
	public function test_checked_unset(): void {
		$radio = new Radio( 'test' );
		$radio->checked( true );
		$this->assertTrue( $radio->is_checked() );
		$radio->checked( false );
		$this->assertFalse( $radio->is_checked() );
	}

	####################################################################
	######                   DISABLED TRAIT                       ######
	####################################################################

	/** @testdox It should be possible to set a radio as disabled */
	public function test_disabled_set(): void {
		$radio = new Radio( 'test' );
		$this->assertFalse( $radio->is_disabled() );
		$radio->disabled();
		$this->assertTrue( $radio->is_disabled() );
	}

	/** @testdox It should be possible to unset disabled on a radio */
	public function test_disabled_unset(): void {
		$radio = new Radio( 'test' );
		$radio->disabled( true );
		$this->assertTrue( $radio->is_disabled() );
		$radio->disabled( false );
		$this->assertFalse( $radio->is_disabled() );
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
		$radio = new Radio( 'test' );
		$this->assertTrue( method_exists( $radio, $method ) );
	}
}
