<?php

declare( strict_types=1 );

/**
 * Trait to add Sanitization to a field.
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

namespace PinkCrab\Form_Components\Element\Field_Traits;

trait Sanitizer {

	/**
	 * The fields sanitization callback
	 *
	 * @var ?callable(mixed):mixed
	 */
	protected $sanitizer;

	/**
	 * Sets the sanitizer.
	 *
	 * @param ?callable(mixed):mixed $sanitizer
	 * @return static
	 */
	public function sanitizer( ?callable $sanitizer = null ): self {
		$this->sanitizer = $sanitizer;
		return $this;
	}

	/**
	 * Checks if the field has a sanitizer
	 *
	 * @return bool
	 */
	public function has_sanitizer(): bool {
		return ! is_null( $this->sanitizer );
	}

	/**
	 * Returns the fields sanitizer
	 *
	 * @return ?callable(mixed):mixed
	 */
	public function get_sanitizer(): ?callable {
		return $this->sanitizer;
	}

	/**
	 * Sanitizes the value if a sanitizer is set.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function sanitize( $value ) {
		if ( $this->has_sanitizer() ) {
			return call_user_func( $this->get_sanitizer(), $value ); /* phpstan:ignore-line */
		}
		return $value;
	}


}
