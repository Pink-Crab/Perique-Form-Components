<?php

declare(strict_types=1);

/**
 * Unit tests for the Sanitize utility class
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Util;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Util\Sanitize;

/**
 * @group unit
 * @group util
 */
class Test_Sanitize extends WP_UnitTestCase {

	####################################################################
	######                       TEXT                             ######
	####################################################################

	/** @testdox It should sanitize a text string */
	public function test_text_string(): void {
		$this->assertEquals( 'hello world', Sanitize::text( 'hello world' ) );
	}

	/** @testdox It should strip HTML tags from text */
	public function test_text_strips_html(): void {
		$this->assertStringNotContainsString( '<script>', Sanitize::text( '<script>alert("xss")</script>' ) );
	}

	/** @testdox It should handle numeric values as text */
	public function test_text_numeric(): void {
		$this->assertEquals( '42', Sanitize::text( 42 ) );
		$this->assertEquals( '3.14', Sanitize::text( 3.14 ) );
	}

	####################################################################
	######                     TEXTAREA                          ######
	####################################################################

	/** @testdox It should sanitize textarea content */
	public function test_textarea(): void {
		$this->assertEquals( "line 1\nline 2", Sanitize::textarea( "line 1\nline 2" ) );
	}

	/** @testdox It should strip HTML from textarea */
	public function test_textarea_strips_html(): void {
		$this->assertStringNotContainsString( '<script>', Sanitize::textarea( '<script>bad</script>text' ) );
	}

	####################################################################
	######                        URL                            ######
	####################################################################

	/** @testdox It should sanitize a URL */
	public function test_url(): void {
		$this->assertEquals( 'http://example.com', Sanitize::url( 'http://example.com' ) );
	}

	/** @testdox It should handle non-string URL input */
	public function test_url_numeric(): void {
		$result = Sanitize::url( 123 );
		$this->assertIsString( $result );
	}

	####################################################################
	######                     HEX COLOR                         ######
	####################################################################

	/** @testdox It should sanitize a valid hex color */
	public function test_hex_color_valid(): void {
		$this->assertEquals( '#ff0000', Sanitize::hex_color( '#ff0000' ) );
	}

	/** @testdox It should sanitize a 3-character hex color */
	public function test_hex_color_short(): void {
		$this->assertEquals( '#f00', Sanitize::hex_color( '#f00' ) );
	}

	####################################################################
	######                       EMAIL                           ######
	####################################################################

	/** @testdox It should sanitize a valid email */
	public function test_email_valid(): void {
		$this->assertEquals( 'test@example.com', Sanitize::email( 'test@example.com' ) );
	}

	/** @testdox It should handle non-string email input */
	public function test_email_numeric(): void {
		$result = Sanitize::email( 123 );
		$this->assertIsString( $result );
	}

	####################################################################
	######                      NUMBER                           ######
	####################################################################

	/** @testdox It should return 0 for null */
	public function test_number_null(): void {
		$this->assertSame( 0, Sanitize::number( null ) );
	}

	/** @testdox It should return an integer for whole numbers */
	public function test_number_integer(): void {
		$this->assertSame( 42, Sanitize::number( '42' ) );
		$this->assertSame( 42, Sanitize::number( 42 ) );
	}

	/** @testdox It should return a float for decimal numbers */
	public function test_number_float(): void {
		$this->assertSame( 3.14, Sanitize::number( '3.14' ) );
		$this->assertSame( 3.14, Sanitize::number( 3.14 ) );
	}

	/** @testdox It should handle string numbers */
	public function test_number_string(): void {
		$this->assertSame( 100, Sanitize::number( '100' ) );
	}

	####################################################################
	######                       NOOP                            ######
	####################################################################

	/** @testdox Noop should pass through the value unchanged */
	public function test_noop_string(): void {
		$this->assertSame( 'hello', Sanitize::noop( 'hello' ) );
	}

	/** @testdox Noop should pass through numeric values unchanged */
	public function test_noop_numeric(): void {
		$this->assertSame( 42, Sanitize::noop( 42 ) );
		$this->assertSame( 3.14, Sanitize::noop( 3.14 ) );
	}

	####################################################################
	######                    CONSTANTS                          ######
	####################################################################

	/** @testdox All Sanitize constants should be callable */
	public function test_constants_are_callable(): void {
		$this->assertTrue( is_callable( Sanitize::TEXT ) );
		$this->assertTrue( is_callable( Sanitize::TEXTAREA ) );
		$this->assertTrue( is_callable( Sanitize::URL ) );
		$this->assertTrue( is_callable( Sanitize::HEX_COLOR ) );
		$this->assertTrue( is_callable( Sanitize::EMAIL ) );
		$this->assertTrue( is_callable( Sanitize::NUMBER ) );
		$this->assertTrue( is_callable( Sanitize::NOOP ) );
	}
}
