<?php

declare(strict_types=1);

/**
 * Unit tests for the Submit Input
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
use PinkCrab\Form_Components\Element\Field\Input\Submit;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Disabled, Read_Only, Required, Single_Value};

/**
 * @group unit
 * @group element
 * @group input
 */
class Test_Submit extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Submit::class;
	}
	
	/** @testdox A Submit input should return an input type of "email" */
	public function test_type(): void {
		$submit = new Submit( 'test' );
		$this->assertEquals( 'submit', $submit->get_input_type() );
	}

	/** @testdox A Submit input should return a type of email_input */
	public function test_element_type(): void {
		$submit = new Submit( 'test' );
		$this->assertEquals( 'submit_input', $submit->get_type() );
	}

	/** @testdox By default the Submit field should sanitize values as integer using intval. */
	public function test_default_sanitizer(): void {
		$submit = new Submit( 'test' );
		$this->assertEquals( Sanitize::TEXT, $submit->get_sanitizer() );
	}

	####################################################################
	######                         TRAITS                         ######
	####################################################################

	/** @testdox A Submit field should allow a single value to be set */
	public function test_uses_single_value(): void {
		$submit = new Submit( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $submit ) );
	}

	/** @testdox A Submit field should allow a disabled value to be set */
	public function test_uses_disabled(): void {
		$submit = new Submit( 'test' );
		$this->assertTrue( usesTrait( Disabled::class )( $submit ) );
	}

	####################################################################
	######                     FIELD SPECIFIC                     ######
	####################################################################

	/** @testdox It should be possible to use an alias called text() to set the submit value. */
	public function test_text_alias(): void {
		$submit = new Submit( 'test' );
		$submit->text( 'test' );
		$this->assertEquals( 'test', $submit->get_value() );
	}

	/** @testdox It should be possible to set the formaction of the submit input */
	public function test_formaction(): void {
		$submit = new Submit( 'test' );
		$submit->formaction( 'test' );
		$this->assertTrue( $submit->has_attribute( 'formaction' ) );
		$this->assertEquals( 'test', $submit->get_attribute( 'formaction' ) );
	}

	/** @testdox It should be possible to check if formaction has been set the submit input */
	public function test_has_formaction(): void {
		$submit = new Submit( 'test' );
		$this->assertFalse( $submit->has_formaction() );
		$submit->attribute( 'formaction', 'test' );
		$this->assertTrue( $submit->has_formaction() );
	}

	/** @testdox It should be possible to get the formaction of the submit input */
	public function test_get_formaction(): void {
		$submit = new Submit( 'test' );
		$this->assertNull( $submit->get_formaction() );
		$submit->attribute( 'formaction', 'test' );
		$this->assertEquals( 'test', $submit->get_formaction() );
	}

	/** @testdox It should be possible to clear the formaction of the submit input */
	public function test_clear_formaction(): void {
		$submit = new Submit( 'test' );
		$submit->attribute( 'formaction', 'test' );
		$this->assertTrue( $submit->has_formaction() );
		$submit->clear_formaction();
		$this->assertFalse( $submit->has_formaction() );
	}

	/** @testdox It should be possible to set the formenctype of the submit input */
	public function test_formenctype(): void {
		$submit = new Submit( 'test' );
		$submit->formenctype( 'test' );
		$this->assertTrue( $submit->has_attribute( 'formenctype' ) );
		$this->assertEquals( 'test', $submit->get_attribute( 'formenctype' ) );
	}

	/** @testdox It should be possible to check if formenctype has been set the submit input */
	public function test_has_formenctype(): void {
		$submit = new Submit( 'test' );
		$this->assertFalse( $submit->has_formenctype() );
		$submit->attribute( 'formenctype', 'test' );
		$this->assertTrue( $submit->has_formenctype() );
	}

	/** @testdox It should be possible to get the formenctype of the submit input */
	public function test_get_formenctype(): void {
		$submit = new Submit( 'test' );
		$this->assertNull( $submit->get_formenctype() );
		$submit->attribute( 'formenctype', 'test' );
		$this->assertEquals( 'test', $submit->get_formenctype() );
	}

	/** @testdox It should be possible to clear the formenctype of the submit input */
	public function test_clear_formenctype(): void {
		$submit = new Submit( 'test' );
		$submit->attribute( 'formenctype', 'test' );
		$this->assertTrue( $submit->has_formenctype() );
		$submit->clear_formenctype();
		$this->assertFalse( $submit->has_formenctype() );
	}

	/** @testdox It should be possible to set the formmethod of the submit input */
	public function test_formmethod(): void {
		$submit = new Submit( 'test' );
		$submit->formmethod( 'test' );
		$this->assertTrue( $submit->has_attribute( 'formmethod' ) );
		$this->assertEquals( 'test', $submit->get_attribute( 'formmethod' ) );
	}

	/** @testdox It should be possible to check if formmethod has been set the submit input */
	public function test_has_formmethod(): void {
		$submit = new Submit( 'test' );
		$this->assertFalse( $submit->has_formmethod() );
		$submit->attribute( 'formmethod', 'test' );
		$this->assertTrue( $submit->has_formmethod() );
	}

	/** @testdox It should be possible to get the formmethod of the submit input */
	public function test_get_formmethod(): void {
		$submit = new Submit( 'test' );
		$this->assertNull( $submit->get_formmethod() );
		$submit->attribute( 'formmethod', 'test' );
		$this->assertEquals( 'test', $submit->get_formmethod() );
	}

	/** @testdox It should be possible to clear the formmethod of the submit input */
	public function test_clear_formmethod(): void {
		$submit = new Submit( 'test' );
		$submit->attribute( 'formmethod', 'test' );
		$this->assertTrue( $submit->has_formmethod() );
		$submit->clear_formmethod();
		$this->assertFalse( $submit->has_formmethod() );
	}

	/** @testdox It should be possible to set the formnovalidate of the submit input */
	public function test_formnovalidate(): void {
		$submit = new Submit( 'test' );
		$submit->formnovalidate( true );
		$this->assertTrue( $submit->has_attribute( 'formnovalidate' ) );
		$this->assertEquals( true, $submit->get_attribute( 'formnovalidate' ) );
		$submit->formnovalidate( false );
		$this->assertFalse($submit->get_attribute( 'formnovalidate' ) );
	}

	/** @testdox It should be possible to check if formnovalidate has been set the submit input */
	public function test_has_formnovalidate(): void {
		$submit = new Submit( 'test' );
		$this->assertFalse( $submit->has_formnovalidate() );
		$submit->attribute( 'formnovalidate', '1' );
		$this->assertTrue( $submit->has_formnovalidate() );
	}

	/** @testdox It should be possible to get the formnovalidate of the submit input */
	public function test_get_formnovalidate(): void {
		$submit = new Submit( 'test' );
		$this->assertNull( $submit->get_formnovalidate() );
		$submit->attribute( 'formnovalidate', '0' );
		$this->assertEquals( false, $submit->get_formnovalidate() );
	}

	/** @testdox It should be possible to clear the formnovalidate of the submit input */
	public function test_clear_formnovalidate(): void {
		$submit = new Submit( 'test' );
		$submit->attribute( 'formnovalidate', '1' );
		$this->assertTrue( $submit->has_formnovalidate() );
		$submit->clear_formnovalidate();
		$this->assertFalse( $submit->has_formnovalidate() );
	}

	/** @testdox It should be possible to set the formtarget of the submit input */
	public function test_formtarget(): void {
		$submit = new Submit( 'test' );
		$submit->formtarget( 'test' );
		$this->assertTrue( $submit->has_attribute( 'formtarget' ) );
		$this->assertEquals( 'test', $submit->get_attribute( 'formtarget' ) );
	}

	/** @testdox It should be possible to check if formtarget has been set the submit input */
	public function test_has_formtarget(): void {
		$submit = new Submit( 'test' );
		$this->assertFalse( $submit->has_formtarget() );
		$submit->attribute( 'formtarget', 'test' );
		$this->assertTrue( $submit->has_formtarget() );
	}

	/** @testdox It should be possible to get the formtarget of the submit input */
	public function test_get_formtarget(): void {
		$submit = new Submit( 'test' );
		$this->assertNull( $submit->get_formtarget() );
		$submit->attribute( 'formtarget', 'test' );
		$this->assertEquals( 'test', $submit->get_formtarget() );
	}

	/** @testdox It should be possible to clear the formtarget of the submit input */
	public function test_clear_formtarget(): void {
		$submit = new Submit( 'test' );
		$submit->attribute( 'formtarget', 'test' );
		$this->assertTrue( $submit->has_formtarget() );
		$submit->clear_formtarget();
		$this->assertFalse( $submit->has_formtarget() );
	}

	





}
