<?php

declare(strict_types=1);

/**
 * Unit tests for the Nonce Element
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Nonce;
use PinkCrab\Form_Components\Element\Element;

/**
 * @group unit
 * @group element
 * @group nonce
 */
class Test_Nonce extends WP_UnitTestCase {

	/** @testdox Nonce should implement the Element interface */
	public function test_implements_element(): void {
		$nonce = new Nonce( 'test_action' );
		$this->assertInstanceOf( Element::class, $nonce );
	}

	/** @testdox It should be possible to get the action */
	public function test_get_action(): void {
		$nonce = new Nonce( 'my_action' );
		$this->assertEquals( 'my_action', $nonce->get_action() );
	}

	/** @testdox It should be possible to get the name */
	public function test_get_name(): void {
		$nonce = new Nonce( 'action', 'my_nonce_name' );
		$this->assertEquals( 'my_nonce_name', $nonce->get_name() );
	}

	/** @testdox The default name should be _wpnonce */
	public function test_default_name(): void {
		$nonce = new Nonce( 'action' );
		$this->assertEquals( '_wpnonce', $nonce->get_name() );
	}

	/** @testdox A Nonce element should return a type of "nonce" */
	public function test_type(): void {
		$nonce = new Nonce( 'action' );
		$this->assertEquals( 'nonce', $nonce->get_type() );
	}

	/** @testdox It should be possible to create a Nonce using the static make method */
	public function test_static_make(): void {
		$nonce = Nonce::make( 'my_action', 'my_name' );
		$this->assertInstanceOf( Nonce::class, $nonce );
		$this->assertEquals( 'my_action', $nonce->get_action() );
		$this->assertEquals( 'my_name', $nonce->get_name() );
	}

	/** @testdox It should be possible to create a Nonce using make with default name */
	public function test_static_make_default_name(): void {
		$nonce = Nonce::make( 'my_action' );
		$this->assertEquals( '_wpnonce', $nonce->get_name() );
	}

	/** @testdox The action should be sanitized */
	public function test_action_sanitized(): void {
		$nonce = new Nonce( '<script>alert("xss")</script>' );
		$this->assertStringNotContainsString( '<script>', $nonce->get_action() );
	}

	/** @testdox The name should be sanitized */
	public function test_name_sanitized(): void {
		$nonce = new Nonce( 'action', '<script>alert("xss")</script>' );
		$this->assertStringNotContainsString( '<script>', $nonce->get_name() );
	}

	####################################################################
	######                  WRAPPER ATTRIBUTES                    ######
	####################################################################

	/** @testdox It should be possible to set and get a wrapper attribute */
	public function test_wrapper_attribute(): void {
		$nonce = new Nonce( 'action' );
		$nonce->wrapper_attribute( 'data-foo', 'bar' );
		$this->assertEquals( 'bar', $nonce->get_wrapper_attribute( 'data-foo' ) );
	}

	/** @testdox It should be possible to check if wrapper attributes exist */
	public function test_has_wrapper_attributes(): void {
		$nonce = new Nonce( 'action' );
		$this->assertFalse( $nonce->has_wrapper_attributes() );
		$nonce->wrapper_attribute( 'test', 'value' );
		$this->assertTrue( $nonce->has_wrapper_attributes() );
	}
}
