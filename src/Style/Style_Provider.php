<?php

declare( strict_types=1 );

/**
 * The Style provider for form styles.
 *
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

namespace PinkCrab\Form_Components\Style;

use function PinkCrab\FunctionConstructors\Objects\{implementsInterface};

class Style_Provider {

	/**
	 * The default style.
	 *
	 * @var class-string<Style>
	 */
	protected static $default_style = Default_Style::class;

	/**
	 * Sets the base style class.
	 *
	 * @param class-string<Style> $style
	 * @return void
	 */
	public static function set_default_style( string $style ): void {
		// If not a valid style, throw exception.
		if ( ! implementsInterface( Style::class )( $style ) ) {
			throw new \InvalidArgumentException( 'Defined style must implement Style interface' );
		}

		self::$default_style = $style;
	}

	/**
	 * Gets the base style class.
	 *
	 * @return Style
	 */
	public static function get_default_style(): Style {
		return new self::$default_style();
	}
}
