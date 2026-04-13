<?php

declare( strict_types=1 );

/**
 * Textarea field element.
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

namespace PinkCrab\Form_Components\Element\Field;

use PinkCrab\Form_Components\Element\Field;
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field_Traits\Sanitizer;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Label, Single_Value, Notification, Placeholder, Disabled, Read_Only, Required, Length, Spellcheck, Autocomplete, Description};

class Textarea extends Field {

	use Label, Single_Value, Notification, Placeholder, Disabled, Read_Only, Required, Length, Spellcheck, Autocomplete, Description;

	/**
	 * The default sanitizer for the textarea.
	 *
	 * @var callable(mixed):mixed|null
	 */
	protected $sanitizer = Sanitize::TEXTAREA;

	/**
	 * The number of visible text rows.
	 *
	 * @var int|null
	 */
	protected $rows = null;

	/**
	 * The number of visible text columns.
	 *
	 * @var int|null
	 */
	protected $cols = null;

	/** @inheritDoc */
	public function get_type(): string {
		return 'textarea';
	}

	/** @inheritDoc */
	public function set_existing( $value ): self {
		if ( usesTrait( Sanitizer::class )( $this ) ) {
			$value = $this->sanitize( $value );
		}

		$this->value( $value );
		return $this;
	}

	/**
	 * Set the number of visible text rows.
	 *
	 * @param int $rows
	 * @return static
	 */
	public function rows( int $rows ): self {
		$this->rows = $rows;
		return $this;
	}

	/**
	 * Get the number of visible text rows.
	 *
	 * @return int|null
	 */
	public function get_rows(): ?int {
		return $this->rows;
	}

	/**
	 * Check if rows has been set.
	 *
	 * @return bool
	 */
	public function has_rows(): bool {
		return ! is_null( $this->rows );
	}

	/**
	 * Set the number of visible text columns.
	 *
	 * @param int $cols
	 * @return static
	 */
	public function cols( int $cols ): self {
		$this->cols = $cols;
		return $this;
	}

	/**
	 * Get the number of visible text columns.
	 *
	 * @return int|null
	 */
	public function get_cols(): ?int {
		return $this->cols;
	}

	/**
	 * Check if cols has been set.
	 *
	 * @return bool
	 */
	public function has_cols(): bool {
		return ! is_null( $this->cols );
	}
}
