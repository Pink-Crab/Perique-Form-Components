<?php

declare(strict_types=1);

/**
 * Unit tests for component factory
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Component;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Form;
use PinkCrab\Form_Components\Element\Nonce;
use PinkCrab\Form_Components\Element\Group;
use PinkCrab\Form_Components\Element\Button;
use PinkCrab\Form_Components\Element\Raw_HTML;
use PinkCrab\Form_Components\Element\Fieldset;
use PinkCrab\Form_Components\Element\Field\Select;
use PinkCrab\Form_Components\Element\Field\Textarea;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Radio;
use PinkCrab\Form_Components\Element\Field\Group\Radio_Group;
use PinkCrab\Form_Components\Element\Field\Group\Checkbox_Group;
use PinkCrab\Form_Components\Component\Component_Factory;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Component\Field\Button_Component;
use PinkCrab\Form_Components\Component\Field\Select_Component;
use PinkCrab\Form_Components\Component\Field\Raw_HTML_Component;
use PinkCrab\Form_Components\Component\Field\Textarea_Component;
use PinkCrab\Form_Components\Component\Field\Radio_Group_Component;
use PinkCrab\Form_Components\Component\Field\Checkbox_Group_Component;
use PinkCrab\Form_Components\Component\Form\Form_Component;
use PinkCrab\Form_Components\Component\Form\Group_Component;
use PinkCrab\Form_Components\Component\Form\Fieldset_Component;
use PinkCrab\Form_Components\Component\Partial\Nonce_Component;

/**
 * @group unit
 * @group component
 */
class Test_Component_Factory extends WP_UnitTestCase {

	/** @testdox It should be possible to get an instance of the component factory using a static constructor */
	public function test_get_instance(): void {
		$this->assertInstanceOf( Component_Factory::class, Component_Factory::instance() );
	}

	/** @testdox It should be possible to create an Input component from an Input field. */
	public function test_create_input(): void {
		$field  = new Text( 'test' );

        // Using from_field() method.
		$this->assertInstanceOf( Input_Component::class, Component_Factory::instance()->from_field( $field ) );

        // Using from_element() method.
        $this->assertInstanceOf( Input_Component::class, Component_Factory::instance()->from_element( $field ) );
	}

	/** @testdox It should be possible to create a Checkbox_Group component */
	public function test_create_checkbox_group(): void {
		$group = new Checkbox_Group( 'test_group' );
		$component = Component_Factory::instance()->from_checkbox_group( $group );
		$this->assertInstanceOf( Checkbox_Group_Component::class, $component );
	}

	/** @testdox from_element should correctly dispatch Checkbox_Group */
	public function test_from_element_checkbox_group(): void {
		$group = new Checkbox_Group( 'test_group' );
		$component = Component_Factory::instance()->from_element( $group );
		$this->assertInstanceOf( Checkbox_Group_Component::class, $component );
	}

	/** @testdox It should be possible to create a Radio_Group component */
	public function test_create_radio_group(): void {
		$group = new Radio_Group( 'test_group' );
		$component = Component_Factory::instance()->from_radio_group( $group );
		$this->assertInstanceOf( Radio_Group_Component::class, $component );
	}

	/** @testdox from_element should correctly dispatch Radio_Group */
	public function test_from_element_radio_group(): void {
		$group = new Radio_Group( 'test_group' );
		$component = Component_Factory::instance()->from_element( $group );
		$this->assertInstanceOf( Radio_Group_Component::class, $component );
	}

	/** @testdox It should be possible to create a Select component */
	public function test_create_select(): void {
		$select = new Select( 'test_select' );
		$component = Component_Factory::instance()->from_select( $select );
		$this->assertInstanceOf( Select_Component::class, $component );
	}

	/** @testdox from_element should correctly dispatch Select */
	public function test_from_element_select(): void {
		$select = new Select( 'test_select' );
		$component = Component_Factory::instance()->from_element( $select );
		$this->assertInstanceOf( Select_Component::class, $component );
	}

	/** @testdox It should be possible to create a Textarea component */
	public function test_create_textarea(): void {
		$textarea = new Textarea( 'test_textarea' );
		$component = Component_Factory::instance()->from_textarea( $textarea );
		$this->assertInstanceOf( Textarea_Component::class, $component );
	}

	/** @testdox from_element should correctly dispatch Textarea */
	public function test_from_element_textarea(): void {
		$textarea = new Textarea( 'test_textarea' );
		$component = Component_Factory::instance()->from_element( $textarea );
		$this->assertInstanceOf( Textarea_Component::class, $component );
	}

	/** @testdox It should be possible to create a Button component */
	public function test_create_button(): void {
		$button = new Button( 'test_button' );
		$component = Component_Factory::instance()->from_button( $button );
		$this->assertInstanceOf( Button_Component::class, $component );
	}

	/** @testdox from_element should correctly dispatch Button */
	public function test_from_element_button(): void {
		$button = new Button( 'test_button' );
		$component = Component_Factory::instance()->from_element( $button );
		$this->assertInstanceOf( Button_Component::class, $component );
	}

