<?php

declare(strict_types=1);

/**
 * Unit tests for the Custom_Field Element
 *
 * @since 2.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Custom_Field;
use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Style\Style;
use PinkCrab\Form_Components\Style\Default_Style;
use PinkCrab\Form_Components\Component\Component_Factory;
use PinkCrab\Form_Components\Component\Field\Custom_Field_Component;
use PinkCrab\Perique\Services\View\Component\Component;

/**
 * @group unit
 * @group element
 * @group custom_field
 */
class Test_Custom_Field extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;
	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Description_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Custom_Field::class;
	}

	####################################################################
	######                     FIELD BASICS                      ######
	####################################################################

	/** @testdox Custom_Field should implement the Element interface */
	public function test_implements_element(): void {
		$field = new Custom_Field( 'test' );
		$this->assertInstanceOf( Element::class, $field );
	}

	/** @testdox Custom_Field should return type of custom_field */
	public function test_get_type(): void {
		$field = new Custom_Field( 'test' );
		$this->assertEquals( 'custom_field', $field->get_type() );
	}

	/** @testdox Custom_Field should be creatable via make */
	public function test_static_make(): void {
		$field = Custom_Field::make( 'test' );
		$this->assertInstanceOf( Custom_Field::class, $field );
	}

	/** @testdox Custom_Field should have a default style */
	public function test_default_style(): void {
		$field = new Custom_Field( 'test' );
		$this->assertInstanceOf( Style::class, $field->get_style() );
	}

	####################################################################
	######                      CONTENT                          ######
	####################################################################

	/** @testdox It should be possible to set and get content as a string */
	public function test_content_string(): void {
		$field = new Custom_Field( 'test' );
		$this->assertEquals( '', $field->get_content() );
		$this->assertFalse( $field->has_content() );

		$field->content( '<div class="picker">Custom Widget</div>' );
		$this->assertEquals( '<div class="picker">Custom Widget</div>', $field->get_content() );
		$this->assertTrue( $field->has_content() );
	}

	/** @testdox It should be possible to set content via a callable */
	public function test_content_callback(): void {
		$field = new Custom_Field( 'my_field' );
		$field->content_callback( function( Custom_Field $f ) {
			return '<input type="text" name="' . esc_attr( $f->get_name() ) . '">';
		} );
		$this->assertTrue( $field->has_content() );
		$this->assertStringContainsString( 'my_field', $field->get_content() );
	}

	/** @testdox Callback should take priority over string content */
	public function test_callback_priority(): void {
		$field = new Custom_Field( 'test' );
		$field->content( 'string content' );
		$field->content_callback( function() {
			return 'callback content';
		} );
		$this->assertEquals( 'callback content', $field->get_content() );
	}

	/** @testdox Content method should return self for chaining */
	public function test_content_returns_self(): void {
		$field = new Custom_Field( 'test' );
		$this->assertSame( $field, $field->content( 'html' ) );
	}

	/** @testdox Content callback method should return self for chaining */
	public function test_content_callback_returns_self(): void {
		$field = new Custom_Field( 'test' );
		$this->assertSame( $field, $field->content_callback( function() { return ''; } ) );
	}

	####################################################################
	######                     KSES RULES                        ######
	####################################################################

	/** @testdox By default kses should be enabled with wp_kses_post rules */
	public function test_default_kses(): void {
		$field = new Custom_Field( 'test' );
		$this->assertTrue( $field->is_kses_enabled() );
		$this->assertNull( $field->get_kses_rules() );
	}

	/** @testdox It should be possible to set custom kses rules */
	public function test_custom_kses_rules(): void {
		$rules = array(
			'div'    => array( 'class' => true, 'id' => true ),
			'span'   => array( 'class' => true ),
			'input'  => array( 'type' => true, 'name' => true, 'value' => true ),
			'script' => array(),
		);
		$field = new Custom_Field( 'test' );
		$result = $field->kses_rules( $rules );
		$this->assertSame( $field, $result );
		$this->assertEquals( $rules, $field->get_kses_rules() );
	}

	/** @testdox Custom kses rules should filter content through wp_kses */
	public function test_filter_with_custom_rules(): void {
		$field = new Custom_Field( 'test' );
		$field->kses_rules( array(
			'div'  => array( 'class' => true ),
			'span' => array(),
		) );
		$filtered = $field->filter_content( '<div class="ok"><script>bad</script><span>good</span></div>' );
		$this->assertStringNotContainsString( '<script>', $filtered );
		$this->assertStringContainsString( '<div class="ok">', $filtered );
		$this->assertStringContainsString( '<span>good</span>', $filtered );
	}

	/** @testdox Default kses should use wp_kses_post */
	public function test_filter_with_default_kses(): void {
		$field    = new Custom_Field( 'test' );
		$filtered = $field->filter_content( '<div class="ok"><script>bad</script></div>' );
		$this->assertStringNotContainsString( '<script>', $filtered );
		$this->assertStringContainsString( '<div class="ok">', $filtered );
	}

	/** @testdox It should be possible to disable kses entirely */
	public function test_disable_kses(): void {
		$field = new Custom_Field( 'test' );
		$field->disable_kses();
		$this->assertFalse( $field->is_kses_enabled() );

		$raw      = '<script>alert("hi")</script>';
		$filtered = $field->filter_content( $raw );
		$this->assertEquals( $raw, $filtered );
	}

	/** @testdox It should be possible to re-enable kses after disabling */
	public function test_enable_kses(): void {
		$field = new Custom_Field( 'test' );
		$field->disable_kses();
		$this->assertFalse( $field->is_kses_enabled() );
		$field->enable_kses();
		$this->assertTrue( $field->is_kses_enabled() );
	}

	####################################################################
	######                    SET EXISTING                        ######
	####################################################################

	/** @testdox set_existing should set the value */
	public function test_set_existing(): void {
		$field = new Custom_Field( 'test' );
		$field->set_existing( 'some value' );
		$this->assertEquals( 'some value', $field->get_value() );
	}

	####################################################################
	######                     LABEL TRAIT                        ######
	####################################################################

	/** @testdox It should be possible to set a label */
	public function test_label(): void {
		$field = new Custom_Field( 'test' );
		$field->label( 'My Custom Field' );
		$this->assertTrue( $field->has_label() );
		$this->assertEquals( 'My Custom Field', $field->get_label() );
	}

	####################################################################
	######                  NOTIFICATION TRAIT                    ######
	####################################################################

	/** @testdox It should be possible to set notifications */
	public function test_notification(): void {
		$field = new Custom_Field( 'test' );
		$field->error_notification( 'This field is required' );
		$this->assertTrue( $field->has_notification() );
		$this->assertEquals( 'This field is required', $field->get_notification() );
		$this->assertEquals( 'error', $field->get_notification_type() );
	}

	####################################################################
	######               COMPONENT FACTORY                       ######
	####################################################################

	/** @testdox Component_Factory should dispatch Custom_Field correctly */
	public function test_factory_from_element(): void {
		$field     = new Custom_Field( 'test' );
		$component = Component_Factory::instance()->from_element( $field );
		$this->assertInstanceOf( Custom_Field_Component::class, $component );
	}

	/** @testdox Component_Factory from_custom_field should work */
	public function test_factory_from_custom_field(): void {
		$field     = new Custom_Field( 'test' );
		$component = Component_Factory::instance()->from_custom_field( $field );
		$this->assertInstanceOf( Custom_Field_Component::class, $component );
	}

	####################################################################
	######                    COMPONENT                          ######
	####################################################################

	/** @testdox Custom_Field_Component should store filtered content */
	public function test_component_stores_content(): void {
		$field = new Custom_Field( 'test' );
		$field->content( '<div>Hello</div>' );
		$component  = new Custom_Field_Component( $field );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'content' );
		$prop->setAccessible( true );
		$this->assertStringContainsString( 'Hello', $prop->getValue( $component ) );
	}

	/** @testdox Custom_Field_Component should set before/after from field */
	public function test_component_before_after(): void {
		$field = new Custom_Field( 'test' );
		$field->before( '<p>Before</p>' );
		$field->after( '<p>After</p>' );
		$component  = new Custom_Field_Component( $field );
		$reflection = new \ReflectionClass( $component );

		$before = $reflection->getProperty( 'before_field' );
		$before->setAccessible( true );
		$this->assertEquals( '<p>Before</p>', $before->getValue( $component ) );

		$after = $reflection->getProperty( 'after_field' );
		$after->setAccessible( true );
		$this->assertEquals( '<p>After</p>', $after->getValue( $component ) );
	}

	/** @testdox Custom_Field_Component should include base class in field_attributes */
	public function test_component_base_attributes(): void {
		$field      = new Custom_Field( 'test' );
		$component  = new Custom_Field_Component( $field );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'field_attributes' );
		$prop->setAccessible( true );
		$this->assertStringContainsString( 'custom-field', $prop->getValue( $component ) );
	}

	####################################################################
	######                    FLUENT API                          ######
	####################################################################

	/** @testdox The fluent API should work for building a custom field */
	public function test_fluent_api(): void {
		$field = Custom_Field::make( 'widget' )
			->label( 'My Widget' )
			->content( '<div class="widget">Content</div>' )
			->before( '<section>' )
			->after( '</section>' )
			->error_notification( 'Required' )
			->kses_rules( array( 'div' => array( 'class' => true ) ) );

		$this->assertEquals( 'widget', $field->get_name() );
		$this->assertEquals( 'My Widget', $field->get_label() );
		$this->assertTrue( $field->has_content() );
		$this->assertEquals( 'error', $field->get_notification_type() );
		$this->assertNotNull( $field->get_kses_rules() );
	}
}
