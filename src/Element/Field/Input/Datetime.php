<?php

declare( strict_types=1 );

/**
 * Input date field
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
use PinkCrab\Form_Components\Element\Field\Attribute\{Range, Autocomplete, Placeholder, Datalist, Read_Only, Required};

class Datetime extends Abstract_Input {

	use Range, Autocomplete, Datalist, Required, Read_Only;

	/** @inheritDoc */
	protected $input_type = 'datetime-local';

	/**
	 * Format used for the sanitizer.
	 * @var string
	 */
	protected $sanitizer_format = 'Y-m-d\TH:i:s';

	/** @inheritDoc */
	public function set_defaults(): void {
		// Set the pattern to match the datetime format.
		$this->sanitizer = function( $value ) {

			// Check if value is either  Y-m-d\TH:i:s or Y-m-d\TH:i
			// If not, return empty string.
			if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(:(\d{2}))?$/', $value ) ) {
				return '';
			}

			// Set the format based on it containing seconds or not.
			$format = strlen( $value ) === 16 ? 'Y-m-d\TH:i' : 'Y-m-d\TH:i:s';

			$date = DateTimeImmutable::createFromFormat( $format, Sanitize::text( $value ) );
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

	/**
	 * Step by days
	 *
	 * @param int $days
	 * @return self
	 */
	public function step_by_days( int $days ): self {
		$this->step( $days * 60 * 60 * 24 );
		return $this;
	}

	/**
	 * Step by weeks
	 *
	 * @param int $weeks
	 * @return self
	 */
	public function step_by_weeks( int $weeks ): self {
		$this->step( $weeks * 60 * 60 * 24 * 7 );
		return $this;
	}



}
