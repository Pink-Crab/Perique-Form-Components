<?php

declare(strict_types=1);

/**
 * Adds Input_Mode Attribute functionality
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

trait Input_Mode {

	/**
	 * Ensure only used when the field has attributes.
	 *
	 * @return bool
	 */
	abstract public function has_attributes(): bool;

	/**
	 * Set inputmode value.
	 *
	 * @param string $inputmode
	 * @return static
	 */
	public function inputmode( string $inputmode ): self {
		$this->attribute( 'inputmode', Sanitize::text( $inputmode ) );

		return $this;
	}

	/**
	 * Checks if the field has a inputmode.
	 *
	 * @return bool
	 */
	public function has_inputmode(): bool {
		return $this->has_attribute( 'inputmode' );
	}

	/**
	 * Get inputmode value.
	 *
	 * @return string|null
	 */
	public function get_inputmode(): ?string {
		return $this->has_attribute( 'inputmode' )
			? Esc::attribute( $this->get_attribute( 'inputmode' ) ?? '' )
			: null;
	}

	/**
	 * Clears the inputmode value.
	 *
	 * @return static
	 */
	public function clear_inputmode(): self {
		if ( $this->has_attribute( 'inputmode' ) ) {
			$this->remove_attribute( 'inputmode' );
		}
		return $this;
	}

}
