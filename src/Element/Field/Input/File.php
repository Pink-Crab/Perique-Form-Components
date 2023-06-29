<?php

declare( strict_types=1 );

/**
 * Input file field
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

namespace PinkCrab\Form_Components\Element\Field\Input;

use PinkCrab\Form_Components\Util\Esc;
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field\Input\Abstract_Input;
use PinkCrab\Form_Components\Element\Field\Attribute\{Multiple, Disabled, Required};

class File extends Abstract_Input {

	use Multiple, Disabled, Required;

	/** @inheritDoc */
	protected $input_type = 'file';

	/** @inheritDoc */
	protected $sanitizer = null;

	/**
	 * Set if select, will be set as first option with no value.
	 *
	 * @param string $capture sets the capture attribute, passing null will force user agent.
	 * @return static
	 * @throws \InvalidArgumentException If the capture is not one of: "user", "environment", "null".
	 */
	public function capture( ?string $capture = null ): self {
		$accepted = array( 'user', 'environment', null );
		if ( ! in_array( $capture, $accepted, true ) ) {
			throw new \InvalidArgumentException( 'Capture must be one of: "user", "environment", "null"' );
		}

		$this->attribute( 'capture', $capture );
		return $this;
	}

	/**
	 * Remove the capture attribute.
	 *
	 * @return static
	 */
	public function remove_capture(): self {
		if ( ! $this->has_capture() ) {
			return $this;
		}

		$this->remove_attribute( 'capture' );
		return $this;
	}

	/**
	 * Checks if the field has a capture.
	 *
	 * @return bool
	 */
	public function has_capture(): bool {
		return $this->has_attribute( 'capture' );
	}

	/**
	 * Get if select, will be set as first option with no value.
	 *
	 * @return string|null
	 */
	public function get_capture(): ?string {
		// If capture is not set, return null.
		if ( ! $this->has_attribute( 'capture' ) ) {
			return null;
		}

		$capture = $this->get_attribute( 'capture' );
		return $capture
			? Esc::attribute( $capture )
			: null;
	}


	/**
	 * Set the accepted mime types.
	 *
	 * @param string $accept sets the accept attribute.
	 * @return static
	 */
	public function accept( string $accept ): self {
		$this->attribute( 'accept', Sanitize::text( $accept ) );
		return $this;
	}

	/**
	 * Remove the accept attribute.
	 *
	 * @return static
	 */
	public function remove_accept(): self {
		if ( ! $this->has_accept() ) {
			return $this;
		}

		$this->remove_attribute( 'accept' );
		return $this;
	}

	/**
	 * Checks if the field has a accept.
	 *
	 * @return bool
	 */
	public function has_accept(): bool {
		return $this->has_attribute( 'accept' );
	}

	/**
	 * Get if select, will be set as first option with no value.
	 *
	 * @return string|null
	 */
	public function get_accept(): ?string {
		return $this->has_attribute( 'accept' )
			? Esc::attribute( $this->get_attribute( 'accept' ) )
			: null;
	}


}
