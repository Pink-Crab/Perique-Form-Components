<?php

declare(strict_types=1);

/**
 * Unit tests for component factory
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Component;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Component\Component_Factory;
use PinkCrab\Form_Components\Component\Field\Input_Component;

class Test_Component_Factory extends WP_UnitTestCase {

	/** @testdox It should be possible to get an instance of the component factory using a static constructor */
	public function test_get_instance(): void {
		$this->assertInstanceOf( Component_Factory::class, Component_Factory::instance() );
	}

	/** @testdox It should be possible to create an Input component from an Input field. */
	public function test_create_input(): void {
		$field  = new Text( 'test' );
		
        // Using from_field() method.
		$this->assertInstanceOf( Input_Component::class, Component_Factory::instance()->from_field( $field ) );

        // Using from_element() method.
        $this->assertInstanceOf( Input_Component::class, Component_Factory::instance()->from_element( $field ) );
	}
}
