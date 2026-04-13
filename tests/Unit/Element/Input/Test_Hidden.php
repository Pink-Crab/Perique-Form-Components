<?php

declare(strict_types=1);

/**
 * Unit tests for the Hidden Input
 * Extends Abstract_Input
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Input;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Field\Input\Hidden;

/**
 * @group unit
 * @group element
 * @group input
 */
class Test_Hidden extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;
	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Description_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Hidden::class;
	}

	/** @testdox A Hidden input should return an input type of "hidden" */
	public function test_type(): void {
		$hidden = new Hidden( 'test' );
		$this->assertEquals( 'hidden', $hidden->get_input_type() );
	}

	/** @testdox A Hidden input should return a type of hidden_input */
	public function test_element_type(): void {
		$hidden = new Hidden( 'test' );
		$this->assertEquals( 'hidden_input', $hidden->get_type() );
	}

	/** @testdox By default the hidden field should have no sanitizer. */
	public function test_default_sanitizer(): void {
		$hidden = new Hidden( 'test' );
		$this->assertNull( $hidden->get_sanitizer() );
	}

	/** @testdox A Hidden field should not show a wrapper by default */
	public function test_default_no_wrapper(): void {
		$hidden = new Hidden( 'test' );
		$this->assertFalse( $hidden->has_wrapper() );
	}

	/** @testdox It should be possible to explicitly enable the wrapper on a hidden field */
	public function test_enable_wrapper(): void {
		$hidden = new Hidden( 'test' );
		$hidden->show_wrapper( true );
		$this->assertTrue( $hidden->has_wrapper() );
	}
}
