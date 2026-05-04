<?php

declare(strict_types=1);

/**
 * Unit tests for individual Component classes that have 0% coverage.
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Component;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Form;
use PinkCrab\Form_Components\Element\Button;
use PinkCrab\Form_Components\Element\Raw_HTML;
use PinkCrab\Form_Components\Element\Fieldset;
use PinkCrab\Form_Components\Element\Field\Select;
use PinkCrab\Form_Components\Element\Field\Textarea;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Group\Checkbox_Group;
use PinkCrab\Form_Components\Element\Field\Group\Radio_Group;
use PinkCrab\Form_Components\Component\Field\Button_Component;
use PinkCrab\Form_Components\Component\Field\Raw_HTML_Component;
use PinkCrab\Form_Components\Component\Field\Select_Component;
use PinkCrab\Form_Components\Component\Field\Textarea_Component;
use PinkCrab\Form_Components\Component\Field\Checkbox_Group_Component;
use PinkCrab\Form_Components\Component\Field\Radio_Group_Component;
use PinkCrab\Form_Components\Component\Field\Datalist_Component;
use PinkCrab\Form_Components\Component\Field\Label_Component;
use PinkCrab\Form_Components\Component\Field\Notification_Component;
use PinkCrab\Form_Components\Component\Form\Form_Component;
use PinkCrab\Form_Components\Component\Form\Fieldset_Component;
use PinkCrab\Form_Components\Component\Form\Group_Component;
use PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End;
use PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Perique\Services\View\Component\Component;

/**
 * @group unit
 * @group component
 */
class Test_Components extends WP_UnitTestCase {

	####################################################################
	######                  DATALIST COMPONENT                   ######
	####################################################################

	/** @testdox Datalist_Component should be constructable with an id and items */
	public function test_datalist_component_construct(): void {
		$component = new Datalist_Component( 'my_list', array( 'opt1' => 'Label 1', 'opt2' => 'Label 2' ) );
		$this->assertInstanceOf( Component::class, $component );
	}

	/** @testdox Datalist_Component should store its id and items */
	public function test_datalist_component_properties(): void {
		$items     = array( 'value1' => 'Label 1', 'value2' => null );
		$component = new Datalist_Component( 'test_list', $items );

		// Access via reflection since properties are protected
		$reflection = new \ReflectionClass( $component );

		$id_prop = $reflection->getProperty( 'id' );
		$id_prop->setAccessible( true );
		$this->assertEquals( 'test_list', $id_prop->getValue( $component ) );

		$items_prop = $reflection->getProperty( 'items' );
		$items_prop->setAccessible( true );
		$this->assertEquals( $items, $items_prop->getValue( $component ) );
	}

	####################################################################
	######                   LABEL COMPONENT                     ######
	####################################################################

	/** @testdox Label_Component should be constructable with a label and for_name */
	public function test_label_component_construct(): void {
		$component = new Label_Component( 'My Label', 'field_name' );
		$this->assertInstanceOf( Component::class, $component );
	}

	/** @testdox Label_Component should store its properties */
	public function test_label_component_properties(): void {
		$component  = new Label_Component( 'Email Address', 'email_field', 'custom-class' );
		$reflection = new \ReflectionClass( $component );

		$label_prop = $reflection->getProperty( 'label' );
		$label_prop->setAccessible( true );
		$this->assertEquals( 'Email Address', $label_prop->getValue( $component ) );

		$for_prop = $reflection->getProperty( 'for_name' );
		$for_prop->setAccessible( true );
		$this->assertEquals( 'email_field', $for_prop->getValue( $component ) );

		$class_prop = $reflection->getProperty( 'label_class' );
		$class_prop->setAccessible( true );
		$this->assertEquals( 'custom-class', $class_prop->getValue( $component ) );
	}

	/** @testdox Label_Component label_class should default to empty string */
	public function test_label_component_default_class(): void {
		$component  = new Label_Component( 'My Label', 'field_name' );
		$reflection = new \ReflectionClass( $component );

		$class_prop = $reflection->getProperty( 'label_class' );
		$class_prop->setAccessible( true );
		$this->assertEquals( '', $class_prop->getValue( $component ) );
	}

	####################################################################
	######               NOTIFICATION COMPONENT                  ######
	####################################################################

	/** @testdox Notification_Component should be constructable from a field with notification */
	public function test_notification_component_construct(): void {
		$text = new Text( 'test' );
		$text->error_notification( 'This field is required' );
		$component = new Notification_Component( $text );
		$this->assertInstanceOf( Component::class, $component );
	}

