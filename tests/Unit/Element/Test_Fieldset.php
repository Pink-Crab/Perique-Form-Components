<?php

declare(strict_types=1);

/**
 * Unit tests for the Fieldset Element
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Fieldset;
use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Email;
use PinkCrab\Form_Components\Style\Default_Style;

/**
 * @group unit
 * @group element
 * @group fieldset
 */
class Test_Fieldset extends WP_UnitTestCase {

	/** @testdox Fieldset should implement the Element interface */
	public function test_implements_element(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertInstanceOf( Element::class, $fieldset );
	}

	/** @testdox It should be possible to get the name */
	public function test_get_name(): void {
		$fieldset = new Fieldset( 'personal' );
		$this->assertEquals( 'personal', $fieldset->get_name() );
	}

	/** @testdox The name should be sanitized */
	public function test_name_sanitized(): void {
		$fieldset = new Fieldset( 'My Fieldset' );
		$this->assertEquals( 'my-fieldset', $fieldset->get_name() );
	}

	/** @testdox A Fieldset should return a type of "fieldset" */
	public function test_type(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertEquals( 'fieldset', $fieldset->get_type() );
	}

	/** @testdox It should be possible to create a Fieldset using the static make method */
	public function test_static_make(): void {
		$fieldset = Fieldset::make( 'test' );
		$this->assertInstanceOf( Fieldset::class, $fieldset );
		$this->assertEquals( 'test', $fieldset->get_name() );
	}

	####################################################################
	######                     LEGEND                             ######
	####################################################################

	/** @testdox The default legend should be null */
	public function test_default_legend(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertNull( $fieldset->get_legend() );
	}

	/** @testdox It should be possible to set the legend text */
	public function test_set_legend(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->legend( 'Personal Info' );
		$this->assertEquals( 'Personal Info', $fieldset->get_legend() );
	}

	/** @testdox It should be possible to check if a legend is set */
	public function test_has_legend(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertFalse( $fieldset->has_legend() );
		$fieldset->legend( 'Test' );
		$this->assertTrue( $fieldset->has_legend() );
	}

	/** @testdox The legend setter should return the instance for chaining */
	public function test_legend_returns_self(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertSame( $fieldset, $fieldset->legend( 'Test' ) );
	}

	####################################################################
	######                     DISABLED                           ######
	####################################################################

	/** @testdox It should be possible to disable a fieldset */
	public function test_disabled(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->disabled( true );
		$this->assertTrue( $fieldset->is_disabled() );
	}

	/** @testdox By default the fieldset should not be disabled */
	public function test_default_not_disabled(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertFalse( $fieldset->is_disabled() );
	}

	####################################################################
	######                     FIELDS                             ######
	####################################################################

	/** @testdox It should be possible to add fields */
	public function test_add_fields(): void {
		$fieldset = Fieldset::make( 'test' )->fields(
			Text::make( 'name' ),
			Email::make( 'email' )
		);

		$this->assertCount( 2, $fieldset->get_fields() );
		$this->assertArrayHasKey( 'name', $fieldset->get_fields() );
		$this->assertArrayHasKey( 'email', $fieldset->get_fields() );
	}

	####################################################################
	######                     STYLE                              ######
	####################################################################

	/** @testdox The fieldset should have a default style */
	public function test_default_style(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertInstanceOf( Default_Style::class, $fieldset->get_style() );
	}

	/** @testdox The fieldset should have style classes on wrapper */
	public function test_style_classes(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertStringContainsString( 'pc-form__element--fieldset', $fieldset->get_wrapper_attribute( 'class' ) );
	}

	/** @testdox The fieldset should have a default wrapper id */
	public function test_default_wrapper_id(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertEquals( 'fieldset-test', $fieldset->get_wrapper_attribute( 'id' ) );
	}

	####################################################################
	######                  ELEMENT WRAP                          ######
	####################################################################

	/** @testdox It should be possible to set before content */
	public function test_before_content(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->before( '<p>Before</p>' );
		$this->assertEquals( '<p>Before</p>', $fieldset->get_before() );
	}

	/** @testdox It should be possible to set after content */
	public function test_after_content(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->after( '<p>After</p>' );
		$this->assertEquals( '<p>After</p>', $fieldset->get_after() );
	}

	####################################################################
	######                  FLUENT API                            ######
	####################################################################

	/** @testdox The full fluent API should work */
	public function test_fluent_api(): void {
		$fieldset = Fieldset::make( 'personal' )
			->legend( 'Personal Details' )
			->disabled( true )
			->before( '<p>Fill in</p>' )
			->fields(
				Text::make( 'name' )->label( 'Name' ),
				Email::make( 'email' )->label( 'Email' )
			);

		$this->assertEquals( 'personal', $fieldset->get_name() );
		$this->assertEquals( 'Personal Details', $fieldset->get_legend() );
		$this->assertTrue( $fieldset->is_disabled() );
		$this->assertCount( 2, $fieldset->get_fields() );
	}

	/** @testdox It should be possible to construct a Fieldset with a custom style */
	public function test_custom_style(): void {
		$style    = new Default_Style();
		$fieldset = new Fieldset( 'test', $style );
		$this->assertSame( $style, $fieldset->get_style() );
	}

