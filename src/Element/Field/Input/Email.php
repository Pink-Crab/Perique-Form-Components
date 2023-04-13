<?php

declare( strict_types=1 );

/**
 * Input Email field
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

namespace PinkCrab\Form_Components\Element\Field\Input;

use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field\Attribute\Multiple;
use PinkCrab\Form_Components\Element\Field\Input\Abstract_Input;
use function PinkCrab\FunctionConstructors\GeneralFunctions\{pipe};
use function \PinkCrab\FunctionConstructors\Strings\split as splitString;
use function PinkCrab\FunctionConstructors\Arrays\{filter, map, toString};
use PinkCrab\Form_Components\Element\Field\Attribute\{Autocomplete, Datalist, Disabled, Length, Pattern, Placeholder, Read_Only, Required, Single_Value, Size};

class Email extends Abstract_Input {

	use Pattern, Autocomplete, Size, Multiple, Datalist, Placeholder, Read_Only, Disabled, Length, Required;

	/** @inheritDoc */
	protected $input_type = 'email';

	/** @inheritDoc */
	public function set_defaults(): void {
		// Set the sanitizer to allow multiple sanitized emails.
		$this->sanitizer = static function( $value ) {
			if ( ! is_string( $value ) ) {
				return '';
			}

			return pipe(
				$value,
				splitString( ',' ),
				map( 'trim' ),
				filter( Sanitize::EMAIL ),
				toString( ',' )
			);
		};
	}

}
