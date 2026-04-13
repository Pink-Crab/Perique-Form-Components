<?php

declare(strict_types=1);

/**
 * Unit tests for the Textarea Field
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field\Textarea;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Label, Single_Value, Placeholder, Disabled, Read_Only, Required, Length, Spellcheck, Autocomplete};

/**
 * @group unit
 * @group element
 * @group textarea
 */
class Test_Textarea extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;
	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Description_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Textarea::class;
	}

	/** @testdox A Textarea should return a type of "textarea" */
	public function test_type(): void {
		$textarea = new Textarea( 'test' );
		$this->assertEquals( 'textarea', $textarea->get_type() );
	}

	/** @testdox By default the textarea field should sanitize values using the TEXTAREA sanitizer. */
	public function test_default_sanitizer(): void {
		$textarea = new Textarea( 'test' );
		$this->assertEquals( Sanitize::TEXTAREA, $textarea->get_sanitizer() );
	}

	/** @testdox A Textarea field should allow a single value to be set */
	public function test_uses_single_value(): void {
		$textarea = new Textarea( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $textarea ) );
	}

	/** @testdox It should be possible to set an existing value on the textarea */
	public function test_set_existing(): void {
		$textarea = new Textarea( 'test' );
		$textarea->set_existing( 'some text value' );
		$this->assertEquals( 'some text value', $textarea->get_value() );
	}

	####################################################################
	######                       ROWS / COLS                      ######
	####################################################################

	/** @testdox It should be possible to set the number of rows on a textarea */
	public function test_set_rows(): void {
		$textarea = new Textarea( 'test' );
		$textarea->rows( 10 );
		$this->assertEquals( 10, $textarea->get_rows() );
	}

	/** @testdox It should be possible to check if rows has been set */
	public function test_has_rows(): void {
		$textarea = new Textarea( 'test' );
		$this->assertFalse( $textarea->has_rows() );
		$textarea->rows( 10 );
		$this->assertTrue( $textarea->has_rows() );
	}

	/** @testdox It should be possible to set the number of cols on a textarea */
	public function test_set_cols(): void {
		$textarea = new Textarea( 'test' );
		$textarea->cols( 40 );
		$this->assertEquals( 40, $textarea->get_cols() );
	}

	/** @testdox It should be possible to check if cols has been set */
	public function test_has_cols(): void {
		$textarea = new Textarea( 'test' );
		$this->assertFalse( $textarea->has_cols() );
		$textarea->cols( 40 );
		$this->assertTrue( $textarea->has_cols() );
	}

	/** @testdox The rows method should return the field instance for chaining */
	public function test_rows_returns_self(): void {
		$textarea = new Textarea( 'test' );
		$this->assertSame( $textarea, $textarea->rows( 10 ) );
	}

	/** @testdox The cols method should return the field instance for chaining */
	public function test_cols_returns_self(): void {
		$textarea = new Textarea( 'test' );
		$this->assertSame( $textarea, $textarea->cols( 40 ) );
	}

	/** @testdox By default rows should be null */
	public function test_default_rows_null(): void {
		$textarea = new Textarea( 'test' );
		$this->assertNull( $textarea->get_rows() );
	}

	/** @testdox By default cols should be null */
	public function test_default_cols_null(): void {
		$textarea = new Textarea( 'test' );
		$this->assertNull( $textarea->get_cols() );
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
			'autocomplete' => array( 'autocomplete' ),
			'maxlength'    => array( 'maxlength' ),
			'minlength'    => array( 'minlength' ),
			'placeholder'  => array( 'placeholder' ),
			'readonly'     => array( 'readonly' ),
			'required'     => array( 'required' ),
			'spellcheck'   => array( 'spellcheck' ),
			'disabled'     => array( 'disabled' ),
			'label'        => array( 'label' ),
		);
	}

	/**
	 * @testdox This field has all attributes as defined by the shared traits
	 * @dataProvider attribute_methods
	 */
	public function test_has_attributes( string $method ): void {
		$textarea = new Textarea( 'test' );
		$this->assertTrue( method_exists( $textarea, $method ) );
	}

	/** @testdox A Textarea should show a wrapper by default */
	public function test_default_wrapper(): void {
		$textarea = new Textarea( 'test' );
		$this->assertTrue( $textarea->has_wrapper() );
	}

	/** @testdox It should be possible to create a Textarea using the static make method */
	public function test_static_make(): void {
		$textarea = Textarea::make( 'test' );
		$this->assertInstanceOf( Textarea::class, $textarea );
		$this->assertEquals( 'test', $textarea->get_name() );
	}
}