	/** @testdox Notification_Component should store notification and wrapper class */
	public function test_notification_component_properties(): void {
		$text = new Text( 'test' );
		$text->warning_notification( 'Please check this' );
		$component  = new Notification_Component( $text );
		$reflection = new \ReflectionClass( $component );

		$notif_prop = $reflection->getProperty( 'notification' );
		$notif_prop->setAccessible( true );
		$this->assertEquals( 'Please check this', $notif_prop->getValue( $component ) );

		$class_prop = $reflection->getProperty( 'wrapper_class' );
		$class_prop->setAccessible( true );
		$this->assertStringContainsString( 'warning', $class_prop->getValue( $component ) );
	}

	/** @testdox Notification_Component should throw for element without Notification trait */
	public function test_notification_component_throws_without_notification_trait(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Element must implement Notification' );

		// Use a mock of the Element interface which won't have the Notification trait
		$element = $this->createMock( \PinkCrab\Form_Components\Element\Element::class );
		new Notification_Component( $element );
	}

	/** @testdox Notification_Component should throw for element with Notification but without Form_Style trait */
	public function test_notification_component_throws_without_form_style(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Element must have a style' );

		// Create an element that uses Notification but not Form_Style
		$element = new \PinkCrab\Form_Components\Tests\Fixtures\Mock_Objects\Element_With_Notification_Only( 'test' );
		new Notification_Component( $element );
	}

	####################################################################
	######                  GROUP COMPONENT                      ######
	####################################################################

	/** @testdox Group_Component should be constructable */
	public function test_group_component_construct(): void {
		$component = new Group_Component( array(), 'id="test"' );
		$this->assertInstanceOf( Component::class, $component );
	}

	/** @testdox Group_Component should store its properties */
	public function test_group_component_properties(): void {
		$child      = new Input_Component( new Text( 'field1' ) );
		$component  = new Group_Component( array( $child ), 'id="group1" class="wrapper"', '<h3>Before</h3>', '<p>After</p>' );
		$reflection = new \ReflectionClass( $component );

		$comp_prop = $reflection->getProperty( 'components' );
		$comp_prop->setAccessible( true );
		$this->assertCount( 1, $comp_prop->getValue( $component ) );

		$attr_prop = $reflection->getProperty( 'attributes' );
		$attr_prop->setAccessible( true );
		$this->assertEquals( 'id="group1" class="wrapper"', $attr_prop->getValue( $component ) );

		$before_prop = $reflection->getProperty( 'before' );
		$before_prop->setAccessible( true );
		$this->assertEquals( '<h3>Before</h3>', $before_prop->getValue( $component ) );

		$after_prop = $reflection->getProperty( 'after' );
		$after_prop->setAccessible( true );
		$this->assertEquals( '<p>After</p>', $after_prop->getValue( $component ) );
	}

	/** @testdox Group_Component before/after should default to empty strings */
	public function test_group_component_defaults(): void {
		$component  = new Group_Component( array(), '' );
		$reflection = new \ReflectionClass( $component );

		$before_prop = $reflection->getProperty( 'before' );
		$before_prop->setAccessible( true );
		$this->assertEquals( '', $before_prop->getValue( $component ) );

		$after_prop = $reflection->getProperty( 'after' );
		$after_prop->setAccessible( true );
		$this->assertEquals( '', $after_prop->getValue( $component ) );
	}

	####################################################################
	######              FIELD WRAPPER END COMPONENT               ######
	####################################################################

	/** @testdox Field_Wrapper_End should be constructable with no args (after_field moved into field templates per issue #23) */
	public function test_field_wrapper_end_construct(): void {
		$component = new Field_Wrapper_End();
		$this->assertInstanceOf( Component::class, $component );
	}

	####################################################################
	######            FIELD WRAPPER START COMPONENT               ######
	####################################################################

	/** @testdox Field_Wrapper_Start should be constructable (before_field moved into field templates per issue #23) */
	public function test_field_wrapper_start_construct(): void {
		$component = new Field_Wrapper_Start( 'class="wrapper"' );
		$this->assertInstanceOf( Component::class, $component );
	}

	/** @testdox Field_Wrapper_Start stores wrapper_attributes for the partial template */
	public function test_field_wrapper_start_properties(): void {
		$component  = new Field_Wrapper_Start( 'id="wrapper" class="test"' );
		$reflection = new \ReflectionClass( $component );

		$attr_prop = $reflection->getProperty( 'wrapper_attributes' );
		$attr_prop->setAccessible( true );
		$this->assertEquals( 'id="wrapper" class="test"', $attr_prop->getValue( $component ) );
	}

