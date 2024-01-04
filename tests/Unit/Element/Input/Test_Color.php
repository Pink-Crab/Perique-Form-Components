<?php

declare(strict_types=1);

/**
 * Unit tests for the Color Input
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
use PinkCrab\Form_Components\Element\Field\Input\Color;

/**
 * @group unit
 * @group element
 * @group input
 * @group color
 */
class Test_Color extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Color::class;
	}

	/** @testdox A Color input should return an input type of "color" */
	public function test_type(): void {
		$color = new Color( 'test' );
		$this->assertEquals( 'color', $color->get_input_type() );
	}

	/** @testdox A Color input should return a type of color_input */
	public function test_element_type(): void {
		$color = new Color( 'test' );
		$this->assertEquals( 'color_input', $color->get_type() );
	}

	/** @testdox By default the Color field should sanitize values as integer using intval. */
	public function test_default_sanitizer(): void {
		$color = new Color( 'test' );
		$this->assertEquals( Sanitize::HEX_COLOR, $color->get_sanitizer() );
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

	####################################################################
	######                     FIELD SPECIFIC                     ######
	####################################################################

	/**
	 * Data provider for valid field values.
	 *
	 * @return array<string, array{0:string,1:bool}>
	 */
	public function sanitizer_format_data(): array {
		return array(
			// Valid colors
			'Valid Color Short'         => array( '#fff', true ),
			'Valid Color Full'          => array( '#cccccc', true ),

			// Invalid colors
			'Invalid Color Using Alpha' => array( '#ccccccff', false ),
			'Invalid Color Too Long'    => array( '#ccccccccccccccc', false ),
			'Invalid Color Without #'   => array( 'ffffff', false ),
			'Invalid Color With Alpha'  => array( 'rgba(255,255,255,0.5)', false ),
			'Invalid Color As Name'  => array( 'red', false ),

		);
	}

	/**
	 * @testdox By default the sanitizer format should be set to the standard HTML spec for this input type.
	 * @dataProvider sanitizer_format_data 
	 */
	public function test_default_sanitizer_format( $value, $expected ): void {
		$class = $this->get_class_under_test();
		$instance = new $class( 'test' );
		$this->assertEquals(
			$expected,
			$instance->set_existing( $value )->get_value() === $value
		);
	}
}