	/** @testdox It should be possible to create a Raw_HTML component */
	public function test_create_raw_html(): void {
		$html = new Raw_HTML( 'test_html' );
		$component = Component_Factory::instance()->from_html( $html );
		$this->assertInstanceOf( Raw_HTML_Component::class, $component );
	}

	/** @testdox from_element should correctly dispatch Raw_HTML */
	public function test_from_element_raw_html(): void {
		$html = new Raw_HTML( 'test_html' );
		$component = Component_Factory::instance()->from_element( $html );
		$this->assertInstanceOf( Raw_HTML_Component::class, $component );
	}

	/** @testdox from_element should correctly dispatch Nonce */
	public function test_from_element_nonce(): void {
		$nonce = new Nonce( 'test_action', 'test_nonce' );
		$component = Component_Factory::instance()->from_element( $nonce );
		$this->assertInstanceOf( Nonce_Component::class, $component );
	}

	/** @testdox from_element should correctly dispatch Form */
	public function test_from_element_form(): void {
		$form = new Form( 'test_form' );
		$component = Component_Factory::instance()->from_element( $form );
		$this->assertInstanceOf( Form_Component::class, $component );
	}

	/** @testdox from_element should correctly dispatch Fieldset */
	public function test_from_element_fieldset(): void {
		$fieldset = new Fieldset( 'test_fieldset' );
		$component = Component_Factory::instance()->from_element( $fieldset );
		$this->assertInstanceOf( Fieldset_Component::class, $component );
	}

	/** @testdox It should be possible to create a Group component */
	public function test_create_group(): void {
		$group = new Group( 'test_group' );
		$group->fields( new Text( 'field1' ) );
		$component = Component_Factory::instance()->from_group( $group );
		$this->assertInstanceOf( Group_Component::class, $component );
	}

	/** @testdox from_element should correctly dispatch Group */
	public function test_from_element_group(): void {
		$group = new Group( 'test_group' );
		$group->fields( new Text( 'field1' ) );
		$component = Component_Factory::instance()->from_element( $group );
		$this->assertInstanceOf( Group_Component::class, $component );
	}

	/** @testdox Group should auto-add wrapper id if not set */
	public function test_group_auto_wrapper_id(): void {
		$group = new Group( 'my_group' );
		$group->fields( new Text( 'field1' ) );

		// Remove the auto-set id via reflection so from_group adds its own
		$group->remove_wrapper_attribute( 'id' );

		$component  = Component_Factory::instance()->from_group( $group );
		$reflection = new \ReflectionClass( $component );

		$attr_prop = $reflection->getProperty( 'attributes' );
		$attr_prop->setAccessible( true );
		$this->assertStringContainsString( 'field_my_group_wrapper', $attr_prop->getValue( $component ) );
	}

	/** @testdox Group with existing wrapper id should keep it */
	public function test_group_existing_wrapper_id(): void {
		$group = new Group( 'my_group' );
		$group->wrapper_attribute( 'id', 'custom_id' );
		$group->fields( new Text( 'field1' ) );
		$component  = Component_Factory::instance()->from_group( $group );
		$reflection = new \ReflectionClass( $component );

		$attr_prop = $reflection->getProperty( 'attributes' );
		$attr_prop->setAccessible( true );
		$this->assertStringContainsString( 'custom_id', $attr_prop->getValue( $component ) );
		$this->assertStringNotContainsString( 'field_my_group', $attr_prop->getValue( $component ) );
	}

	/** @testdox Group with before/after content should pass it to the component */
	public function test_group_before_after(): void {
		$group = new Group( 'my_group' );
		$group->before( '<h3>Section</h3>' );
		$group->after( '<hr>' );
		$group->fields( new Text( 'field1' ) );
		$component  = Component_Factory::instance()->from_group( $group );
		$reflection = new \ReflectionClass( $component );

		$before_prop = $reflection->getProperty( 'before' );
		$before_prop->setAccessible( true );
		$this->assertEquals( '<h3>Section</h3>', $before_prop->getValue( $component ) );

		$after_prop = $reflection->getProperty( 'after' );
		$after_prop->setAccessible( true );
		$this->assertEquals( '<hr>', $after_prop->getValue( $component ) );
	}

	/** @testdox from_elements should convert an array of elements to components */
	public function test_from_elements(): void {
		$elements = array(
			new Text( 'text_field' ),
			new Select( 'select_field' ),
			new Textarea( 'textarea_field' ),
		);
		$components = Component_Factory::instance()->from_elements( $elements );
		$this->assertCount( 3, $components );
		$this->assertInstanceOf( Input_Component::class, $components[0] );
		$this->assertInstanceOf( Select_Component::class, $components[1] );
		$this->assertInstanceOf( Textarea_Component::class, $components[2] );
	}

	/** @testdox from_element should throw InvalidArgumentException for unknown element types */
	public function test_from_element_throws_for_unknown(): void {
		$this->expectException( \InvalidArgumentException::class );
		$unknown = $this->createMock( \PinkCrab\Form_Components\Element\Element::class );
		$unknown->method( 'get_type' )->willReturn( 'unknown' );
		Component_Factory::instance()->from_element( $unknown );
	}
}
