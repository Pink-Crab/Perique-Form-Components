<?php

declare(strict_types=1);

/**
 * Unit tests for the Fields trait (tested through Form)
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use Respect\Validation\Validator;
use PinkCrab\Form_Components\Element\Form;
use PinkCrab\Form_Components\Element\Group;
use PinkCrab\Form_Components\Element\Nonce;
use PinkCrab\Form_Components\Element\Raw_HTML;
use PinkCrab\Form_Components\Element\Fieldset;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Email;
use PinkCrab\Form_Components\Element\Field\Input\Number;
use PinkCrab\Form_Components\Element\Field\Select;

/**
 * @group unit
 * @group element
 * @group fields
 */
class Test_Fields_Trait extends WP_UnitTestCase {

	####################################################################
	######                     ADD FIELD                          ######
	####################################################################

	/** @testdox It should be possible to add a field using the add_field method */
	public function test_add_field(): void {
		$form = new Form( 'test_form' );
		$form->add_field( 'username', Text::class );
		$this->assertTrue( $form->has_field( 'username' ) );
		$field = $form->get_field( 'username' );
		$this->assertInstanceOf( Text::class, $field );
	}

	/** @testdox It should be possible to add a field with a config callback */
	public function test_add_field_with_config(): void {
		$form = new Form( 'test_form' );
		$form->add_field( 'username', Text::class, function( $field ) {
			$field->label( 'Username' );
			$field->required();
			return $field;
		} );
		$field = $form->get_field( 'username' );
		$this->assertInstanceOf( Text::class, $field );
		$this->assertEquals( 'Username', $field->get_label() );
		$this->assertTrue( $field->is_required() );
	}

	####################################################################
	######                       FIELDS                          ######
	####################################################################

	/** @testdox It should be possible to add multiple fields using the fields method */
	public function test_fields(): void {
		$form = new Form( 'test_form' );
		$form->fields(
			new Text( 'name' ),
			new Email( 'email' ),
			new Number( 'age' )
		);
		$this->assertCount( 3, $form->get_fields() );
		$this->assertTrue( $form->has_field( 'name' ) );
		$this->assertTrue( $form->has_field( 'email' ) );
		$this->assertTrue( $form->has_field( 'age' ) );
	}

	/** @testdox It should be possible to get all field names */
	public function test_get_field_names(): void {
		$form = new Form( 'test_form' );
		$form->fields(
			new Text( 'first_name' ),
			new Text( 'last_name' )
		);
		$names = $form->get_field_names();
		$this->assertContains( 'first_name', $names );
		$this->assertContains( 'last_name', $names );
	}

	/** @testdox Getting a non-existent field should return null */
	public function test_get_nonexistent_field(): void {
		$form = new Form( 'test_form' );
		$this->assertNull( $form->get_field( 'nonexistent' ) );
	}

	/** @testdox has_field should return false for non-existent fields */
	public function test_has_field_false(): void {
		$form = new Form( 'test_form' );
		$this->assertFalse( $form->has_field( 'nonexistent' ) );
	}

	####################################################################
	######                   NESTED FIELDS                       ######
	####################################################################

	/** @testdox It should be possible to get nested fields from a Group */
	public function test_nested_fields_from_group(): void {
		$form  = new Form( 'test_form' );
		$group = new Group( 'address' );
		$group->fields(
			new Text( 'street' ),
			new Text( 'city' )
		);
		$form->fields( $group );

		// The form should be able to find the nested fields
		$this->assertTrue( $form->has_field( 'street' ) );
		$this->assertTrue( $form->has_field( 'city' ) );
	}

	/** @testdox get_nested_fields should return all fields flattened */
	public function test_get_nested_fields(): void {
		$form  = new Form( 'test_form' );
		$group = new Group( 'group1' );
		$group->fields(
			new Text( 'nested_field' )
		);
		$form->fields(
			new Text( 'top_field' ),
			$group
		);
		$nested = $form->get_nested_fields();
		$this->assertArrayHasKey( 'top_field', $nested );
		$this->assertArrayHasKey( 'nested_field', $nested );
	}

	####################################################################
	######                 VALIDATION RULES                      ######
	####################################################################

	/** @testdox It should be possible to add validation rules manually */
	public function test_add_validation_rule(): void {
		$form = new Form( 'test_form' );
		$form->add_validation_rule( 'name', new Validator() );
		$rules = $form->get_validation_rules();
		$this->assertArrayHasKey( 'name', $rules );
	}

	/** @testdox Fields with validators should auto-register validation rules */
	public function test_auto_register_validation_rules(): void {
		$form = new Form( 'test_form' );
		$text = new Text( 'username' );
		$text->validator( new Validator() );
		$form->fields( $text );
		$rules = $form->get_validation_rules();
		$this->assertArrayHasKey( 'username', $rules );
	}

	/** @testdox Fields without validators should not add validation rules */
	public function test_no_auto_register_without_validator(): void {
		$form = new Form( 'test_form' );
		$form->fields( new Text( 'plain_field' ) );
		$rules = $form->get_validation_rules();
		$this->assertArrayNotHasKey( 'plain_field', $rules );
	}

	####################################################################
	######                  STYLE CASCADE                        ######
	####################################################################

	/** @testdox Parent form style should cascade to child fields without explicit style */
	public function test_style_cascade(): void {
		$form = new Form( 'test_form' );
		$text = new Text( 'name' );
		$form->fields( $text );

		// The child should inherit the parent's style
		$field = $form->get_field( 'name' );
		$this->assertNotNull( $field );
	}
}