	####################################################################
	######                  FORM COMPONENT                       ######
	####################################################################

	/** @testdox Form_Component should include action attribute when set */
	public function test_form_component_with_action(): void {
		$form = new Form( 'test_form' );
		$form->action( '/submit' );
		$form->fields( new Text( 'name' ) );
		$component  = new Form_Component( $form );
		$reflection = new \ReflectionClass( $component );

		$attr_prop = $reflection->getProperty( 'form_attributes' );
		$attr_prop->setAccessible( true );
		$this->assertStringContainsString( 'action=', $attr_prop->getValue( $component ) );
	}

	/** @testdox Form_Component should include enctype attribute when set */
	public function test_form_component_with_enctype(): void {
		$form = new Form( 'test_form' );
		$form->enctype( 'multipart/form-data' );
		$form->fields( new Text( 'name' ) );
		$component  = new Form_Component( $form );
		$reflection = new \ReflectionClass( $component );

		$attr_prop = $reflection->getProperty( 'form_attributes' );
		$attr_prop->setAccessible( true );
		$this->assertStringContainsString( 'enctype=', $attr_prop->getValue( $component ) );
		$this->assertStringContainsString( 'multipart/form-data', $attr_prop->getValue( $component ) );
	}

	/** @testdox Form_Component should set before and after content */
	public function test_form_component_before_after(): void {
		$form = new Form( 'test_form' );
		$form->before( '<h2>Title</h2>' );
		$form->after( '<p>Footer</p>' );
		$component  = new Form_Component( $form );
		$reflection = new \ReflectionClass( $component );

		$before_prop = $reflection->getProperty( 'before_form' );
		$before_prop->setAccessible( true );
		$this->assertEquals( '<h2>Title</h2>', $before_prop->getValue( $component ) );

		$after_prop = $reflection->getProperty( 'after_form' );
		$after_prop->setAccessible( true );
		$this->assertEquals( '<p>Footer</p>', $after_prop->getValue( $component ) );
	}

	/** @testdox Form_Component should default before/after to empty strings */
	public function test_form_component_no_before_after(): void {
		$form       = new Form( 'test_form' );
		$component  = new Form_Component( $form );
		$reflection = new \ReflectionClass( $component );

		$before_prop = $reflection->getProperty( 'before_form' );
		$before_prop->setAccessible( true );
		$this->assertEquals( '', $before_prop->getValue( $component ) );

		$after_prop = $reflection->getProperty( 'after_form' );
		$after_prop->setAccessible( true );
		$this->assertEquals( '', $after_prop->getValue( $component ) );
	}

	####################################################################
	######                FIELDSET COMPONENT                     ######
	####################################################################

	/** @testdox Fieldset_Component should include disabled attribute when set */
	public function test_fieldset_component_disabled(): void {
		$fieldset = new Fieldset( 'test_fieldset' );
		$fieldset->disabled();
		$fieldset->fields( new Text( 'name' ) );
		$component  = new Fieldset_Component( $fieldset );
		$reflection = new \ReflectionClass( $component );

		$attr_prop = $reflection->getProperty( 'fieldset_attributes' );
		$attr_prop->setAccessible( true );
		$this->assertStringContainsString( 'disabled', $attr_prop->getValue( $component ) );
	}

	/** @testdox Fieldset_Component should set legend and before/after */
	public function test_fieldset_component_legend_and_wrap(): void {
		$fieldset = new Fieldset( 'test_fieldset' );
		$fieldset->legend( 'My Fieldset' );
		$fieldset->before( '<p>Before</p>' );
		$fieldset->after( '<p>After</p>' );
		$component  = new Fieldset_Component( $fieldset );
		$reflection = new \ReflectionClass( $component );

		$legend_prop = $reflection->getProperty( 'legend' );
		$legend_prop->setAccessible( true );
		$this->assertEquals( 'My Fieldset', $legend_prop->getValue( $component ) );

		$before_prop = $reflection->getProperty( 'before' );
		$before_prop->setAccessible( true );
		$this->assertEquals( '<p>Before</p>', $before_prop->getValue( $component ) );

		$after_prop = $reflection->getProperty( 'after' );
		$after_prop->setAccessible( true );
		$this->assertEquals( '<p>After</p>', $after_prop->getValue( $component ) );
	}

	####################################################################
	######            ABSTRACT FIELD COMPONENT                   ######
	####################################################################

