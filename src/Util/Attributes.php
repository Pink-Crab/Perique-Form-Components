<?php

declare( strict_types=1 );

/**
 * Helper functions for working with attributes.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Form
 */

namespace PinkCrab\Form_Components\Util;

class Attributes {

	/**
	 * Combines two arrays of attributes, with the second overwriting the first
	 * and allowing the concatenation of defined indexes.
	 *
	 * @param array<string, string|int|float|null> $attributes
	 * @param array<string, string|int|float|null> $overwrites
	 * @param array<string> $concat_keys
	 * @return array<string, string|int|float|null>
	 */
	public static function combine( array $attributes, array $overwrites, array $concat_keys = array() ): array {
		foreach ( $overwrites as $key => $value ) {
			if ( array_key_exists( $key, $attributes ) && in_array( $key, $concat_keys, true ) ) {
				$attributes[ Esc::attribute( $key ) ] .= ' ' . Esc::attribute( $value );
			} else {
				$attributes[ Esc::attribute( $key ) ] = Esc::attribute( $value );
			}

			// Trim any whitespace.
			$attributes[ Esc::attribute( $key ) ] = preg_replace( '/\s+/', ' ', trim( $attributes[ Esc::attribute( $key ) ] ) );
		}
		return $attributes;
	}

	/**
	 * Parses an array of attributes into a string.
	 * Treats any index with a null value, as a flag.
	 *
	 * @param array<string, string|int|float|null> $attributes
	 * @return string
	 */
	public static function parse( array $attributes ): string {
		return join(
			' ',
			\PinkCrab\FunctionConstructors\Arrays\mapWithKey(
				function( $key, $value ): string {
					return $value === null
						? Esc::attribute( $key )
						: Esc::attribute( $key ) . '="' . Esc::html( $value ) . '"';
				}
			)( $attributes )
		);
	}
}
