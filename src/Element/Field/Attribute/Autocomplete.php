<?php

declare( strict_types=1 );

/**
 * The autocomplete attribute added to inputs.
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

namespace PinkCrab\Form_Components\Element\Field\Attribute;

trait Autocomplete {

	/**
	 * Ensure only used when the field has attributes.
	 *
	 * @return bool
	 */
	abstract public function has_attributes(): bool;

	/**
	 * If set will use the value as the autocomplete value.
	 *
	 * @param string $autocomplete
	 * @return static
	 */
	public function autocomplete( ?string $autocomplete = 'on' ): self {
		if ( ! is_null( $autocomplete ) ) {
			$this->attribute( 'autocomplete', esc_attr( $autocomplete ) );
		} else {
			$this->remove_attribute( 'autocomplete' );
		}

		return $this;
	}

	/**
	 * Get if select, will be set as first option with no value.
	 *
	 * @return ?string
	 */
	public function get_autocomplete(): ?string {
		return $this->has_attribute( 'autocomplete' )
			? (string) $this->get_attribute( 'autocomplete' )
			: null;
	}

	/**
	 * Checks if the autocomplete attribute is set.
	 *
	 * @return bool
	 */
	public function has_autocomplete(): bool {
		return $this->has_attribute( 'autocomplete' );
	}
}