	/** @testdox It should be possible to set before_field on a field component */
	public function test_abstract_field_before_field(): void {
		$text      = new Text( 'test' );
		$component = new Input_Component( $text );
		$result    = $component->before_field( '<span>Before</span>' );
		$this->assertSame( $component, $result );

		$reflection = new \ReflectionClass( $component );
		$prop       = $reflection->getProperty( 'before_field' );
		$prop->setAccessible( true );
		$this->assertEquals( '<span>Before</span>', $prop->getValue( $component ) );
	}

	/** @testdox It should be possible to set after_field on a field component */
	public function test_abstract_field_after_field(): void {
		$text      = new Text( 'test' );
		$component = new Input_Component( $text );
		$result    = $component->after_field( '<span>After</span>' );
		$this->assertSame( $component, $result );

		$reflection = new \ReflectionClass( $component );
		$prop       = $reflection->getProperty( 'after_field' );
		$prop->setAccessible( true );
		$this->assertEquals( '<span>After</span>', $prop->getValue( $component ) );
	}

	####################################################################
	######               INPUT COMPONENT DETAILS                 ######
	####################################################################

	/** @testdox Input_Component should set before and after from the field */
	public function test_input_component_before_after(): void {
		$text = new Text( 'test' );
		$text->before( '<span>Before</span>' );
		$text->after( '<span>After</span>' );
		$component  = new Input_Component( $text );
		$reflection = new \ReflectionClass( $component );

		$before_prop = $reflection->getProperty( 'before_field' );
		$before_prop->setAccessible( true );
		$this->assertEquals( '<span>Before</span>', $before_prop->getValue( $component ) );

		$after_prop = $reflection->getProperty( 'after_field' );
		$after_prop->setAccessible( true );
		$this->assertEquals( '<span>After</span>', $after_prop->getValue( $component ) );
	}

	/** @testdox Input_Component should accept additional attributes */
	public function test_input_component_with_extra_attributes(): void {
		$text      = new Text( 'test' );
		$component = new Input_Component( $text, array( 'data-custom' => 'value' ) );
		$this->assertInstanceOf( Component::class, $component );
	}

