<?php

declare(strict_types=1);

/**
 * Unit tests for the Radio_Group Field
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Group;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Field\Group\Radio_Group;

/**
 * @group unit
 * @group element
 * @group group
 */
class Test_Radio_Group extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;
	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Description_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Radio_Group::class;
	}

	/** @testdox A Radio_Group should return a type of "radio_group" */
	public function test_type(): void {
		$group = new Radio_Group( 'test' );
		$this->assertEquals( 'radio_group', $group->get_type() );
	}

	/** @testdox It should be possible to create a Radio_Group using the static make method */
	public function test_static_make(): void {
		$group = Radio_Group::make( 'test' );
		$this->assertInstanceOf( Radio_Group::class, $group );
		$this->assertEquals( 'test', $group->get_name() );
	}

	####################################################################
	######                        OPTIONS                         ######
	####################################################################

	/** @testdox It should be possible to set options on the radio group */
	public function test_set_options(): void {
		$group = new Radio_Group( 'test' );
		$group->options( array( 'a' => 'Option A', 'b' => 'Option B' ) );
		$this->assertCount( 2, $group->get_options() );
	}

	/** @testdox The options method should return the field instance for chaining */
	public function test_options_returns_self(): void {
		$group = new Radio_Group( 'test' );
		$this->assertSame( $group, $group->options( array( 'a' => 'A' ) ) );
	}

	####################################################################
	######                       SELECTED                         ######
	####################################################################

	/** @testdox It should be possible to set the selected value */
	public function test_set_selected(): void {
		$group = new Radio_Group( 'test' );
		$group->selected( 'a' );
		$this->assertEquals( 'a', $group->get_selected() );
	}

	/** @testdox It should be possible to check if a specific value is selected */
	public function test_is_selected(): void {
		$group = new Radio_Group( 'test' );
		$group->selected( 'a' );
		$this->assertTrue( $group->is_selected( 'a' ) );
		$this->assertFalse( $group->is_selected( 'b' ) );
	}

	/** @testdox It should be possible to check if any value is selected */
	public function test_has_selected(): void {
		$group = new Radio_Group( 'test' );
		$this->assertFalse( $group->has_selected() );
		$group->selected( 'a' );
		$this->assertTrue( $group->has_selected() );
	}

	/** @testdox By default no value should be selected */
	public function test_default_selected_null(): void {
		$group = new Radio_Group( 'test' );
		$this->assertNull( $group->get_selected() );
	}

	/** @testdox The selected method should return the field instance for chaining */
	public function test_selected_returns_self(): void {
		$group = new Radio_Group( 'test' );
		$this->assertSame( $group, $group->selected( 'a' ) );
	}

	/** @testdox It should be possible to set existing value via set_existing */
	public function test_set_existing(): void {
		$group = new Radio_Group( 'test' );
		$group->set_existing( 'b' );
		$this->assertEquals( 'b', $group->get_selected() );
	}

	/** @testdox set_existing should cast values to string */
	public function test_set_existing_casts_to_string(): void {
		$group = new Radio_Group( 'test' );
		$group->set_existing( 123 );
		$this->assertSame( '123', $group->get_selected() );
	}

	####################################################################
	######                    SHARED ATTRIBUTES                   ######
	####################################################################

	/**
	 * The methods that should be defined by the traits to represent shared attributes.
	 *
	 * @return array<string, array<string>>
	 */
	public function attribute_methods(): array {
		return array(
			'label'    => array( 'label' ),
			'options'  => array( 'options' ),
			'disabled' => array( 'disabled' ),
			'required' => array( 'required' ),
		);
	}

	/**
	 * @testdox This field has all attributes as defined by the shared traits
	 * @dataProvider attribute_methods
	 */
	public function test_has_attributes( string $method ): void {
		$group = new Radio_Group( 'test' );
		$this->assertTrue( method_exists( $group, $method ) );
	}
}
