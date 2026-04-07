<?php

declare(strict_types=1);

/**
 * Unit tests for the Form Element
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Form;
use PinkCrab\Form_Components\Element\Nonce;
use PinkCrab\Form_Components\Element\Button;
use PinkCrab\Form_Components\Element\Raw_HTML;
use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Email;
use PinkCrab\Form_Components\Element\Field\Textarea;
use PinkCrab\Form_Components\Element\Field\Select;
use PinkCrab\Form_Components\Style\Default_Style;

/**
 * @group unit
 * @group element
 * @group form
 */
class Test_Form extends WP_UnitTestCase {

	/** @testdox Form should implement the Element interface */
	public function test_implements_element(): void {
		$form = new Form( 'test' );
		$this->assertInstanceOf( Element::class, $form );
	}

	/** @testdox It should be possible to get the name */
	public function test_get_name(): void {
		$form = new Form( 'contact_form' );
		$this->assertEquals( 'contact_form', $form->get_name() );
	}

	/** @testdox The name should be sanitized */
	public function test_name_sanitized(): void {
		$form = new Form( 'My Form Name' );
		$this->assertEquals( 'my-form-name', $form->get_name() );
	}

	/** @testdox A Form element should return a type of "form" */
	public function test_type(): void {
		$form = new Form( 'test' );
		$this->assertEquals( 'form', $form->get_type() );
	}

	/** @testdox It should be possible to create a Form using the static make method */
	public function test_static_make(): void {
		$form = Form::make( 'test' );
		$this->assertInstanceOf( Form::class, $form );
		$this->assertEquals( 'test', $form->get_name() );
	}

	####################################################################
	######                     HTTP METHOD                        ######
	####################################################################

	/** @testdox The default method should be POST */
	public function test_default_method(): void {
		$form = new Form( 'test' );
		$this->assertEquals( 'POST', $form->get_method() );
	}

	/** @testdox It should be possible to set the HTTP method */
	public function test_set_method(): void {
		$form = new Form( 'test' );
		$form->method( 'GET' );
		$this->assertEquals( 'GET', $form->get_method() );
	}

	/** @testdox The method should be uppercased */
	public function test_method_uppercased(): void {
		$form = new Form( 'test' );
		$form->method( 'post' );
		$this->assertEquals( 'POST', $form->get_method() );
	}

	/** @testdox The method setter should return the instance for chaining */
	public function test_method_returns_self(): void {
		$form = new Form( 'test' );
		$this->assertSame( $form, $form->method( 'POST' ) );
	}

	####################################################################
	######                     ACTION URL                         ######
	####################################################################

	/** @testdox The default action should be empty string */
	public function test_default_action(): void {
		$form = new Form( 'test' );
		$this->assertEquals( '', $form->get_action() );
	}

	/** @testdox It should be possible to set the action URL */
	public function test_set_action(): void {
		$form = new Form( 'test' );
		$form->action( '/submit' );
		$this->assertEquals( '/submit', $form->get_action() );
	}

	/** @testdox The action setter should return the instance for chaining */
	public function test_action_returns_self(): void {
		$form = new Form( 'test' );
		$this->assertSame( $form, $form->action( '/submit' ) );
	}

	####################################################################
	######                     ENCTYPE                            ######
	####################################################################

	/** @testdox The default enctype should be null */
	public function test_default_enctype(): void {
		$form = new Form( 'test' );
		$this->assertNull( $form->get_enctype() );
	}

	/** @testdox It should be possible to set the enctype */
	public function test_set_enctype(): void {
		$form = new Form( 'test' );
		$form->enctype( 'multipart/form-data' );
		$this->assertEquals( 'multipart/form-data', $form->get_enctype() );
	}

	/** @testdox The enctype setter should return the instance for chaining */
	public function test_enctype_returns_self(): void {
		$form = new Form( 'test' );
		$this->assertSame( $form, $form->enctype( 'multipart/form-data' ) );
	}

	####################################################################
	######                     FIELDS                             ######
	####################################################################

	/** @testdox It should be possible to add pre-built element instances via fields() */
	public function test_fields_adds_elements(): void {
		$text  = Text::make( 'name' );
		$email = Email::make( 'email' );

		$form = Form::make( 'test' )->fields( $text, $email );

		$fields = $form->get_fields();
		$this->assertCount( 2, $fields );
		$this->assertSame( $text, $fields['name'] );
		$this->assertSame( $email, $fields['email'] );
	}

	/** @testdox The fields method should return the instance for chaining */
	public function test_fields_returns_self(): void {
		$form = new Form( 'test' );
		$this->assertSame( $form, $form->fields( Text::make( 'name' ) ) );
	}

	/** @testdox It should be possible to add a Nonce element as a field */
	public function test_nonce_as_field(): void {
		$nonce = Nonce::make( 'my_action', 'my_nonce' );
		$form  = Form::make( 'test' )->fields( $nonce );

		$fields = $form->get_fields();
		$this->assertCount( 1, $fields );
		$this->assertSame( $nonce, $fields['my_nonce'] );
	}