	/** @testdox Input_Component should include wrapper id when not explicitly set */
	public function test_input_component_auto_wrapper_id(): void {
		$text = new Text( 'my_field' );

		// Remove the auto-set wrapper id so set_wrapper_attributes generates one
		$text->remove_wrapper_attribute( 'id' );

		$component  = new Input_Component( $text );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'wrapper_attributes' );
		$prop->setAccessible( true );
		$this->assertStringContainsString( 'field_my_field_wrapper', $prop->getValue( $component ) );
	}

	/** @testdox Input_Component should keep existing wrapper id */
	public function test_input_component_existing_wrapper_id(): void {
		$text = new Text( 'my_field' );
		$text->wrapper_attribute( 'id', 'custom_wrapper' );
		$component  = new Input_Component( $text );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'wrapper_attributes' );
		$prop->setAccessible( true );
		$this->assertStringContainsString( 'custom_wrapper', $prop->getValue( $component ) );
	}

	/** @testdox Input_Component should set show_wrapper from field */
	public function test_input_component_show_wrapper(): void {
		$text = new Text( 'test' );
		$text->show_wrapper( false );
		$component  = new Input_Component( $text );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'show_wrapper' );
		$prop->setAccessible( true );
		$this->assertFalse( $prop->getValue( $component ) );
	}

	/** @testdox Input_Component should include datalist key in base attributes for fields with Datalist trait */
	public function test_input_component_datalist_base_attribute(): void {
		$text = new Text( 'test' );
		$text->datalist_item( 'opt1', 'Label 1' );
		$component  = new Input_Component( $text );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'field_attributes' );
		$prop->setAccessible( true );
		$this->assertStringContainsString( 'list=', $prop->getValue( $component ) );
	}

	####################################################################
	######              BUTTON COMPONENT DETAILS                 ######
	####################################################################

	/** @testdox Button_Component should store type and text */
	public function test_button_component_properties(): void {
		$button = new Button( 'test_btn' );
		$button->type( 'submit' );
		$button->text( 'Submit Form' );
		$button->before( '<div>' );
		$button->after( '</div>' );
		$component  = new Button_Component( $button );
		$reflection = new \ReflectionClass( $component );

		$type_prop = $reflection->getProperty( 'type' );
		$type_prop->setAccessible( true );
		$this->assertEquals( 'submit', $type_prop->getValue( $component ) );

		$text_prop = $reflection->getProperty( 'text' );
		$text_prop->setAccessible( true );
		$this->assertEquals( 'submit form', $text_prop->getValue( $component ) );
	}

	/** @testdox Button_Component should accept additional attributes */
	public function test_button_component_with_extra_attributes(): void {
		$button    = new Button( 'test' );
		$component = new Button_Component( $button, array( 'data-action' => 'submit' ) );
		$this->assertInstanceOf( Component::class, $component );
	}

	####################################################################
	######              SELECT COMPONENT DETAILS                 ######
	####################################################################

	/** @testdox Select_Component should set field attributes with base class */
	public function test_select_component_attributes(): void {
		$select = new Select( 'my_select' );
		$select->before( '<label>Pick one</label>' );
		$select->after( '<small>Help</small>' );
		$component  = new Select_Component( $select );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'field_attributes' );
		$prop->setAccessible( true );
		$this->assertStringContainsString( 'class=', $prop->getValue( $component ) );
		$this->assertStringContainsString( 'select', $prop->getValue( $component ) );
	}

	/** @testdox Select_Component should accept additional attributes */
	public function test_select_component_with_extra_attributes(): void {
		$select    = new Select( 'test' );
		$component = new Select_Component( $select, array( 'data-search' => 'true' ) );
		$this->assertInstanceOf( Component::class, $component );
	}

	####################################################################
	######             TEXTAREA COMPONENT DETAILS                ######
	####################################################################

	/** @testdox Textarea_Component should set field attributes with base class */
	public function test_textarea_component_attributes(): void {
		$textarea = new Textarea( 'my_textarea' );
		$textarea->before( '<label>Content</label>' );
		$textarea->after( '<small>Max 500 chars</small>' );
		$component  = new Textarea_Component( $textarea );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'field_attributes' );
		$prop->setAccessible( true );
		$this->assertStringContainsString( 'textarea', $prop->getValue( $component ) );
	}

	/** @testdox Textarea_Component should accept additional attributes */
	public function test_textarea_component_with_extra_attributes(): void {
		$textarea  = new Textarea( 'test' );
		$component = new Textarea_Component( $textarea, array( 'rows' => '5' ) );
		$this->assertInstanceOf( Component::class, $component );
	}

	####################################################################
	######          CHECKBOX GROUP COMPONENT DETAILS             ######
	####################################################################

	/** @testdox Checkbox_Group_Component should set field attributes with base class */
	public function test_checkbox_group_component_attributes(): void {
		$group = new Checkbox_Group( 'my_checks' );
		$group->before( '<p>Select all</p>' );
		$group->after( '<p>Done</p>' );
		$component  = new Checkbox_Group_Component( $group );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'field_attributes' );
		$prop->setAccessible( true );
		$this->assertStringContainsString( 'checkbox-group', $prop->getValue( $component ) );
	}

	/** @testdox Checkbox_Group_Component should accept additional attributes */
	public function test_checkbox_group_component_with_extra_attributes(): void {
		$group     = new Checkbox_Group( 'test' );
		$component = new Checkbox_Group_Component( $group, array( 'data-max' => '3' ) );
		$this->assertInstanceOf( Component::class, $component );
	}

	####################################################################
	######           RADIO GROUP COMPONENT DETAILS               ######
	####################################################################

	/** @testdox Radio_Group_Component should set field attributes with base class */
	public function test_radio_group_component_attributes(): void {
		$group = new Radio_Group( 'my_radios' );
		$group->before( '<p>Choose one</p>' );
		$group->after( '<p>End</p>' );
		$component  = new Radio_Group_Component( $group );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'field_attributes' );
		$prop->setAccessible( true );
		$this->assertStringContainsString( 'radio-group', $prop->getValue( $component ) );
	}

	/** @testdox Radio_Group_Component should accept additional attributes */
	public function test_radio_group_component_with_extra_attributes(): void {
		$group     = new Radio_Group( 'test' );
		$component = new Radio_Group_Component( $group, array( 'data-required' => 'true' ) );
		$this->assertInstanceOf( Component::class, $component );
	}

	####################################################################
	######            RAW HTML COMPONENT DETAILS                 ######
	####################################################################

	/** @testdox Raw_HTML_Component should store the html from the element */
	public function test_raw_html_component_stores_html(): void {
		$html      = new Raw_HTML( 'divider' );
		$html->html( '<hr class="divider">' );
		$component  = new Raw_HTML_Component( $html );
		$reflection = new \ReflectionClass( $component );

		$prop = $reflection->getProperty( 'html' );
		$prop->setAccessible( true );
		$this->assertEquals( '<hr class="divider">', $prop->getValue( $component ) );
	}
}