	/** @testdox Getting wrapper class attribute should include element wrapper style */
	public function test_get_wrapper_attribute_class(): void {
		$fieldset = new Fieldset( 'test' );
		$class    = $fieldset->get_wrapper_attribute( 'class' );
		$this->assertStringContainsString( 'pc-form__element', $class );
		$this->assertStringContainsString( 'fieldset', $class );
	}

	/** @testdox Getting wrapper class with custom class added should merge */
	public function test_get_wrapper_attribute_class_with_custom(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->add_wrapper_class( 'custom-class' );
		$class = $fieldset->get_wrapper_attribute( 'class' );
		$this->assertStringContainsString( 'custom-class', $class );
		$this->assertStringContainsString( 'pc-form__element', $class );
	}

	/** @testdox It should be possible to get a single attribute via the base trait method */
	public function test_get_attribute(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertNull( $fieldset->get_attribute( 'data-nope' ) );
		$fieldset->attribute( 'data-foo', 'bar' );
		$this->assertEquals( 'bar', $fieldset->get_attribute( 'data-foo' ) );
	}

	/** @testdox It should be possible to get all attributes via the base trait method */
	public function test_get_attributes(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->attribute( 'data-a', '1' );
		$fieldset->attribute( 'data-b', '2' );
		$attrs = $fieldset->get_attributes();
		$this->assertIsArray( $attrs );
		$this->assertArrayHasKey( 'data-a', $attrs );
		$this->assertArrayHasKey( 'data-b', $attrs );
	}

	/** @testdox Adding a class when no class attribute exists should set it */
	public function test_add_class_when_no_class_exists(): void {
		$fieldset = new Fieldset( 'test' );
		// Clear any existing class via reflection
		$reflection = new \ReflectionClass( $fieldset );
		$prop       = $reflection->getProperty( 'attributes' );
		$prop->setAccessible( true );
		$attrs = $prop->getValue( $fieldset );
		unset( $attrs['class'] );
		$prop->setValue( $fieldset, $attrs );

		$fieldset->add_class( 'my-class' );
		$this->assertEquals( 'my-class', $fieldset->get_attribute( 'class' ) );
	}

	/** @testdox Removing a class when no class attribute exists should not throw */
	public function test_remove_class_when_no_class_exists(): void {
		$fieldset = new Fieldset( 'test' );
		// Clear any existing class via reflection
		$reflection = new \ReflectionClass( $fieldset );
		$prop       = $reflection->getProperty( 'attributes' );
		$prop->setAccessible( true );
		$attrs = $prop->getValue( $fieldset );
		unset( $attrs['class'] );
		$prop->setValue( $fieldset, $attrs );

		$this->assertSame( $fieldset, $fieldset->remove_class( 'nope' ) );
	}

	/** @testdox Adding and removing classes should work via base trait */
	public function test_add_and_remove_class(): void {
		$fieldset = new Fieldset( 'test' );
		// Clear any existing class via reflection
		$reflection = new \ReflectionClass( $fieldset );
		$prop       = $reflection->getProperty( 'attributes' );
		$prop->setAccessible( true );
		$attrs = $prop->getValue( $fieldset );
		unset( $attrs['class'] );
		$prop->setValue( $fieldset, $attrs );

		$fieldset->add_class( 'foo' );
		$fieldset->add_class( 'bar' );
		$this->assertStringContainsString( 'foo', $fieldset->get_attribute( 'class' ) );
		$this->assertStringContainsString( 'bar', $fieldset->get_attribute( 'class' ) );

		$fieldset->remove_class( 'foo' );
		$this->assertStringNotContainsString( 'foo', $fieldset->get_attribute( 'class' ) );
		$this->assertStringContainsString( 'bar', $fieldset->get_attribute( 'class' ) );
	}

	####################################################################
	######                  DESCRIPTION                            ######
	####################################################################

	/**
	 * @testdox It should be possible to set and get a pre-description
	 * @see https://github.com/Pink-Crab/Perique-Form-Components/issues/18
	 */
	public function test_pre_description(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->pre_description( 'Hint before fields' );
		$this->assertTrue( $fieldset->has_pre_description() );
		$this->assertSame( 'Hint before fields', $fieldset->get_pre_description() );
	}

	/**
	 * @testdox It should be possible to set and get a post-description
	 * @see https://github.com/Pink-Crab/Perique-Form-Components/issues/18
	 */
	public function test_post_description(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->post_description( 'Help text after fields' );
		$this->assertTrue( $fieldset->has_post_description() );
		$this->assertSame( 'Help text after fields', $fieldset->get_post_description() );
	}

	/**
	 * @testdox A fieldset with no descriptions should return null
	 * @see https://github.com/Pink-Crab/Perique-Form-Components/issues/18
	 */
	public function test_no_descriptions_returns_null(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertFalse( $fieldset->has_pre_description() );
		$this->assertFalse( $fieldset->has_post_description() );
		$this->assertNull( $fieldset->get_pre_description() );
		$this->assertNull( $fieldset->get_post_description() );
	}
}
