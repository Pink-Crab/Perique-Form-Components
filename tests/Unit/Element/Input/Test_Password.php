<?php

declare(strict_types=1);

/**
 * Unit tests for the Password Input
 * Extends Abstract_Input
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Input;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Field\Input\Password;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Single_Value,Autocomplete, Pattern, Datalist, Placeholder, Disabled, Read_Only,Required, Length};

/**
 * @group unit
 * @group element
 * @group input
 */
class Test_Password extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Password::class;
	}
    
    /** @testdox A Password input should return an input type of "TEXT" */
	public function test_type(): void {
		$password = new Password( 'test' );
		$this->assertEquals( 'password', $password->get_input_type() );
	}

	/** @testdox A Password input should return a type of password_input */
	public function test_element_type(): void {
		$password = new Password( 'test' );
		$this->assertEquals( 'password_input', $password->get_type() );
	}

	/** @testdox By default the password field should sanitize values as strings. */
	public function test_default_sanitizer(): void {
		$password = new Password( 'test' );
		$this->assertNull( $password->get_sanitizer() );
	}

	/** @testdox A Password field should allow a single value to be set */
	public function test_uses_single_value(): void {
		$password = new Password( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $password ) );
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
            'maxlength' => array( 'maxlength' ),
            'minlength' => array( 'minlength' ),
            'pattern' => array( 'pattern' ),
            'placeholder' => array( 'placeholder' ),
            'readonly' => array( 'readonly' ),
            'required' => array( 'required' ),
            'size' => array( 'size' ),
        );
    }

    /** 
     * @testdox This input field has all attributes as defined by the shared traits
     * @dataProvider attribute_methods
     */
    public function test_has_attributes( string $method ): void {
        $password = new Password( 'test' );
        $this->assertTrue( method_exists( $password, $method ) );
    }
}
