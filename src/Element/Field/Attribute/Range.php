<?php

declare(strict_types=1);

/**
 * Range field trait, adds min, max and step
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
use PinkCrab\Form_Components\Element\Field\Attribute\Step;

/**
 * Range field trait, adds min, max and step
 *
 * @inherits Step::has_step()
 * @inherits Step::step(int|float|string|null): static
 * @inherits Step::get_step(): ?string
 */
trait Range {

	use Step;

	/**
	 * Ensure only used when the field has attributes.
	 *
	 * @return bool
	 */
	abstract public function has_attributes(): bool;

	/**
	 * Set min value.
	 *
	 * @param int|float|string|null $min
	 * @return static
	 */
	public function min( $min ): self {
		if ( null !== $min && \is_numeric( $min ) ) {
			$this->attribute( 'min', Utils::esc_attr( $min ) );
		} else {
			$this->remove_attribute( 'min' );
		}

		return $this;
	}

	/**
	 * Get min value.
	 *
	 * @return string|null
	 */
	public function get_min(): ?string {
		return $this->has_attribute( 'min' )
			? (string) $this->get_attribute( 'min' )
			: null;
	}

	/**
	 * Set max value.
	 *
	 * @param int|float|string|null $max
	 * @return static
	 */
	public function max( $max ): self {
		if ( null !== $max && \is_numeric( $max ) ) {
			$this->attribute( 'max', Utils::esc_attr( $max ) );
		} else {
			$this->remove_attribute( 'max' );
		}

		return $this;
	}

	/**
	 * Get max value.
	 *
	 * @return string
	 */
	public function get_max(): ?string {
		return $this->has_attribute( 'max' )
			? (string) $this->get_attribute( 'max' )
			: null;
	}

	/**
	 * Set the min and max values.
	 *
	 * @param int|float|string $min
	 * @param int|float|string $max
	 * @return static
	 */
	public function range( $min, $max ): self {
		return $this->min( $min )->max( $max );
	}

}
