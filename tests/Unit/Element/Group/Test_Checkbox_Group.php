<?php

declare(strict_types=1);

/**
 * Unit tests for the Checkbox_Group Field
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Group;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Field\Group\Checkbox_Group;

/**
 * @group unit
 * @group element
 * @group group
 */
class Test_Checkbox_Group extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;
	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Description_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Checkbox_Group::class;
	}

	/** @testdox A Checkbox_Group should return a type of "checkbox_group" */
	public function test_type(): void {
		$group = new Checkbox_Group( 'test' );
		$this->assertEquals( 'checkbox_group', $group->get_type() );
	}

	/** @testdox It should be possible to create a Checkbox_Group using the static make method */
	public function test_static_make(): void {
		$group = Checkbox_Group::make( 'test' );
		$this->assertInstanceOf( Checkbox_Group::class, $group );
		$this->assertEquals( 'test', $group->get_name() );
	}

	####################################################################
	######                        OPTIONS                         ######
	####################################################################

	/** @testdox It should be possible to set options on the checkbox group */
	public function test_set_options(): void {
		$group = new Checkbox_Group( 'test' );
		$group->options( array( 'a' => 'Option A', 'b' => 'Option B' ) );
		$this->assertCount( 2, $group->get_options() );
	}

	/** @testdox The options method should return the field instance for chaining */
	public function test_options_returns_self(): void {
		$group = new Checkbox_Group( 'test' );
		$this->assertSame( $group, $group->options( array( 'a' => 'A' ) ) );
	}

	####################################################################
	######                       SELECTED                         ######
	####################################################################

	/** @testdox It should be possible to set selected values */
	public function test_set_selected(): void {
		$group = new Checkbox_Group( 'test' );
		$group->selected( array( 'a', 'c' ) );
		$this->assertEquals( array( 'a', 'c' ), $group->get_selected() );
	}

	/** @testdox It should be possible to check if a specific value is selected */
	public function test_is_selected(): void {
		$group = new Checkbox_Group( 'test' );
		$group->selected( array( 'a', 'c' ) );
		$this->assertTrue( $group->is_selected( 'a' ) );
		$this->assertFalse( $group->is_selected( 'b' ) );
		$this->assertTrue( $group->is_selected( 'c' ) );
	}

	/** @testdox It should be possible to check if any values are selected */
	public function test_has_selected(): void {
		$group = new Checkbox_Group( 'test' );
		$this->assertFalse( $group->has_selected() );
		$group->selected( array( 'a' ) );
		$this->assertTrue( $group->has_selected() );
	}

	/** @testdox By default no values should be selected */
	public function test_default_selected_empty(): void {
		$group = new Checkbox_Group( 'test' );
		$this->assertEmpty( $group->get_selected() );
	}

	/** @testdox The selected method should return the field instance for chaining */
	public function test_selected_returns_self(): void {
		$group = new Checkbox_Group( 'test' );
		$this->assertSame( $group, $group->selected( array( 'a' ) ) );
	}

	/** @testdox It should be possible to set existing values via set_existing */
	public function test_set_existing(): void {
		$group = new Checkbox_Group( 'test' );
		$group->set_existing( array( 'a', 'b' ) );
		$this->assertEquals( array( 'a', 'b' ), $group->get_selected() );
	}

	/** @testdox Selected values should be cast to strings */
	public function test_selected_values_cast_to_string(): void {
		$group = new Checkbox_Group( 'test' );
		$group->selected( array( '1', '2' ) );
		$this->assertSame( array( '1', '2' ), $group->get_selected() );
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
		);
	}

	/**
	 * @testdox This field has all attributes as defined by the shared traits
	 * @dataProvider attribute_methods
	 */
	public function test_has_attributes( string $method ): void {
		$group = new Checkbox_Group( 'test' );
		$this->assertTrue( method_exists( $group, $method ) );
	}
}
