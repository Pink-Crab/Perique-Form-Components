<?php

declare( strict_types=1 );

/**
 * Helper class for creating components from elements in a single call.
 *
 * Provides static methods for each element type that create the element,
 * optionally configure it via a callable, and return the component
 * ready for $this->component() in templates.
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

namespace PinkCrab\Form_Components\Util;

use PinkCrab\Form_Components\Element\Form;
use PinkCrab\Form_Components\Element\Nonce;
use PinkCrab\Form_Components\Element\Button;
use PinkCrab\Form_Components\Element\Raw_HTML;
use PinkCrab\Form_Components\Element\Fieldset;
use PinkCrab\Form_Components\Element\Custom_Field;
use PinkCrab\Form_Components\Component\Field\Custom_Field_Component;
use PinkCrab\Form_Components\Element\Field\Select;
use PinkCrab\Form_Components\Element\Field\Textarea;
use PinkCrab\Form_Components\Element\Field\Input\Tel;
use PinkCrab\Form_Components\Element\Field\Input\Url;
use PinkCrab\Form_Components\Element\Field\Input\Date;
use PinkCrab\Form_Components\Element\Field\Input\File;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Time;
use PinkCrab\Form_Components\Element\Field\Input\Week;
use PinkCrab\Form_Components\Element\Field\Input\Color;
use PinkCrab\Form_Components\Element\Field\Input\Email;
use PinkCrab\Form_Components\Element\Field\Input\Month;
use PinkCrab\Form_Components\Element\Field\Input\Radio;
use PinkCrab\Form_Components\Element\Field\Input\Hidden;
use PinkCrab\Form_Components\Element\Field\Input\Number;
use PinkCrab\Form_Components\Element\Field\Input\Range;
use PinkCrab\Form_Components\Element\Field\Input\Search;
use PinkCrab\Form_Components\Element\Field\Input\Submit;
use PinkCrab\Form_Components\Element\Field\Input\Checkbox;
use PinkCrab\Form_Components\Element\Field\Input\Datetime;
use PinkCrab\Form_Components\Element\Field\Input\Password;
use PinkCrab\Form_Components\Element\Field\Group\Radio_Group;
use PinkCrab\Form_Components\Element\Field\Group\Checkbox_Group;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Component\Field\Select_Component;
use PinkCrab\Form_Components\Component\Field\Button_Component;
use PinkCrab\Form_Components\Component\Field\Textarea_Component;
use PinkCrab\Form_Components\Component\Field\Raw_HTML_Component;
use PinkCrab\Form_Components\Component\Field\Radio_Group_Component;
use PinkCrab\Form_Components\Component\Field\Checkbox_Group_Component;
use PinkCrab\Form_Components\Component\Form\Form_Component;
use PinkCrab\Form_Components\Component\Form\Fieldset_Component;
use PinkCrab\Form_Components\Component\Partial\Nonce_Component;
use PinkCrab\Perique\Services\View\Component\Component;

class Make {

	/**
	 * Text input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Text): Text
	 * @return Component
	 */
	public static function text( string $name, ?callable $config = null ): Component {
		$element = Text::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Email input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Email): Email
	 * @return Component
	 */
	public static function email( string $name, ?callable $config = null ): Component {
		$element = Email::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Password input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Password): Password
	 * @return Component
	 */
	public static function password( string $name, ?callable $config = null ): Component {
		$element = Password::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Search input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Search): Search
	 * @return Component
	 */
	public static function search( string $name, ?callable $config = null ): Component {
		$element = Search::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Tel input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Tel): Tel
	 * @return Component
	 */
	public static function tel( string $name, ?callable $config = null ): Component {
		$element = Tel::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * URL input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Url): Url
	 * @return Component
	 */
	public static function url( string $name, ?callable $config = null ): Component {
		$element = Url::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Number input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Number): Number
	 * @return Component
	 */
	public static function number( string $name, ?callable $config = null ): Component {
		$element = Number::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Range input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Range): Range
	 * @return Component
	 */
	public static function range( string $name, ?callable $config = null ): Component {
		$element = Range::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Date input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Date): Date
	 * @return Component
	 */
	public static function date( string $name, ?callable $config = null ): Component {
		$element = Date::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Time input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Time): Time
	 * @return Component
	 */
	public static function time( string $name, ?callable $config = null ): Component {
		$element = Time::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Datetime input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Datetime): Datetime
	 * @return Component
	 */
	public static function datetime( string $name, ?callable $config = null ): Component {
		$element = Datetime::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Month input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Month): Month
	 * @return Component
	 */
	public static function month( string $name, ?callable $config = null ): Component {
		$element = Month::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Week input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Week): Week
	 * @return Component
	 */
	public static function week( string $name, ?callable $config = null ): Component {
		$element = Week::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Color input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Color): Color
	 * @return Component
	 */
	public static function color( string $name, ?callable $config = null ): Component {
		$element = Color::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * File input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(File): File
	 * @return Component
	 */
	public static function file( string $name, ?callable $config = null ): Component {
		$element = File::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Hidden input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Hidden): Hidden
	 * @return Component
	 */
	public static function hidden( string $name, ?callable $config = null ): Component {
		$element = Hidden::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Checkbox input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Checkbox): Checkbox
	 * @return Component
	 */
	public static function checkbox( string $name, ?callable $config = null ): Component {
		$element = Checkbox::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Radio input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Radio): Radio
	 * @return Component
	 */
	public static function radio( string $name, ?callable $config = null ): Component {
		$element = Radio::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Submit input.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Submit): Submit
	 * @return Component
	 */
	public static function submit( string $name, ?callable $config = null ): Component {
		$element = Submit::make( $name );
		return new Input_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Select field.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Select): Select
	 * @return Component
	 */
	public static function select( string $name, ?callable $config = null ): Component {
		$element = Select::make( $name );
		return new Select_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Textarea field.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Textarea): Textarea
	 * @return Component
	 */
	public static function textarea( string $name, ?callable $config = null ): Component {
		$element = Textarea::make( $name );
		return new Textarea_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Checkbox group.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Checkbox_Group): Checkbox_Group
	 * @return Component
	 */
	public static function checkbox_group( string $name, ?callable $config = null ): Component {
		$element = Checkbox_Group::make( $name );
		return new Checkbox_Group_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Radio group.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Radio_Group): Radio_Group
	 * @return Component
	 */
	public static function radio_group( string $name, ?callable $config = null ): Component {
		$element = Radio_Group::make( $name );
		return new Radio_Group_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Button element.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Button): Button
	 * @return Component
	 */
	public static function button( string $name, ?callable $config = null ): Component {
		$element = Button::make( $name );
		return new Button_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Nonce element.
	 *
	 * @param string $action
	 * @param string $name
	 * @return Component
	 */
	public static function nonce( string $action, string $name = '_wpnonce' ): Component {
		return new Nonce_Component( Nonce::make( $action, $name ) );
	}

	/**
	 * Raw HTML element.
	 *
	 * @param string $name
	 * @param string $html
	 * @return Component
	 */
	public static function raw_html( string $name, string $html = '' ): Component {
		return new Raw_HTML_Component( Raw_HTML::make( $name, $html ) );
	}

	/**
	 * Form element.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Form): Form
	 * @return Component
	 */
	public static function form( string $name, ?callable $config = null ): Component {
		$element = Form::make( $name );
		return new Form_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Fieldset element.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Fieldset): Fieldset
	 * @return Component
	 */
	public static function fieldset( string $name, ?callable $config = null ): Component {
		$element = Fieldset::make( $name );
		return new Fieldset_Component( $config ? $config( $element ) : $element );
	}

	/**
	 * Custom field element.
	 *
	 * @param string $name
	 * @param callable|null $config fn(Custom_Field): Custom_Field
	 * @return Component
	 */
	public static function custom( string $name, ?callable $config = null ): Component {
		$element = Custom_Field::make( $name );
		return new Custom_Field_Component( $config ? $config( $element ) : $element );
	}
}
