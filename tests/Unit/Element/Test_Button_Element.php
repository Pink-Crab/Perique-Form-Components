<?php

declare(strict_types=1);

/**
 * Unit tests for the Button Element (not Input\Button)
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Button;
use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Style\Default_Style;

/**
 * @group unit
 * @group element
 * @group button_element
 */
class Test_Button_Element extends WP_UnitTestCase {

	/** @testdox Button should implement the Element interface */
	public function test_implements_element(): void {
		$button = new Button( 'test' );
		$this->assertInstanceOf( Element::class, $button );
	}

	/** @testdox It should be possible to get the name */
	public function test_get_name(): void {
		$button = new Button( 'submit_btn' );
		$this->assertEquals( 'submit_btn', $button->get_name() );
	}

	/** @testdox The name should be sanitized */
	public function test_name_sanitized(): void {
		$button = new Button( 'My Button' );
		$this->assertEquals( 'my-button', $button->get_name() );
	}

	/** @testdox The default type should be "button" */
	public function test_default_type(): void {
		$button = new Button( 'test' );
		$this->assertEquals( 'button', $button->get_type() );
	}

	/** @testdox It should be possible to set the button type */
	public function test_set_type(): void {
		$button = new Button( 'test' );
		$button->type( 'submit' );
		$this->assertEquals( 'submit', $button->get_type() );
	}

	/** @testdox The default text should be empty */
	public function test_default_text(): void {
		$button = new Button( 'test' );
		$this->assertEquals( '', $button->get_text() );
	}

	/** @testdox It should be possible to set the button text */
	public function test_set_text(): void {
		$button = new Button( 'test' );
		$button->text( 'Click Me' );
		$this->assertEquals( 'Click Me', $button->get_text() );
	}

	/** @testdox It should be possible to create a Button using the static make method */
	public function test_static_make(): void {
		$button = Button::make( 'test' );
		$this->assertInstanceOf( Button::class, $button );
		$this->assertEquals( 'test', $button->get_name() );
	}

	/** @testdox The button should have a default style */
	public function test_default_style(): void {
		$button = new Button( 'test' );
		$this->assertInstanceOf( Default_Style::class, $button->get_style() );
	}

	/** @testdox The button should apply the button_class from style */
	public function test_button_class_applied(): void {
		$button = new Button( 'test' );
		$this->assertStringContainsString( 'pc-form__button', $button->get_attribute( 'class' ) );
	}

	/** @testdox It should be possible to set the button as disabled */
	public function test_disabled(): void {
		$button = new Button( 'test' );
		$button->disabled( true );
		$this->assertTrue( $button->is_disabled() );
	}

	/** @testdox The fluent API should work for chaining */
	public function test_fluent_api(): void {
		$button = Button::make( 'submit' )
			->type( 'submit' )
			->text( 'Send' )
			->disabled( true );

		$this->assertEquals( 'submit', $button->get_type() );
		$this->assertEquals( 'Send', $button->get_text() );
		$this->assertTrue( $button->is_disabled() );
	}

	####################################################################
	######                  WRAPPER ATTRIBUTES                    ######
	####################################################################

	/** @testdox The button should have a default wrapper id */
	public function test_default_wrapper_id(): void {
		$button = new Button( 'test' );
		$this->assertStringContainsString( 'form-button', $button->get_wrapper_attribute( 'id' ) );
	}

	/** @testdox It should be possible to add a wrapper class */
	public function test_add_wrapper_class(): void {
		$button = new Button( 'test' );
		$button->add_wrapper_class( 'custom-wrapper' );
		$this->assertStringContainsString( 'custom-wrapper', $button->get_wrapper_attribute( 'class' ) );
	}

	####################################################################
	######                  ELEMENT WRAP                          ######
	####################################################################

	/** @testdox It should be possible to set before content */
	public function test_before_content(): void {
		$button = new Button( 'test' );
		$button->before( '<span>Before</span>' );
		$this->assertEquals( '<span>Before</span>', $button->get_before() );
	}

	/** @testdox It should be possible to set after content */
	public function test_after_content(): void {
		$button = new Button( 'test' );
		$button->after( '<span>After</span>' );
		$this->assertEquals( '<span>After</span>', $button->get_after() );
	}

	/** @testdox Getting a non-class attribute should return null if not set */
	public function test_get_attribute_non_class_returns_null(): void {
		$button = new Button( 'test' );
		$this->assertNull( $button->get_attribute( 'data-something' ) );
	}

	/** @testdox Getting a non-class attribute that exists should return its value */
	public function test_get_attribute_non_class_returns_value(): void {
		$button = new Button( 'test' );
		$button->attribute( 'data-action', 'submit' );
		$this->assertEquals( 'submit', $button->get_attribute( 'data-action' ) );
	}

	/** @testdox It should be possible to construct a Button with a custom style */
	public function test_custom_style(): void {
		$style  = new Default_Style();
		$button = new Button( 'test', $style );
		$this->assertSame( $style, $button->get_style() );
	}

	/** @testdox Getting wrapper class attribute should include element wrapper style */
	public function test_get_wrapper_attribute_class(): void {
		$button = new Button( 'test' );
		$class  = $button->get_wrapper_attribute( 'class' );
		$this->assertStringContainsString( 'pc-form__element', $class );
		$this->assertStringContainsString( 'button', $class );
	}

	/** @testdox Getting wrapper class with custom class should merge */
	public function test_get_wrapper_attribute_class_with_custom(): void {
		$button = new Button( 'test' );
		$button->add_wrapper_class( 'my-custom' );
		$class = $button->get_wrapper_attribute( 'class' );
		$this->assertStringContainsString( 'my-custom', $class );
		$this->assertStringContainsString( 'pc-form__element', $class );
	}

	/** @testdox Getting all wrapper attributes should include style classes */
	public function test_get_wrapper_attributes(): void {
		$button = new Button( 'test' );
		$attrs  = $button->get_wrapper_attributes();
		$this->assertArrayHasKey( 'class', $attrs );
		$this->assertStringContainsString( 'button', $attrs['class'] );
	}

	/** @testdox Getting all attributes should include button style class */
	public function test_get_attributes_includes_style(): void {
		$button = new Button( 'test' );
		$attrs  = $button->get_attributes();
		$this->assertArrayHasKey( 'class', $attrs );
		$this->assertStringContainsString( 'pc-form__button', $attrs['class'] );
	}

	/** @testdox Getting class attribute with custom class added should merge with style */
	public function test_get_attribute_class_with_custom(): void {
		$button = new Button( 'test' );
		$button->add_class( 'extra' );
		$class = $button->get_attribute( 'class' );
		$this->assertStringContainsString( 'pc-form__button', $class );
		$this->assertStringContainsString( 'extra', $class );
	}
}