	/** @testdox It should be possible to add a Button element as a field */
	public function test_button_as_field(): void {
		$button = Button::make( 'submit' )->type( 'submit' )->text( 'Send' );
		$form   = Form::make( 'test' )->fields( $button );

		$fields = $form->get_fields();
		$this->assertCount( 1, $fields );
		$this->assertSame( $button, $fields['submit'] );
	}

	/** @testdox It should be possible to add a Raw_HTML element as a field */
	public function test_raw_html_as_field(): void {
		$raw  = Raw_HTML::make( 'intro' )->html( '<p>Intro</p>' );
		$form = Form::make( 'test' )->fields( $raw );

		$fields = $form->get_fields();
		$this->assertCount( 1, $fields );
		$this->assertSame( $raw, $fields['intro'] );
	}

	/** @testdox It should be possible to add mixed element types */
	public function test_mixed_elements(): void {
		$form = Form::make( 'test' )->fields(
			Text::make( 'name' )->label( 'Name' ),
			Email::make( 'email' )->label( 'Email' ),
			Nonce::make( 'action', 'nonce' ),
			Button::make( 'submit' )->type( 'submit' )->text( 'Send' )
		);

		$fields = $form->get_fields();
		$this->assertCount( 4, $fields );
		$this->assertArrayHasKey( 'name', $fields );
		$this->assertArrayHasKey( 'email', $fields );
		$this->assertArrayHasKey( 'nonce', $fields );
		$this->assertArrayHasKey( 'submit', $fields );
	}

	/** @testdox Field names should be tracked */
	public function test_field_names_tracked(): void {
		$form = Form::make( 'test' )->fields(
			Text::make( 'first_name' ),
			Text::make( 'last_name' )
		);

		$names = $form->get_field_names();
		$this->assertContains( 'first_name', $names );
		$this->assertContains( 'last_name', $names );
	}

	####################################################################
	######                     STYLE                              ######
	####################################################################

	/** @testdox The form should have a default style */
	public function test_default_style(): void {
		$form = new Form( 'test' );
		$this->assertInstanceOf( Default_Style::class, $form->get_style() );
	}

	/** @testdox The form should apply the form_class from style */
	public function test_form_class_applied(): void {
		$form = new Form( 'test' );
		$this->assertStringContainsString( 'pc-form', $form->get_wrapper_attribute( 'class' ) );
	}

	/** @testdox The form should have a default wrapper id */
	public function test_default_wrapper_id(): void {
		$form = new Form( 'test' );
		$this->assertEquals( 'form-test', $form->get_wrapper_attribute( 'id' ) );
	}

	####################################################################
	######                  WRAPPER ATTRIBUTES                    ######
	####################################################################

	/** @testdox It should be possible to set and get a wrapper attribute */
	public function test_wrapper_attribute(): void {
		$form = new Form( 'test' );
		$form->wrapper_attribute( 'data-foo', 'bar' );
		$this->assertEquals( 'bar', $form->get_wrapper_attribute( 'data-foo' ) );
	}

	/** @testdox It should be possible to add a wrapper class */
	public function test_add_wrapper_class(): void {
		$form = new Form( 'test' );
		$form->add_wrapper_class( 'custom-form' );
		$this->assertStringContainsString( 'custom-form', $form->get_wrapper_attribute( 'class' ) );
	}

	####################################################################
	######                  ELEMENT WRAP                          ######
	####################################################################

	/** @testdox It should be possible to set before content */
	public function test_before_content(): void {
		$form = new Form( 'test' );
		$form->before( '<div>Before</div>' );
		$this->assertEquals( '<div>Before</div>', $form->get_before() );
	}

	/** @testdox It should be possible to set after content */
	public function test_after_content(): void {
		$form = new Form( 'test' );
		$form->after( '<div>After</div>' );
		$this->assertEquals( '<div>After</div>', $form->get_after() );
	}

	####################################################################
	######                  FLUENT API                            ######
	####################################################################

	/** @testdox The full fluent API should work for building a form */
	public function test_fluent_api(): void {
		$form = Form::make( 'contact' )
			->method( 'POST' )
			->action( '/submit' )
			->enctype( 'multipart/form-data' )
			->before( '<h2>Contact</h2>' )
			->after( '<p>Footer</p>' )
			->fields(
				Text::make( 'name' )->label( 'Name' )->required( true ),
				Email::make( 'email' )->label( 'Email' ),
				Nonce::make( 'contact_action', 'contact_nonce' ),
				Button::make( 'submit' )->type( 'submit' )->text( 'Send' )
			);

		$this->assertEquals( 'contact', $form->get_name() );
		$this->assertEquals( 'POST', $form->get_method() );
		$this->assertEquals( '/submit', $form->get_action() );
		$this->assertEquals( 'multipart/form-data', $form->get_enctype() );
		$this->assertEquals( '<h2>Contact</h2>', $form->get_before() );
		$this->assertEquals( '<p>Footer</p>', $form->get_after() );
		$this->assertCount( 4, $form->get_fields() );
	}

	/** @testdox It should be possible to construct a Form with a custom style */
	public function test_custom_style(): void {
		$style = new \PinkCrab\Form_Components\Style\Default_Style();
		$form  = new Form( 'test', $style );
		$this->assertSame( $style, $form->get_style() );
	}
}
