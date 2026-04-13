<?php

declare(strict_types=1);

/**
 * Adds optional pre and post descriptions to a field.
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

trait Description {

	/**
	 * Description displayed before the input.
	 *
	 * @var string|null
	 */
	protected $pre_description = null;

	/**
	 * Description displayed after the input.
	 *
	 * @var string|null
	 */
	protected $post_description = null;

	/**
	 * Sets the pre-description (displayed before the input).
	 *
	 * @param string $description
	 * @return static
	 */
	public function pre_description( string $description ): self {
		$this->pre_description = $description;
		return $this;
	}

	/**
	 * Gets the pre-description.
	 *
	 * @return string|null
	 */
	public function get_pre_description(): ?string {
		return $this->pre_description;
	}

	/**
	 * Checks if the field has a pre-description.
	 *
	 * @return bool
	 */
	public function has_pre_description(): bool {
		return ! is_null( $this->pre_description );
	}

	/**
	 * Sets the post-description (displayed after the input).
	 *
	 * @param string $description
	 * @return static
	 */
	public function post_description( string $description ): self {
		$this->post_description = $description;
		return $this;
	}

	/**
	 * Gets the post-description.
	 *
	 * @return string|null
	 */
	public function get_post_description(): ?string {
		return $this->post_description;
	}

	/**
	 * Checks if the field has a post-description.
	 *
	 * @return bool
	 */
	public function has_post_description(): bool {
		return ! is_null( $this->post_description );
	}
}
