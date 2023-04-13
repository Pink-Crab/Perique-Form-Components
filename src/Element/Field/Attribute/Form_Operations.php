<?php

declare(strict_types=1);

/**
 * Adds functionality to a filed to allow it to interact with its form.
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
 * @package PinkCrab\Form_Fields
 */

namespace PinkCrab\Form_Components\Element\Field\Attribute;

use PinkCrab\Form_Components\Util\Esc;
use PinkCrab\Form_Components\Util\Sanitize;

trait Form_Operations {

	/**
	 * Ensure only used when the field has attributes.
	 *
	 * @return bool
	 */
	abstract public function has_attributes(): bool;

	/**
	 * Sets the formaction.
	 *
	 * @param string $action
	 * @return self
	 */
	public function formaction( string $action ): self {
		$this->attribute( 'formaction', Sanitize::text( $action ) );
		return $this;
	}

	/**
	 * Checks if the formaction is set.
	 *
	 * @return bool
	 */
	public function has_formaction(): bool {
		return $this->has_attribute( 'formaction' );
	}

	/**
	 * Gets the formaction.
	 *
	 * @return string|null
	 */
	public function get_formaction(): ?string {
		return $this->has_formaction()
			? Esc::attribute( $this->get_attribute( 'formaction' ) )
			: null;
	}

	/**
	 * Clears the formaction if set
	 *
	 * @return self
	 */
	public function clear_formaction(): self {
		if ( $this->has_formaction() ) {
			$this->remove_attribute( 'formaction' );
		}
		return $this;
	}

	/**
	 * Sets the formenctype.
	 *
	 * @param string $enctype
	 * @return self
	 */
	public function formenctype( string $enctype ): self {
		$this->attribute( 'formenctype', Sanitize::text( $enctype ) );
		return $this;
	}

	/**
	 * Checks if the formenctype is set.
	 *
	 * @return bool
	 */
	public function has_formenctype(): bool {
		return $this->has_attribute( 'formenctype' );
	}

	/**
	 * Gets the formenctype.
	 *
	 * @return string|null
	 */
	public function get_formenctype(): ?string {
		return $this->has_formenctype()
			? Esc::attribute( $this->get_attribute( 'formenctype' ) )
			: null;
	}

	/**
	 * Clears the formenctype if set
	 *
	 * @return self
	 */
	public function clear_formenctype(): self {
		if ( $this->has_formenctype() ) {
			$this->remove_attribute( 'formenctype' );
		}
		return $this;
	}

	/**
	 * Sets the formmethod.
	 *
	 * @param string $method
	 * @return self
	 */
	public function formmethod( string $method ): self {
		$this->attribute( 'formmethod', Sanitize::text( $method ) );
		return $this;
	}

	/**
	 * Checks if the formmethod is set.
	 *
	 * @return bool
	 */
	public function has_formmethod(): bool {
		return $this->has_attribute( 'formmethod' );
	}

	/**
	 * Gets the formmethod.
	 *
	 * @return string|null
	 */
	public function get_formmethod(): ?string {
		return $this->has_formmethod()
			? Esc::attribute( $this->get_attribute( 'formmethod' ) )
			: null;
	}

	/**
	 * Clears the formmethod if set
	 *
	 * @return self
	 */
	public function clear_formmethod(): self {
		if ( $this->has_formmethod() ) {
			$this->remove_attribute( 'formmethod' );
		}
		return $this;
	}

	/**
	 * Sets the formnovalidate.
	 *
	 * @param bool $novalidate
	 * @return self
	 */
	public function formnovalidate( bool $novalidate = true ): self {
		$this->attribute( 'formnovalidate', $novalidate );
		return $this;
	}

	/**
	 * Checks if the formnovalidate is set.
	 *
	 * @return bool
	 */
	public function has_formnovalidate(): bool {
		return $this->has_attribute( 'formnovalidate' );
	}

	/**
	 * Gets the formnovalidate.
	 *
	 * @return bool|null
	 */
	public function get_formnovalidate(): ?bool {
		return $this->has_formnovalidate()
			? (bool) $this->get_attribute( 'formnovalidate' )
			: null;
	}

	/**
	 * Clears the formnovalidate if set
	 *
	 * @return self
	 */
	public function clear_formnovalidate(): self {
		if ( $this->has_formnovalidate() ) {
			$this->remove_attribute( 'formnovalidate' );
		}
		return $this;
	}

	/**
	 * Sets the formtarget.
	 *
	 * @param string $target
	 * @return self
	 */
	public function formtarget( string $target ): self {
		$this->attribute( 'formtarget', Sanitize::text( $target ) );
		return $this;
	}

	/**
	 * Checks if the formtarget is set.
	 *
	 * @return bool
	 */
	public function has_formtarget(): bool {
		return $this->has_attribute( 'formtarget' );
	}

	/**
	 * Gets the formtarget.
	 *
	 * @return string|null
	 */
	public function get_formtarget(): ?string {
		return $this->has_formtarget()
			? Esc::attribute( $this->get_attribute( 'formtarget' ) )
			: null;
	}

	/**
	 * Clears the formtarget if set
	 *
	 * @return self
	 */
	public function clear_formtarget(): self {
		if ( $this->has_formtarget() ) {
			$this->remove_attribute( 'formtarget' );
		}
		return $this;
	}

}
