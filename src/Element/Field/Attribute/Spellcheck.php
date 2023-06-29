<?php

declare(strict_types=1);

/**
 * Adds spellcheck Attribute functionality
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

trait Spellcheck {

	/**
	 * Ensure only used when the field has attributes.
	 *
	 * @return bool
	 */
	abstract public function has_attributes(): bool;

	/**
	 * Set spellcheck value.
	 *
	 * @param string $spellcheck
	 * @return static
	 */
	public function spellcheck( string $spellcheck ): self {
		$this->attribute( 'spellcheck', Sanitize::text( $spellcheck ) );

		return $this;
	}

	/**
	 * Checks if the field has a spellcheck.
	 *
	 * @return bool
	 */
	public function has_spellcheck(): bool {
		return $this->has_attribute( 'spellcheck' );
	}

	/**
	 * Get spellcheck value.
	 *
	 * @return string|null
	 */
	public function get_spellcheck(): ?string {
		return $this->has_attribute( 'spellcheck' )
			? Esc::attribute( $this->get_attribute( 'spellcheck' ) ?? '' )
			: null;
	}

	/**
	 * Clears the spellcheck value.
	 *
	 * @return static
	 */
	public function clear_spellcheck(): self {
		if ( $this->has_attribute( 'spellcheck' ) ) {
			$this->remove_attribute( 'spellcheck' );
		}
		return $this;
	}

}