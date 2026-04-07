<?php

declare(strict_types=1);

/**
 * Unit tests for the Validation trait
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element;

use WP_UnitTestCase;
use Respect\Validation\Validator;
use PinkCrab\Form_Components\Element\Field\Input\Text;

/**
 * @group unit
 * @group element
 * @group validation
 */
class Test_Validation_Trait extends WP_UnitTestCase {

	/** @testdox A field should not have a validator by default */
	public function test_no_validator_by_default(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_validator() );
		$this->assertNull( $text->get_validator() );
	}

	/** @testdox It should be possible to set a validator on a field */
	public function test_set_validator(): void {
		$text      = new Text( 'test' );
		$validator = new Validator();
		$text->validator( $validator );
		$this->assertTrue( $text->has_validator() );
		$this->assertSame( $validator, $text->get_validator() );
	}

	/** @testdox It should be possible to clear a validator by passing null */
	public function test_clear_validator(): void {
		$text      = new Text( 'test' );
		$validator = new Validator();
		$text->validator( $validator );
		$this->assertTrue( $text->has_validator() );
		$text->validator( null );
		$this->assertFalse( $text->has_validator() );
		$this->assertNull( $text->get_validator() );
	}

	/** @testdox The validator method should return the field for chaining */
	public function test_validator_returns_self(): void {
		$text   = new Text( 'test' );
		$result = $text->validator( new Validator() );
		$this->assertSame( $text, $result );
	}
}
