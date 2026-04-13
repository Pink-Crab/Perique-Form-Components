<?php

declare(strict_types=1);

/**
 * Unit tests for the Select Field
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field\Select;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Label, Single_Value, Options, Disabled, Required, Multiple, Autocomplete, Size};

/**
 * @group unit
 * @group element
 * @group select
 */
class Test_Select extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;
	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Description_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Select::class;
	}

	/** @testdox A Select should return a type of "select" */
	public function test_type(): void {
		$select = new Select( 'test' );
		$this->assertEquals( 'select', $select->get_type() );
	}

	/** @testdox By default the select field should sanitize values using the TEXT sanitizer. */
	public function test_default_sanitizer(): void {
		$select = new Select( 'test' );
		$this->assertEquals( Sanitize::TEXT, $select->get_sanitizer() );
	}

	/** @testdox A Select field should allow a single value to be set */
	public function test_uses_single_value(): void {
		$select = new Select( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $select ) );
	}

	/** @testdox It should be possible to set an existing value on the select */
	public function test_set_existing(): void {
		$select = new Select( 'test' );
		$select->set_existing( 'option_a' );
		$this->assertEquals( 'option_a', $select->get_value() );
	}

	/** @testdox It should be possible to create a Select using the static make method */
	public function test_static_make(): void {
		$select = Select::make( 'test' );
		$this->assertInstanceOf( Select::class, $select );
		$this->assertEquals( 'test', $select->get_name() );
	}

	####################################################################
	######                        OPTIONS                         ######
	####################################################################

	/** @testdox It should be possible to set options on the select */
	public function test_set_options(): void {
		$select = new Select( 'test' );
		$select->options( array( 'a' => 'Option A', 'b' => 'Option B' ) );
		$this->assertCount( 2, $select->get_options() );
		$this->assertEquals( 'Option A', $select->get_options()['a'] );
		$this->assertEquals( 'Option B', $select->get_options()['b'] );
	}

	/** @testdox By default options should be an empty array */
	public function test_default_options_empty(): void {
		$select = new Select( 'test' );
		$this->assertEmpty( $select->get_options() );
	}

	/** @testdox The options method should return the field instance for chaining */
	public function test_options_returns_self(): void {
		$select = new Select( 'test' );
		$this->assertSame( $select, $select->options( array( 'a' => 'A' ) ) );
	}

	####################################################################
	######                       OPTGROUPS                        ######
	####################################################################

	/** @testdox It should be possible to add an optgroup to the select */
	public function test_add_optgroup(): void {
		$select = new Select( 'test' );
		$select->optgroup( 'Group 1', array( 'a' => 'Option A', 'b' => 'Option B' ) );
		$this->assertTrue( $select->has_optgroups() );
		$this->assertCount( 1, $select->get_optgroups() );
		$this->assertArrayHasKey( 'Group 1', $select->get_optgroups() );
	}

	/** @testdox It should be possible to add multiple optgroups */
	public function test_add_multiple_optgroups(): void {
		$select = new Select( 'test' );
		$select->optgroup( 'Group 1', array( 'a' => 'Option A' ) );
		$select->optgroup( 'Group 2', array( 'b' => 'Option B' ) );
		$this->assertCount( 2, $select->get_optgroups() );
	}

	/** @testdox By default optgroups should be empty */
	public function test_default_optgroups_empty(): void {
		$select = new Select( 'test' );
		$this->assertFalse( $select->has_optgroups() );
		$this->assertEmpty( $select->get_optgroups() );
	}

	/** @testdox The optgroup method should return the field instance for chaining */
	public function test_optgroup_returns_self(): void {
		$select = new Select( 'test' );
		$this->assertSame( $select, $select->optgroup( 'Group', array( 'a' => 'A' ) ) );
	}

	/** @testdox An optgroup should contain the correct options */
	public function test_optgroup_contains_options(): void {
		$select = new Select( 'test' );
		$select->optgroup( 'Fruits', array( 'apple' => 'Apple', 'banana' => 'Banana' ) );
		$group = $select->get_optgroups()['Fruits'];
		$this->assertEquals( 'Apple', $group['apple'] );
		$this->assertEquals( 'Banana', $group['banana'] );
	}

	/** @testdox It should be possible to combine flat options with optgroups */
	public function test_options_and_optgroups_combined(): void {
		$select = new Select( 'test' );
		$select->options( array( '' => 'Select one...' ) );
		$select->optgroup( 'Fruits', array( 'apple' => 'Apple' ) );
		$select->optgroup( 'Vegetables', array( 'carrot' => 'Carrot' ) );

		$this->assertCount( 1, $select->get_options() );
		$this->assertCount( 2, $select->get_optgroups() );
	}

	####################################################################
	######                       MULTIPLE                         ######
	####################################################################

	/** @testdox It should be possible to enable multiple selection */
	public function test_multiple(): void {
		$select = new Select( 'test' );
		$select->multiple();
		$this->assertTrue( $select->is_multiple() );
	}

	/** @testdox By default multiple should be disabled */
	public function test_default_not_multiple(): void {
		$select = new Select( 'test' );
		$this->assertFalse( $select->is_multiple() );
	}

	/** @testdox It should be possible to disable multiple selection after enabling it */
	public function test_disable_multiple(): void {
		$select = new Select( 'test' );
		$select->multiple( true );
		$this->assertTrue( $select->is_multiple() );
		$select->multiple( false );
		$this->assertFalse( $select->is_multiple() );
	}

	####################################################################
	######                  MULTIPLE SELECTION                    ######
	####################################################################

	/** @testdox It should be possible to set multiple existing values when multiple is enabled */
	public function test_set_existing_with_array(): void {
		$select = new Select( 'test' );
		$select->multiple( true );
		$select->options( array( 'a' => 'A', 'b' => 'B', 'c' => 'C' ) );
		$select->set_existing( array( 'a', 'c' ) );

		$this->assertIsArray( $select->get_value() );
		$this->assertCount( 2, $select->get_value() );
		$this->assertContains( 'a', $select->get_value() );
		$this->assertContains( 'c', $select->get_value() );
	}

	/** @testdox is_selected should return true for values in the array when multiple */
	public function test_is_selected_multiple(): void {
		$select = new Select( 'test' );
		$select->multiple( true );
		$select->options( array( 'a' => 'A', 'b' => 'B', 'c' => 'C' ) );
		$select->set_existing( array( 'a', 'c' ) );

		$this->assertTrue( $select->is_selected( 'a' ) );
		$this->assertFalse( $select->is_selected( 'b' ) );
		$this->assertTrue( $select->is_selected( 'c' ) );
	}

	/** @testdox is_selected should work with single values */
	public function test_is_selected_single(): void {
		$select = new Select( 'test' );
		$select->options( array( 'a' => 'A', 'b' => 'B' ) );
		$select->set_existing( 'b' );

		$this->assertFalse( $select->is_selected( 'a' ) );
		$this->assertTrue( $select->is_selected( 'b' ) );
	}

	/** @testdox is_selected should return false when no value is set */
	public function test_is_selected_no_value(): void {
		$select = new Select( 'test' );
		$select->options( array( 'a' => 'A' ) );

		$this->assertFalse( $select->is_selected( 'a' ) );
	}

	/** @testdox set_existing with array should sanitize each value */
	public function test_set_existing_array_sanitizes(): void {
		$select = new Select( 'test' );
		$select->multiple( true );
		$select->set_existing( array( '<script>a</script>', 'b' ) );

		$values = $select->get_value();
		$this->assertIsArray( $values );
		$this->assertNotContains( '<script>a</script>', $values );
	}

	/** @testdox set_existing with single value should still work when multiple is enabled */
	public function test_set_existing_single_when_multiple(): void {
		$select = new Select( 'test' );
		$select->multiple( true );
		$select->set_existing( 'a' );

		$this->assertEquals( 'a', $select->get_value() );
		$this->assertTrue( $select->is_selected( 'a' ) );
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
			'autocomplete' => array( 'autocomplete' ),
			'required'     => array( 'required' ),
			'disabled'     => array( 'disabled' ),
			'multiple'     => array( 'multiple' ),
			'size'         => array( 'size' ),
			'label'        => array( 'label' ),
			'options'      => array( 'options' ),
		);
	}

	/**
	 * @testdox This field has all attributes as defined by the shared traits
	 * @dataProvider attribute_methods
	 */
	public function test_has_attributes( string $method ): void {
		$select = new Select( 'test' );
		$this->assertTrue( method_exists( $select, $method ) );
	}
}
