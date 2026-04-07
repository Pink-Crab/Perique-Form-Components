<?php

declare(strict_types=1);

/**
 * Unit tests for the Raw_HTML Element
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Raw_HTML;
use PinkCrab\Form_Components\Element\Element;

/**
 * @group unit
 * @group element
 * @group raw_html
 */
class Test_Raw_HTML extends WP_UnitTestCase {

	/** @testdox Raw_HTML should implement the Element interface */
	public function test_implements_element(): void {
		$raw = new Raw_HTML( 'test' );
		$this->assertInstanceOf( Element::class, $raw );
	}

	/** @testdox It should be possible to get the name of the element */
	public function test_get_name(): void {
		$raw = new Raw_HTML( 'test' );
		$this->assertEquals( 'test', $raw->get_name() );
	}

	/** @testdox The name should be sanitized */
	public function test_name_sanitized(): void {
		$raw = new Raw_HTML( '<p>test</p>' );
		$this->assertEquals( 'test', $raw->get_name() );
	}

	/** @testdox A Raw_HTML element should return a type of "raw_html" */
	public function test_type(): void {
		$raw = new Raw_HTML( 'test' );
		$this->assertEquals( 'raw_html', $raw->get_type() );
	}

	/** @testdox It should be possible to set HTML via the constructor */
	public function test_set_html_via_constructor(): void {
		$raw = new Raw_HTML( 'test', '<p>Hello</p>' );
		$this->assertEquals( '<p>Hello</p>', $raw->get_html() );
	}

	/** @testdox It should be possible to set HTML via the fluent method */
	public function test_set_html_fluent(): void {
		$raw = new Raw_HTML( 'test' );
		$raw->html( '<div>Content</div>' );
		$this->assertEquals( '<div>Content</div>', $raw->get_html() );
	}

	/** @testdox The html method should return the instance for chaining */
	public function test_html_returns_self(): void {
		$raw = new Raw_HTML( 'test' );
		$this->assertSame( $raw, $raw->html( '<p>test</p>' ) );
	}

	/** @testdox It should be possible to check if HTML has been set */
	public function test_has_html(): void {
		$raw = new Raw_HTML( 'test' );
		$this->assertFalse( $raw->has_html() );

		$raw->html( '<p>test</p>' );
		$this->assertTrue( $raw->has_html() );
	}

	/** @testdox By default the HTML should be an empty string */
	public function test_default_html_empty(): void {
		$raw = new Raw_HTML( 'test' );
		$this->assertEquals( '', $raw->get_html() );
	}

	/** @testdox It should be possible to create a Raw_HTML using the static make method */
	public function test_static_make(): void {
		$raw = Raw_HTML::make( 'test', '<p>Hello</p>' );
		$this->assertInstanceOf( Raw_HTML::class, $raw );
		$this->assertEquals( 'test', $raw->get_name() );
		$this->assertEquals( '<p>Hello</p>', $raw->get_html() );
	}

	/** @testdox It should be possible to create a Raw_HTML using make without HTML */
	public function test_static_make_without_html(): void {
		$raw = Raw_HTML::make( 'test' );
		$this->assertEquals( '', $raw->get_html() );
	}

	####################################################################
	######                  WRAPPER ATTRIBUTES                    ######
	####################################################################

	/** @testdox It should be possible to set and get a wrapper attribute */
	public function test_wrapper_attribute(): void {
		$raw = new Raw_HTML( 'test' );
		$raw->wrapper_attribute( 'data-foo', 'bar' );
		$this->assertEquals( 'bar', $raw->get_wrapper_attribute( 'data-foo' ) );
	}

	/** @testdox It should be possible to check if wrapper attributes exist */
	public function test_has_wrapper_attributes(): void {
		$raw = new Raw_HTML( 'test' );
		$this->assertFalse( $raw->has_wrapper_attributes() );
		$raw->wrapper_attribute( 'test', 'value' );
		$this->assertTrue( $raw->has_wrapper_attributes() );
	}

	/** @testdox It should be possible to add a wrapper class */
	public function test_add_wrapper_class(): void {
		$raw = new Raw_HTML( 'test' );
		$raw->add_wrapper_class( 'my-class' );
		$this->assertStringContainsString( 'my-class', $raw->get_wrapper_attribute( 'class' ) );
	}

	/** @testdox It should be possible to set the wrapper id */
	public function test_wrapper_id(): void {
		$raw = new Raw_HTML( 'test' );
		$raw->wrapper_id( 'my-id' );
		$this->assertEquals( 'my-id', $raw->get_wrapper_attribute( 'id' ) );
	}

	/** @testdox It should be possible to get all wrapper attributes */
	public function test_get_wrapper_attributes(): void {
		$raw = new Raw_HTML( 'test' );
		$raw->wrapper_attribute( 'data-foo', 'bar' );
		$attrs = $raw->get_wrapper_attributes();
		$this->assertIsArray( $attrs );
		$this->assertArrayHasKey( 'data-foo', $attrs );
	}

	/** @testdox Removing a wrapper class when none exist should not throw */
	public function test_remove_wrapper_class_when_no_class(): void {
		$raw = new Raw_HTML( 'test' );
		$this->assertSame( $raw, $raw->remove_wrapper_class( 'nope' ) );
	}
}
