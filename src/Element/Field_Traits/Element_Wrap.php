<?php

declare( strict_types=1 );

/**
 * Trait to before and after element contents.
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

trait Element_Wrap {

	use Wrapper_Attributes;

	/**
	 * Holds the contents before the element.
	 *
	 * @var string|null
	 */
	protected $before;

	/**
	 * Holds the contents after the element.
	 *
	 * @var string|null
	 */
	protected $after;

	/**
	 * Sets the before content.
	 *
	 * @param string $before
	 * @return static
	 */
	public function before( string $before ): self {
		$this->before = wp_kses_post( $before );
		return $this;
	}

	/**
	 * Sets the after content.
	 *
	 * @param string $after
	 * @return static
	 */
	public function after( string $after ): self {
		$this->after = wp_kses_post( $after );
		return $this;
	}

	/**
	 * Returns the before content.
	 *
	 * @return string|null
	 */
	public function get_before(): ?string {
		return $this->before;
	}

	/**
	 * Returns the after content.
	 *
	 * @return string|null
	 */
	public function get_after(): ?string {
		return $this->after;

	}

	/**
	 * Checks if the element has before content.
	 *
	 * @return bool
	 */
	public function has_before(): bool {
		return ! is_null( $this->before );

	}

	/**
	 * Checks if the element has after content.
	 *
	 * @return bool
	 */
	public function has_after(): bool {
		return ! is_null( $this->after );
	}


}
