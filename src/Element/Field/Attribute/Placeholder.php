<?php

declare(strict_types=1);

/**
 * Adds Placeholder functionality
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

trait Placeholder {

	/**
	 * Ensure only used when the field has attributes.
	 *
	 * @return bool
	 */
	abstract public function has_attributes(): bool;

	/**
	 * Set if select, will be set as first option with no value.
	 *
	 * @param string $placeholder  If select, will be set as first option with no value.
	 * @return static
	 */
	public function placeholder( ?string $placeholder = null ): self {
		if ( $placeholder ) {
			$this->attribute( 'placeholder', $placeholder );
		} else {
			$this->remove_attribute( 'placeholder' );
		}

		return $this;
	}

	/**
	 * Checks if the field has a placeholder.
	 *
	 * @return bool
	 */
	public function has_placeholder(): bool {
		return $this->has_attribute( 'placeholder' );
	}

	/**
	 * Get if select, will be set as first option with no value.
	 *
	 * @return string|null
	 */
	public function get_placeholder(): ?string {
		return $this->has_attribute( 'placeholder' )
			? (string) $this->get_attribute( 'placeholder' )
			: null;
	}

}
