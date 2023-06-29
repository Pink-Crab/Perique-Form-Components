<?php

declare( strict_types=1 );

/**
 * Sanitization functions
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

use DateTimeImmutable;
use PinkCrab\Form_Components\Util\Esc;

class Sanitize {

	public const TEXT      = __NAMESPACE__ . '\Sanitize::text';
	public const TEXTAREA  = __NAMESPACE__ . '\Sanitize::textarea';
	public const URL       = __NAMESPACE__ . '\Sanitize::url';
	public const HEX_COLOR = __NAMESPACE__ . '\Sanitize::HEX_COLOR';
	public const EMAIL     = __NAMESPACE__ . '\Sanitize::email';
	public const NUMBER    = __NAMESPACE__ . '\Sanitize::number';
	public const NOOP      = __NAMESPACE__ . '\Sanitize::noop';

	/**
	 * Sanitizes a string like text input.
	 *
	 * @param string|int|float|Stringable $value
	 * @return string
	 */
	public static function text( $value ): string {
		return (string) sanitize_text_field( (string) $value );
	}

	/**
	 * Sanitizes a string like textarea.
	 *
	 * @param string|int|float|Stringable $value
	 * @return string
	 */
	public static function textarea( $value ): string {
		return (string) sanitize_textarea_field( (string) $value );
	}

	/**
	 * Sanitizes a string like a URL.
	 *
	 * @param string|int|float|Stringable $value
	 * @return string
	 */
	public static function url( $value ): string {
		return (string) esc_url_raw( (string) $value );
	}

	/**
	 * Sanitizes a string as hex colour.
	 *
	 * @param string|int|float|Stringable $value
	 * @return string
	 */
	public static function hex_color( $value ): string {
		return (string) sanitize_hex_color( (string) $value );
	}

	/**
	 * Sanitizes a string as a email.
	 *
	 * @param string|int|float|Stringable $value
	 * @return string
	 */
	public static function email( $value ): string {
		return (string) sanitize_email( (string) $value );
	}

	/**
	 * Sanitizes a value as a number.
	 *
	 * @param string|int|float|Stringable $value
	 * @return int|float
	 */
	public static function number( $value ) {
		// Cast stringable to string.
		if ( is_object( $value ) && method_exists( $value, '__toString' ) ) {
			$value = (string) $value;
		}

		// Check if number is a whole number or a float using regex
		return is_numeric( $value ) && ! is_float( $value )
			? (int) $value
			: (float) $value;
	}

	/**
	 * Sanitizes a value as is (pass through).
	 *
	 * @param string|int|float|Stringable $value
	 * @return string|int|float
	 */
	public static function noop( $value ) {
		// Cast stringable to string.
		if ( is_object( $value ) && method_exists( $value, '__toString' ) ) {
			$value = (string) $value;
		}

		return $value;
	}

}
