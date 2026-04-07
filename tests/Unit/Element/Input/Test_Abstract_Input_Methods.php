<?php

declare(strict_types=1);

/**
 * Unit tests for Abstract_Input methods not covered by field-specific tests.
 * Tests tabindex, set_existing, and get_type/get_input_type.
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Input;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Number;

/**
 * @group unit
 * @group element
 * @group input
 * @group abstract_input
 */
class Test_Abstract_Input_Methods extends WP_UnitTestCase {

	####################################################################
	######                     TABINDEX                          ######
	####################################################################

	/** @testdox It should be possible to set a tabindex on an input */
	public function test_tabindex_set(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_tabindex() );
		$text->tabindex( '5' );
		$this->assertTrue( $text->has_tabindex() );
		$this->assertEquals( '5', $text->get_tabindex() );
	}

	/** @testdox A field without tabindex should return null */
	public function test_tabindex_not_set(): void {
		$text = new Text( 'test' );
		$this->assertNull( $text->get_tabindex() );
	}

	/** @testdox It should be possible to clear the tabindex */
	public function test_tabindex_clear(): void {
		$text = new Text( 'test' );
		$text->tabindex( '3' );
		$this->assertTrue( $text->has_tabindex() );
		$text->clear_tabindex();
		$this->assertFalse( $text->has_tabindex() );
		$this->assertNull( $text->get_tabindex() );
	}

	/** @testdox Clearing tabindex when not set should not throw */
	public function test_tabindex_clear_when_not_set(): void {
		$text = new Text( 'test' );
		$text->clear_tabindex();
		$this->assertFalse( $text->has_tabindex() );
	}

	####################################################################
	######                    SET_EXISTING                        ######
	####################################################################

	/** @testdox It should be possible to set an existing value on a field with a sanitizer */
	public function test_set_existing_with_sanitizer(): void {
		$text = new Text( 'test' );
		$text->set_existing( 'hello world' );
		$this->assertEquals( 'hello world', $text->get_value() );
	}

	/** @testdox Set existing should sanitize the value using the field's sanitizer */
	public function test_set_existing_sanitizes(): void {
		$text = new Text( 'test' );
		$text->set_existing( '<script>alert("xss")</script>' );
		// The sanitizer should strip the script tag
		$this->assertStringNotContainsString( '<script>', $text->get_value() );
	}

	/** @testdox Set existing should work on a field without a sanitizer */
	public function test_set_existing_without_sanitizer(): void {
		$number = new Number( 'test' );
		$number->set_existing( '42' );
		// Number sanitizer converts to int, then value() stores it
		$this->assertNotNull( $number->get_value() );
	}

	####################################################################
	######                   GET_TYPE                             ######
	####################################################################

	/** @testdox get_type should return the input type suffixed with _input */
	public function test_get_type_format(): void {
		$text   = new Text( 'test' );
		$number = new Number( 'test' );
		$this->assertEquals( 'text_input', $text->get_type() );
		$this->assertEquals( 'number_input', $number->get_type() );
	}

	/** @testdox get_input_type should return the raw input type */
	public function test_get_input_type(): void {
		$text   = new Text( 'test' );
		$number = new Number( 'test' );
		$this->assertEquals( 'text', $text->get_input_type() );
		$this->assertEquals( 'number', $number->get_input_type() );
	}

	####################################################################
	######              SANITIZE WITHOUT SANITIZER                ######
	####################################################################

	/** @testdox Calling sanitize when no sanitizer is set should return the value as-is */
	public function test_sanitize_without_sanitizer(): void {
		$text = new Text( 'test' );
		$text->sanitizer( null );
		$this->assertEquals( 'raw_value', $text->sanitize( 'raw_value' ) );
	}

	####################################################################
	######              FORM_STYLE GUARD                          ######
	####################################################################

	/** @testdox Getting style when not set should throw RuntimeException */
	public function test_get_style_throws_when_null(): void {
		$text = new Text( 'test' );
		// Clear the style via reflection
		$reflection = new \ReflectionClass( $text );
		$prop       = $reflection->getProperty( 'form_style' );
		$prop->setAccessible( true );
		$prop->setValue( $text, null );

		$this->expectException( \RuntimeException::class );
		$text->get_style();
	}

	/** @testdox has_explicit_style should return false by default and true after calling style() */
	public function test_has_explicit_style(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_explicit_style() );
		$text->style( new \PinkCrab\Form_Components\Style\Default_Style() );
		$this->assertTrue( $text->has_explicit_style() );
	}
}
