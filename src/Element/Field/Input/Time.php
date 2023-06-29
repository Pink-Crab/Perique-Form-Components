<?php

declare( strict_types=1 );

/**
 * Input Time field
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
use PinkCrab\Form_Components\Element\Field\Attribute\{Range, Autocomplete, Datalist, Disabled, Input_Mode, Notification, Read_Only, Required, Single_Value, Step};

class Time extends Abstract_Input {

	use Range, Autocomplete, Datalist, Required, Read_Only, Disabled, Input_Mode;

	/**
	 * Sets the input type
	 *
	 * @var string
	 */
	protected $input_type = 'time';

	/**
	 * Format used for the sanitizer.
	 * @var string
	 */
	protected $sanitizer_format = 'H:i:s';

	/** @inheritDoc */
	public function set_defaults(): void {
		// Set the pattern to match the date format.
		$this->sanitizer = function( $value ) {
			$value = Sanitize::text( $value );

			// Check if value is either H:M or H:M:S
			if ( ! preg_match( '/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $value ) ) {
				return '';
			}

			// Set the format based on containing seconds or not, based on number of : in string.
			$format = substr_count( $value, ':' ) === 2 ? 'H:i:s' : 'H:i';

			$date = DateTimeImmutable::createFromFormat( $format, $value );

			return $date ? $date->format( Esc::attribute( $this->sanitizer_format ) ) : '';
		};
	}

	/**
	 * Step by minutes
	 *
	 * @param int $minutes
	 * @return self
	 */
	public function step_by_minutes( int $minutes ): self {
		$this->step( $minutes * 60 );
		return $this;
	}

	/**
	 * Step by seconds
	 *
	 * @param int $seconds
	 * @return self
	 */
	public function step_by_seconds( int $seconds ): self {
		$this->step( $seconds );
		return $this;
	}

	/**
	 * Step by hours
	 *
	 * @param int $hours
	 * @return self
	 */
	public function step_by_hours( int $hours ): self {
		$this->step( $hours * 60 * 60 );
		return $this;
	}


}
