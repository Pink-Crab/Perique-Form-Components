<?php

declare(strict_types=1);

/**
 * Unit tests for the Group Element
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Group;
use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Email;
use PinkCrab\Form_Components\Style\Default_Style;

/**
 * @group unit
 * @group element
 * @group group
 */
class Test_Group extends WP_UnitTestCase {

	/** @testdox Group should implement the Element interface */
	public function test_implements_element(): void {
		$group = new Group( 'test' );
		$this->assertInstanceOf( Element::class, $group );
	}

	/** @testdox It should be possible to get the name */
	public function test_get_name(): void {
		$group = new Group( 'personal' );
		$this->assertEquals( 'personal', $group->get_name() );
	}

	/** @testdox The name should be sanitized */
	public function test_name_sanitized(): void {
		$group = new Group( 'My Group' );
		$this->assertEquals( 'my-group', $group->get_name() );
	}

	/** @testdox A Group should return a type of "group" */
	public function test_type(): void {
		$group = new Group( 'test' );
		$this->assertEquals( 'group', $group->get_type() );
	}

	/** @testdox It should be possible to create a Group using make */
	public function test_static_make(): void {
		$group = Group::make( 'test' );
		$this->assertInstanceOf( Group::class, $group );
		$this->assertEquals( 'test', $group->get_name() );
	}

	####################################################################
	######                     FIELDS                             ######
	####################################################################

	/** @testdox It should be possible to add fields via fields() */
	public function test_add_fields(): void {
		$group = Group::make( 'test' )->fields(
			Text::make( 'name' ),
			Email::make( 'email' )
		);

		$this->assertCount( 2, $group->get_fields() );
		$this->assertArrayHasKey( 'name', $group->get_fields() );
		$this->assertArrayHasKey( 'email', $group->get_fields() );
	}

	/** @testdox It should be possible to add fields via add_field() */
	public function test_add_field_by_class(): void {
		$group = new Group( 'test' );
		$group->add_field( 'name', Text::class );
		$group->add_field( 'email', Email::class );

		$this->assertCount( 2, $group->get_fields() );
	}

	/** @testdox Field names should be tracked */
	public function test_field_names_tracked(): void {
		$group = Group::make( 'test' )->fields(
			Text::make( 'first' ),
			Text::make( 'last' )
		);

		$names = $group->get_field_names();
		$this->assertContains( 'first', $names );
		$this->assertContains( 'last', $names );
	}

	####################################################################
	######                     STYLE                              ######
	####################################################################

	/** @testdox The group should have a default wrapper id */
	public function test_default_wrapper_id(): void {
		$group = new Group( 'test' );
		$this->assertEquals( 'form-group_test', $group->get_wrapper_attribute( 'id' ) );
	}

	/** @testdox The group should have style classes */
	public function test_style_classes(): void {
		$group = new Group( 'test' );
		$this->assertStringContainsString( 'pc-form__element--group', $group->get_wrapper_attribute( 'class' ) );
	}

	####################################################################
	######                  ELEMENT WRAP                          ######
	####################################################################

	/** @testdox It should be possible to set before content */
	public function test_before_content(): void {
		$group = new Group( 'test' );
		$group->before( '<h3>Section</h3>' );
		$this->assertEquals( '<h3>Section</h3>', $group->get_before() );
	}

	/** @testdox It should be possible to set after content */
	public function test_after_content(): void {
		$group = new Group( 'test' );
		$group->after( '<hr>' );
		$this->assertEquals( '<hr>', $group->get_after() );
	}

	####################################################################
	######                  WRAPPER ATTRIBUTES                    ######
	####################################################################

	/** @testdox It should be possible to add wrapper data */
	public function test_wrapper_data(): void {
		$group = new Group( 'test' );
		$group->wrapper_data( 'section', 'info' );
		$this->assertEquals( 'info', $group->get_wrapper_attribute( 'data-section' ) );
	}

	/** @testdox It should be possible to add a wrapper class */
	public function test_add_wrapper_class(): void {
		$group = new Group( 'test' );
		$group->add_wrapper_class( 'custom' );
		$this->assertStringContainsString( 'custom', $group->get_wrapper_attribute( 'class' ) );
	}

	/** @testdox It should be possible to construct a Group with a custom style */
	public function test_custom_style(): void {
		$style = new \PinkCrab\Form_Components\Style\Default_Style();
		$group = new Group( 'test', $style );
		$this->assertSame( $style, $group->get_style() );
	}
}
