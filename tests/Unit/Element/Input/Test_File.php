<?php

declare(strict_types=1);

/**
 * Unit tests for the File Input
 * Extends Abstract_Input
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Input;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Field\Input\File;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Single_Value, Multiple, Disabled, Required};

/**
 * @group unit
 * @group element
 * @group input
 * @group file
 */
* /
class Test_File extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/**
 * @inheritDoc
*/
	public function get_class_under_test(): string {
		return File::class;
	}

	/**
 * @testdox A File input should return an input type of "file"
*/
	public function test_type(): void {
		$file = new File( 'test' );
		$this->assertEquals( 'file', $file->get_input_type() );
	}

	/**
 * @testdox A File input should return a type of file_input
*/
	public function test_element_type(): void {
		$file = new File( 'test' );
		$this->assertEquals( 'file_input', $file->get_type() );
	}

	/**
 * @testdox By default the File field should sanitize values as integer using intval.
*/
	public function test_default_sanitizer(): void {
		$file = new File( 'test' );
		$this->assertNull( $file->get_sanitizer() );
	}

	####################################################################
	######                         TRAITS                         ######
	####################################################################

	/**
 * @testdox A File field should allow a single value to be set
*/
	public function test_uses_single_value(): void {
		$file = new File( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $file ) );
	}

	/**
 * @testdox A File input field should implement the multiple trait.
*/
	public function test_uses_multiple(): void {
		$file = new File( 'test' );
		$this->assertTrue( usesTrait( Multiple::class )( $file ) );
	}

	/**
 * @testdox A File input field should implement the disabled trait.
*/
	public function test_uses_disabled(): void {
		$file = new File( 'test' );
		$this->assertTrue( usesTrait( Disabled::class )( $file ) );
	}

	/**
 * @testdox A File input field should implement the required trait.
*/
	public function test_uses_required(): void {
		$file = new File( 'test' );
		$this->assertTrue( usesTrait( Required::class )( $file ) );
	}

	####################################################################
	######                      FILE SPECIFIC                     ######
	####################################################################

	/**
	 * @testdox It should be possible to set the capture attribute as either a "user", "environment" or NULL
	 * @dataProvider provide_valid_capture_types
	 */
	public function test_capture_attribute( ?string $capture ): void {
		$file = new File( 'test' );
		$file->capture( $capture );
		$this->assertTrue( $file->has_attribute( 'capture' ) );
	}

	/**
 * @return array<string|null[]>
*/
	public function provide_valid_capture_types(): array {
		return array(
			'user'        => array( 'user' ),
			'environment' => array( 'environment' ),
			'null'        => array( null ),
		);
	}

	/**
	 * @testdox It should not be possible to set the capture attribute to anything other than "user", "environment" or NULL
	 * @dataProvider provide_invalid_capture_types
	 */
	public function test_invalid_capture_attribute( string $capture ): void {
		$file = new File( 'test' );
		$this->expectException( \InvalidArgumentException::class );
		$file->capture( $capture );
	}

	/**
 * @return array<string[]>
*/
	public function provide_invalid_capture_types(): array {
		return array(
			'an invalid string' => array( 'invalid' ),
			'an empty string'   => array( '' ),
		);
	}

	/**
 * @testdox It should be possible to unset the capture if its set.
*/
	public function test_unset_capture(): void {
		$file = new File( 'test' );

		// Attempting to unset, when not set, should just return.
		$file->remove_capture();
		$this->assertFalse( $file->has_attribute( 'capture' ) );

		// Set and unset.
		$file->capture( 'user' );
		$this->assertTrue( $file->has_attribute( 'capture' ) );

		$file->remove_capture();
		$this->assertFalse( $file->has_attribute( 'capture' ) );
	}

	/**
 * @test It should be possible to check if a File field has a capture value defined.
*/
	public function test_has_capture(): void {
		$file = new File( 'test' );
		$this->assertFalse( $file->has_capture() );

		$file->capture( 'user' );
		$this->assertTrue( $file->has_capture() );
	}

	/**
 * @testdox Attempting to get the capture value for a File field should return null if not set.
*/
	public function test_get_capture_unset(): void {
		$file = new File( 'test' );
		$this->assertNull( $file->get_capture() );
	}

	/**
 * @testdox It should be possible to get the capture value for a File field.
*/
	public function test_get_capture(): void {
		$file = new File( 'test' );
		$file->capture( 'user' );
		$this->assertEquals( 'user', $file->get_capture() );
	}

	/**
 * @testdox The value defined for capture should be ran through an esc function
*/
	public function test_capture_escaped(): void {
		$file = new File( 'test' );
		$file->attribute( 'capture', '<p>user</p>' );
		$this->assertEquals( '&lt;p&gt;user&lt;/p&gt;', $file->get_capture() );
	}

	/**
 * @test It should be possible to set the accept attribute of a File Input
*/
	public function test_accept_attribute(): void {
		$file = new File( 'test' );
		$file->accept( 'image/*' );
		$this->assertTrue( $file->has_attribute( 'accept' ) );
	}

	/**
 * @testdox The value use for the accept attribute of a File Input should be escaped.
*/
	public function test_accept_attribute_escaped(): void {
		$file = new File( 'test' );
		$file->accept( '<p>image/*</p>' );
		$this->assertEquals( 'image/*', $file->get_attribute( 'accept' ) );
	}

	/**
 * @test It should be possible to unset the accept attribute of a File Input
*/
	public function test_unset_accept_attribute(): void {
		$file = new File( 'test' );

		// Attempting to unset, when not set, should just return.
		$file->remove_accept();

		$file->accept( 'image/*' );
		$this->assertTrue( $file->has_attribute( 'accept' ) );

		$file->remove_accept();
		$this->assertFalse( $file->has_attribute( 'accept' ) );
	}

	/**
 * @test It should be possible to check if a File Input has an accept attribute set
*/
	public function test_has_accept_attribute(): void {
		$file = new File( 'test' );
		$this->assertFalse( $file->has_accept() );

		$file->accept( 'image/*' );
		$this->assertTrue( $file->has_accept() );
	}

	/**
 * @testdox It should be possible to get the defined value for a File Field
*/
	public function test_get_accept_attribute(): void {
		$file = new File( 'test' );
		$file->accept( 'image/*' );
		$this->assertEquals( 'image/*', $file->get_accept() );
	}

	/**
 * @testdox Attempting to get the accept value for a File field should return null if not set.
*/
	public function test_get_accept_unset(): void {
		$file = new File( 'test' );
		$this->assertNull( $file->get_accept() );
	}

	/**
 * @testdox The value returned when getting the accept value of Accept attribute of a File Field
*/
	public function test_get_accept_escaped(): void {
		$file = new File( 'test' );
		$file->attribute( 'accept', '<p>image/*</p>' );
		$this->assertEquals( '&lt;p&gt;image/*&lt;/p&gt;', $file->get_accept() );
	}
}
