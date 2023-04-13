<?php

declare( strict_types=1 );

/**
 * Input month field
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

use DateTimeImmutable;
use PinkCrab\Form_Components\Util\Esc;
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field\Input\Abstract_Input;
use PinkCrab\Form_Components\Element\Field\Attribute\{Autocomplete, Range, Datalist, Disabled, Input_Mode, Read_Only, Required};

class Month extends Abstract_Input {

	use Autocomplete, Range, Datalist, Required, Read_Only, Disabled, Input_Mode;

	/**
	 * Sets the input type
	 *
	 * @var string
	 */
	protected $input_type = 'month';

	/**
	 * Format used for the sanitizer.
	 * @var string
	 */
	protected $sanitizer_format = 'Y-m';


	/** @inheritDoc */
	public function set_defaults(): void {
		// Set the pattern to match the date format.
		$this->sanitizer = function( $value ) {
			$date = DateTimeImmutable::createFromFormat(
				Esc::attribute( $this->sanitizer_format ),
				Sanitize::text( $value )
			);
			return $date ? $date->format( Esc::attribute( $this->sanitizer_format ) ) : '';
		};
	}

	/**
	 * Step by months
	 * 
	 * @param int $step
	 * @return self
	 */
	public function step_by_months( int $step = 1 ): self {
		$this->step( $step );
		return $this;
	}

}
