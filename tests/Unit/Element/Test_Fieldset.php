<?php

declare(strict_types=1);

/**
 * Unit tests for the Fieldset Element
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Fieldset;
use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Email;
use PinkCrab\Form_Components\Style\Default_Style;

/**
 * @group unit
 * @group element
 * @group fieldset
 */
class Test_Fieldset extends WP_UnitTestCase {

	/** @testdox Fieldset should implement the Element interface */
	public function test_implements_element(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertInstanceOf( Element::class, $fieldset );
	}

	/** @testdox It should be possible to get the name */
	public function test_get_name(): void {
		$fieldset = new Fieldset( 'personal' );
		$this->assertEquals( 'personal', $fieldset->get_name() );
	}

	/** @testdox The name should be sanitized */
	public function test_name_sanitized(): void {
		$fieldset = new Fieldset( 'My Fieldset' );
		$this->assertEquals( 'my-fieldset', $fieldset->get_name() );
	}

	/** @testdox A Fieldset should return a type of "fieldset" */
	public function test_type(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertEquals( 'fieldset', $fieldset->get_type() );
	}

	/** @testdox It should be possible to create a Fieldset using the static make method */
	public function test_static_make(): void {
		$fieldset = Fieldset::make( 'test' );
		$this->assertInstanceOf( Fieldset::class, $fieldset );
		$this->assertEquals( 'test', $fieldset->get_name() );
	}

	####################################################################
	######                     LEGEND                             ######
	####################################################################

	/** @testdox The default legend should be null */
	public function test_default_legend(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertNull( $fieldset->get_legend() );
	}

	/** @testdox It should be possible to set the legend text */
	public function test_set_legend(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->legend( 'Personal Info' );
		$this->assertEquals( 'Personal Info', $fieldset->get_legend() );
	}

	/** @testdox It should be possible to check if a legend is set */
	public function test_has_legend(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertFalse( $fieldset->has_legend() );
		$fieldset->legend( 'Test' );
		$this->assertTrue( $fieldset->has_legend() );
	}

	/** @testdox The legend setter should return the instance for chaining */
	public function test_legend_returns_self(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertSame( $fieldset, $fieldset->legend( 'Test' ) );
	}

	####################################################################
	######                     DISABLED                           ######
	####################################################################

	/** @testdox It should be possible to disable a fieldset */
	public function test_disabled(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->disabled( true );
		$this->assertTrue( $fieldset->is_disabled() );
	}

	/** @testdox By default the fieldset should not be disabled */
	public function test_default_not_disabled(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertFalse( $fieldset->is_disabled() );
	}

	####################################################################
	######                     FIELDS                             ######
	####################################################################

	/** @testdox It should be possible to add fields */
	public function test_add_fields(): void {
		$fieldset = Fieldset::make( 'test' )->fields(
			Text::make( 'name' ),
			Email::make( 'email' )
		);

		$this->assertCount( 2, $fieldset->get_fields() );
		$this->assertArrayHasKey( 'name', $fieldset->get_fields() );
		$this->assertArrayHasKey( 'email', $fieldset->get_fields() );
	}

	####################################################################
	######                     STYLE                              ######
	####################################################################

	/** @testdox The fieldset should have a default style */
	public function test_default_style(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertInstanceOf( Default_Style::class, $fieldset->get_style() );
	}

	/** @testdox The fieldset should have style classes on wrapper */
	public function test_style_classes(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertStringContainsString( 'pc-form__element--fieldset', $fieldset->get_wrapper_attribute( 'class' ) );
	}

	/** @testdox The fieldset should have a default wrapper id */
	public function test_default_wrapper_id(): void {
		$fieldset = new Fieldset( 'test' );
		$this->assertEquals( 'fieldset-test', $fieldset->get_wrapper_attribute( 'id' ) );
	}

	####################################################################
	######                  ELEMENT WRAP                          ######
	####################################################################

	/** @testdox It should be possible to set before content */
	public function test_before_content(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->before( '<p>Before</p>' );
		$this->assertEquals( '<p>Before</p>', $fieldset->get_before() );
	}

	/** @testdox It should be possible to set after content */
	public function test_after_content(): void {
		$fieldset = new Fieldset( 'test' );
		$fieldset->after( '<p>After</p>' );
		$this->assertEquals( '<p>After</p>', $fieldset->get_after() );
	}

	####################################################################
	######                  FLUENT API                            ######
	####################################################################

	/** @testdox The full fluent API should work */
	public function test_fluent_api(): void {
		$fieldset = Fieldset::make( 'personal' )
			->legend( 'Personal Details' )
			->disabled( true )
			->before( '<p>Fill in</p>' )
			->fields(
				Text::make( 'name' )->label( 'Name' ),
				Email::make( 'email' )->label( 'Email' )
			);

		$this->assertEquals( 'personal', $fieldset->get_name() );
		$this->assertEquals( 'Personal Details', $fieldset->get_legend() );
		$this->assertTrue( $fieldset->is_disabled() );
		$this->assertCount( 2, $fieldset->get_fields() );
	}
}
