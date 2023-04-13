<?php

declare(strict_types=1);

/**
 * Trait for all shared methods between elements.
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use PinkCrab\Form_Components\Style\Style;
use PinkCrab\Form_Components\Element\Field;

trait Shared_Field_Cases {

	/**
	 * Abstract method for getting the class under test.
	 *
	 * @return class-string<Field>
	 */
	abstract public function get_class_under_test(): string;

	####################################################################
	######                     FIELD SPECIFIC                     ######
	####################################################################

	/** @testdox [Shared::Field] It should be possible to get the Fields defined name attribute from a newly created instance (only constructor called) */
	public function test_get_name(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$this->assertEquals( 'test', $field->get_name() );
	}

	/** @testdox [Shared::Field] The name a field is created with should have its name santized as a string */
	public function test_name_sanitized(): void {
		$class = $this->get_class_under_test();
		$field = new $class( '<p>test</p>' );
		$this->assertEquals( 'test', $field->get_name() );
	}

	/** @testdox [Shared::Field] It should be possible to get the type of the field ass soon as the field is created. */
	public function test_get_type(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$this->assertIsString( $field->get_type() );
	}

	####################################################################
	######                    Form_Style Trait                    ######
	####################################################################


	/** @testdox It should be possible to create an instance of the field and always have a style definition, even if not supplied. */
	public function test_get_style(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$this->assertInstanceOf( Style::class, $field->get_style() );
	}


	####################################################################
	######                    Sanitizer Trait                     ######
	####################################################################

	/** @testdox [Shared::Sanitizer] It should be possible to set a sanitizer on the field as either a callable or null to clear.. */
	public function test_set_sanitizer(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$field->sanitizer( 'intval' );
		$this->assertEquals( 'intval', $field->get_sanitizer() );
		$this->assertTrue( $field->has_sanitizer() );

		$field->sanitizer( null );
		$this->assertNull( $field->get_sanitizer() );
		$this->assertFalse( $field->has_sanitizer() );
	}

	/** @testdox [Shared::Sanitizer] It should be possible to get the defined sanitizer on the field.*/
	public function test_get_sanitizer(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$field->sanitizer( 'intval' );
		$this->assertEquals( 'intval', $field->get_sanitizer() );
	}

	/** @testdox [Shared::Sanitizer] It should be possible to check if a sanitizer has been defined on the field.*/
	public function test_has_sanitizer(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$field->sanitizer( 'intval' );
		$this->assertTrue( $field->has_sanitizer() );

		$field->sanitizer( null );
		$this->assertFalse( $field->has_sanitizer() );
	}

	/** @testdox [Shared::Sanitizer] It should be possible to call the sanitizer with a value */
	public function test_call_sanitizer(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$field->sanitizer( 'intval' );
		$this->assertEquals( 1, $field->sanitize( '1' ) );
	}

	####################################################################
	######                   Element_Wrap Trait                   ######
	####################################################################

	/** @testdox [Shared::Element_Wrap] It should be possible to set a string which is rendered before the field*/
	public function test_set_before(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$field->before( 'before' );
		$this->assertEquals( 'before', $field->get_before() );
	}

	/** @testdox [Shared::Element_Wrap] It should be possible to set a string which is rendered after the field*/
	public function test_set_after(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$field->after( 'after' );
		$this->assertEquals( 'after', $field->get_after() );
	}


	/** @testdox [Shared::Element_Wrap] It should be possible to get string or null (if not set) which is rendered before the field*/
	public function test_get_before(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		// Null by default.
		$this->assertNull( $field->get_before() );

		$field->before( 'before' );
		$this->assertEquals( 'before', $field->get_before() );
	}

	/** @testdox [Shared::Element_Wrap] It should be possible to get string or null (if not set) which is rendered after the field*/
	public function test_get_after(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		// Null by default.
		$this->assertNull( $field->get_after() );

		$field->after( 'after' );
		$this->assertEquals( 'after', $field->get_after() );
	}

	/** @testdox [Shared::Element_Wrap] It should be possible check if a value which is rendered after the field is set or not (not by default) */
	public function test_has_before(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		// Null by default.
		$this->assertFalse( $field->has_before() );

		$field->before( 'before' );
		$this->assertTrue( $field->has_before() );
	}

	/** @testdox [Shared::Element_Wrap] It should be possible check if a value which is rendered after the field is set or not (not by default) */
	public function test_has_after(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		// Null by default.
		$this->assertFalse( $field->has_after() );

		$field->after( 'after' );
		$this->assertTrue( $field->has_after() );
	}


	####################################################################
	######                    Attributes Trait                    ######
	####################################################################

	/** @testdox [Shared::Attributes] It should be possible to set an attribute on the field. */
	public function test_set_attribute(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$field->attribute( 'test', 'value' );
		$this->assertEquals( 'value', $field->get_attribute( 'test' ) );
	}

	/** @testdox [Shared::Attributes] It should be possible to get an attribute on the field. */
	public function test_get_attribute(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$field->attribute( 'test', 'value' );
		$this->assertEquals( 'value', $field->get_attribute( 'test' ) );
	}

	/** @testdox [Shared::Attributes] It should be possible to check if an attribute is set on the field. */
	public function test_has_attribute(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$this->assertFalse( $field->has_attribute( 'test' ) );
		$field->attribute( 'test', 'value' );
		$this->assertTrue( $field->has_attribute( 'test' ) );
	}

	/** @testdox [Shared::Attributes] It should be possible to get all attributes on the field. */
	public function test_get_attributes(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$field->attribute( 'test', 'value' );
		$field->attribute( 'test2', 'value2' );

				$this->assertArrayHasKey( 'test', $field->get_attributes() );
		$this->assertEquals( 'value', $field->get_attribute( 'test' ) );
		$this->assertArrayHasKey( 'test2', $field->get_attributes() );
		$this->assertEquals( 'value2', $field->get_attribute( 'test2' ) );
	}

	/** @testdox [Shared::Attributes] It should be possible to set multiple attributes on the field. */
	public function test_set_attributes(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );
		$field->attributes(
			array(
				'test'  => 'value',
				'test2' => 'value2',
			)
		);

		$this->assertArrayHasKey( 'test', $field->get_attributes() );
		$this->assertEquals( 'value', $field->get_attribute( 'test' ) );
		$this->assertArrayHasKey( 'test2', $field->get_attributes() );
		$this->assertEquals( 'value2', $field->get_attribute( 'test2' ) );
	}

	/** @testdox [Shared::Attributes] It should be possible to check if any attributes defined on the field. */
	public function test_field_has_attributes(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->attribute( 'test', 'value' );
		$this->assertTrue( $field->has_attributes() );

		// Remove the fields attributes using reflection.
		$reflection = new \ReflectionClass( $field );
		$property   = $reflection->getProperty( 'attributes' );
		$property->setAccessible( true );
		$property->setValue( $field, array() );

		$this->assertFalse( $field->has_attributes() );
	}

	/** @testdox [Shared::Attributes] It should be possible to remove an attribute from the field. */
	public function test_remove_attribute(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->attribute( 'test', 'value' );
		$this->assertTrue( $field->has_attribute( 'test' ) );

		$field->remove_attribute( 'test' );
		$this->assertFalse( $field->has_attribute( 'test' ) );
	}

	/** @testdox [Shared::Attributes] It should be possible to append (with whitespace) a class to any value existing value with the class key */
	public function test_add_class(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->add_class( 'test' );
		$this->assertStringContainsString( 'test', $field->get_attribute( 'class' ) );

		$field->add_class( 'test2' );
		$this->assertStringContainsString( 'test2', $field->get_attribute( 'class' ) );
	}

	/** @testdox [Shared::Attributes] When adding a class to the field, no duplicates should be allowed */
	public function test_add_class_no_duplicates(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->add_class( 'test' );
		$this->assertEquals( 1, substr_count( $field->get_attribute( 'class' ), 'test' ) );

		$field->add_class( 'test' );
		$this->assertEquals( 1, substr_count( $field->get_attribute( 'class' ), 'test' ) );
	}

	/** @testdox [Shared::Attributes] Attempting to remove a class, when no classes are defined, should not throw any errors. */
	public function test_remove_class_no_classes(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		// use reflection to remove the 'class' index from the $field->attributes array.
		$reflection = new \ReflectionClass( $field );
		$property   = $reflection->getProperty( 'attributes' );
		$property->setAccessible( true );
		$attributes = $property->getValue( $field );
		unset( $attributes['class'] );
		$property->setValue( $field, $attributes );

		$this->assertSame( $field, $field->remove_class( 'test' ) );
	}

	/** @testdox [Shared::Attributes] It should be possible to remove a class from the field, if has already been supplied */
	public function test_remove_class(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->add_class( 'test' );
		$this->assertStringContainsString( 'test', $field->get_attribute( 'class' ) );

		$field->remove_class( 'test' );
		$this->assertStringNotContainsString( 'test', $field->get_attribute( 'class' ) );
	}

	/** @testdox [Shared::Attributes] It should be possible to set the ID of the field */
	public function test_set_id(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->id( 'test' );
		$this->assertEquals( 'test', $field->get_attribute( 'id' ) );
	}

	/** @testdox [Shared::Attributes] It should be possible to set a data- attribute*/
	public function test_set_data_attribute(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->data( 'test', 'value' );
		$this->assertEquals( 'value', $field->get_attribute( 'data-test' ) );
	}

	####################################################################
	######                Wrapper Attributes Trait                ######
	####################################################################

	/** @testdox [Shared::WrapperAttributes] It should be possible to set and get a wrapper attribute */
	public function test_set_wrapper_attribute(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->wrapper_attribute( 'test', 'value' );
		$this->assertEquals( 'value', $field->get_wrapper_attribute( 'test' ) );
	}

	/** @testdox [Shared::WrapperAttributes] It should be possible to set and get multiple wrapper attributes */
	public function test_set_wrapper_attributes(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->wrapper_attributes(
			array(
				'test'  => 'value',
				'test2' => 'value2',
			)
		);

		$this->assertArrayHasKey( 'test', $field->get_wrapper_attributes() );
		$this->assertEquals( 'value', $field->get_wrapper_attribute( 'test' ) );
		$this->assertArrayHasKey( 'test2', $field->get_wrapper_attributes() );
		$this->assertEquals( 'value2', $field->get_wrapper_attribute( 'test2' ) );
	}

	/** @testdox [Shared::WrapperAttributes] It should be possible to check if a wrapper attribute exists */
	public function test_has_wrapper_attribute(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->wrapper_attribute( 'test', 'value' );
		$this->assertTrue( $field->has_wrapper_attribute( 'test' ) );
	}

	/** @testdox [Shared::WrapperAttributes] It should be possible to check if a wrapper has any defined attributes */
	public function test_has_wrapper_attributes(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->wrapper_attribute( 'test', 'value' );
		$this->assertTrue( $field->has_wrapper_attributes() );

		// Remove all $wrapper_attributes and check again.
		$reflection = new \ReflectionClass( $field );
		$property   = $reflection->getProperty( 'wrapper_attributes' );
		$property->setAccessible( true );
		$property->setValue( $field, array() );

		$this->assertFalse( $field->has_wrapper_attributes() );
	}

	/** @testdox [Shared::WrapperAttributes] It should be possible to remove a wrapper attribute */
	public function test_remove_wrapper_attribute(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->wrapper_attribute( 'test', 'value' );
		$this->assertTrue( $field->has_wrapper_attribute( 'test' ) );

		$field->remove_wrapper_attribute( 'test' );
		$this->assertFalse( $field->has_wrapper_attribute( 'test' ) );
	}

	/** @testdox [Shared::WrapperAttributes] It should be possible to add a class to the wrapper */
	public function test_add_wrapper_class(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->add_wrapper_class( 'test' );
		$this->assertEquals( 1, substr_count( $field->get_wrapper_attribute( 'class' ), 'test' ) );
	}

	/** @testdox [Shared::WrapperAttributes] It should be possible to remove a class from the wrapper */
	public function test_remove_wrapper_class(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->add_wrapper_class( 'test' );
		$this->assertEquals( 1, substr_count( $field->get_wrapper_attribute( 'class' ), 'test' ) );

		$field->remove_wrapper_class( 'test' );
		$this->assertEquals( 0, substr_count( $field->get_wrapper_attribute( 'class' ), 'test' ) );
	}

	/** @testdox [Shared::WrapperAttributes] When adding a wrapper class, no duplicates should be allowed. */
	public function test_add_wrapper_class_no_duplicates(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->add_wrapper_class( 'test' );
		$field->add_wrapper_class( 'test' );
		$this->assertEquals( 1, substr_count( $field->get_wrapper_attribute( 'class' ), 'test' ) );
	}

	/** @testdox [Shared::WrapperAttributes] It should be possible remove a class by its values from wrapper attributes. */
	public function test_remove_wrapper_class_by_name(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->add_wrapper_class( 'test' );
		$field->add_wrapper_class( 'test2' );
		$field->add_wrapper_class( 'test3' );
		$this->assertEquals( 3, substr_count( $field->get_wrapper_attribute( 'class' ), 'test' ) );

		$field->remove_wrapper_class( 'test2' );
		$this->assertEquals( 2, substr_count( $field->get_wrapper_attribute( 'class' ), 'test' ) );
	}

	/** @testdox [Shared::WrapperAttributes] It should be possible to remove a class, even if no classes has been deifned without throwing an error. */
	public function test_remove_wrapper_class_no_classes(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		// use reflection to remove the 'class' index from the $field->wrapper_attributes array.
		$reflection = new \ReflectionClass( $field );
		$property   = $reflection->getProperty( 'wrapper_attributes' );
		$property->setAccessible( true );
		$wrapper_attributes = $property->getValue( $field );
		unset( $wrapper_attributes['class'] );
		$property->setValue( $field, $wrapper_attributes );

		$field->remove_wrapper_class( 'test2' );
		$this->assertSame( $field, $field->remove_class( 'test' ) );
	}

	/** @testdox [Shared::WrapperAttributes] It should be possible to set the wrapper id of this field. */
	public function test_set_wrapper_id(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->wrapper_id( 'test' );
		$this->assertEquals( 'test', $field->get_wrapper_attribute( 'id' ) );
	}

	/** @testdox [Shared::WrapperAttributes] It should be possible to set a data attribute of the field wrapper.. */
	public function test_set_wrapper_data_attribute(): void {
		$class = $this->get_class_under_test();
		$field = new $class( 'test' );

		$field->wrapper_data( 'test', 'value' );
		$this->assertEquals( 'value', $field->get_wrapper_attribute( 'data-test' ) );
	}


}
