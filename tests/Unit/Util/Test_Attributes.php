<?php

declare(strict_types=1);

/**
 * Unit tests for attribute helpers
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Util;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Util\Attributes;

class Test_Attributes extends WP_UnitTestCase {

	/** @testdox It should be possible to merge to 2 arrays, but have some defined index concatenated. */
	public function test_merge_with_concat(): void {
		$this->assertEquals(
			array(
				'foo' => 'a b c',
				'bar' => '2',
			),
			Attributes::combine(
				array(
					'foo' => 'a',
					'bar' => '1',
				),
				array(
					'foo' => 'b c',
					'bar' => '2',
				),
				array( 'foo' )
			)
		);
	}

	/** @testdox It should be possible to merge 2 arrays, but choose to not concatenated any indexes */
	public function test_merge_without_concat(): void {
		$this->assertEquals(
			array(
				'foo' => 'b c',
				'bar' => '2',
			),
			Attributes::combine(
				array(
					'foo' => 'a',
					'bar' => '1',
				),
				array(
					'foo' => 'b c',
					'bar' => '2',
				),
				array()
			)
		);
	}

	/** @testdox When concatinating strings, there should only be 1 space between each sub string */
	public function test_concat_with_space(): void {
		$this->assertEquals(
			array( 'foo' => 'a b c' ),
			Attributes::combine(
				array( 'foo' => ' a ' ),
				array( 'foo' => ' b c ' ),
				array( 'foo' )
			)
		);
	}

	/** @testdox It should be possible to parse html element attributes and have both keu="value" and FLAG attributes parsed. */
	public function test_parse_attributes(): void {
		$this->assertEquals(
			'foo="bar" bar="baz" flag',
			Attributes::parse(
				array(
					'foo'  => 'bar',
					'bar'  => 'baz',
					'flag' => null,
				)
			)
		);
	}
}
