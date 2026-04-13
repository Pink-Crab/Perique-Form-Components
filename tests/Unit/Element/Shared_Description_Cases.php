<?php

declare(strict_types=1);

/**
 * Shared test cases for the Description trait.
 *
 * @see https://github.com/Pink-Crab/Perique-Form-Components/issues/18
 *
 * @since 2.2.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use PinkCrab\Form_Components\Element\Field;

trait Shared_Description_Cases {

	/**
	 * Abstract method for getting the class under test.
	 *
	 * @return class-string<Field>
	 */
	abstract public function get_class_under_test(): string;

	/** @testdox [Shared::Description] It should be possible to set and get a pre-description */
	public function test_pre_description_set_and_get(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->pre_description( 'Hint before input' );
		$this->assertTrue( $field->has_pre_description() );
		$this->assertSame( 'Hint before input', $field->get_pre_description() );
	}

	/** @testdox [Shared::Description] It should be possible to set and get a post-description */
	public function test_post_description_set_and_get(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->post_description( 'Help text after input' );
		$this->assertTrue( $field->has_post_description() );
		$this->assertSame( 'Help text after input', $field->get_post_description() );
	}

	/** @testdox [Shared::Description] A field with no pre-description should return null */
	public function test_no_pre_description_returns_null(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$this->assertFalse( $field->has_pre_description() );
		$this->assertNull( $field->get_pre_description() );
	}

	/** @testdox [Shared::Description] A field with no post-description should return null */
	public function test_no_post_description_returns_null(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$this->assertFalse( $field->has_post_description() );
		$this->assertNull( $field->get_post_description() );
	}

	/** @testdox [Shared::Description] It should be possible to set both pre and post descriptions */
	public function test_both_descriptions(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->pre_description( 'Before' );
		$field->post_description( 'After' );

		$this->assertSame( 'Before', $field->get_pre_description() );
		$this->assertSame( 'After', $field->get_post_description() );
	}

	/** @testdox [Shared::Description] Setting pre-description should return self for fluent chaining */
	public function test_pre_description_fluent(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$this->assertSame( $field, $field->pre_description( 'test' ) );
	}

	/** @testdox [Shared::Description] Setting post-description should return self for fluent chaining */
	public function test_post_description_fluent(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$this->assertSame( $field, $field->post_description( 'test' ) );
	}
}
