<?php

declare(strict_types=1);

/**
 * Length property.
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

use PinkCrab\Form_Components\Utils;

trait Length {

	/**
	 * Ensure only used when the field has attributes.
	 *
	 * @return bool
	 */
	abstract public function has_attributes(): bool;

	/**
	 * Get the minlength value
	 *
	 * @return string|null
	 */
	public function get_minlength(): ?string {
		return $this->has_attribute( 'minlength' )
			? (string) $this->get_attribute( 'minlength' )
			: null;
	}

	/**
	 * Checks if we have a minlength value
	 *
	 * @return bool
	 */
	public function has_minlength(): bool {
		return $this->has_attribute( 'minlength' );
	}

	/**
	 * Set the minlength value
	 *
	 * @param int|string|float|null $minlength  The minlength value
	 * @return static
	 */
	public function minlength( $minlength = null ): self {
		if ( null !== $minlength && \is_numeric( $minlength ) ) {
			$this->attribute( 'minlength', Utils::esc_attr( $minlength ) );
		} else {
			$this->remove_attribute( 'minlength' );
		}

		return $this;
	}

	/**
	 * Get the maxlength value
	 *
	 * @return string|null
	 */
	public function get_maxlength(): ?string {
		return $this->has_attribute( 'maxlength' )
			? (string) $this->get_attribute( 'maxlength' )
			: null;
	}

	/**
	 * Checks if we have a maxlength value
	 *
	 * @return bool
	 */
	public function has_maxlength(): bool {
		return $this->has_attribute( 'maxlength' );
	}

	/**
	 * Set the maxlength value
	 *
	 * @param int|string|float|null $maxlength  The maxlength value
	 * @return static
	 */
	public function maxlength( $maxlength = null ): self {
		if ( null !== $maxlength && \is_numeric( $maxlength ) ) {
			$this->attribute( 'maxlength', Utils::esc_attr( $maxlength ) );
		} else {
			$this->remove_attribute( 'maxlength' );
		}

		return $this;
	}

}
