<?php
/**
 * Typed Inputs - Kitchen Sink
 *
 * Email, Password, Search, Tel, URL, Hidden - each with all their traits.
 */

use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Email;
use PinkCrab\Form_Components\Element\Field\Input\Password;
use PinkCrab\Form_Components\Element\Field\Input\Search;
use PinkCrab\Form_Components\Element\Field\Input\Tel;
use PinkCrab\Form_Components\Element\Field\Input\Url;
use PinkCrab\Form_Components\Element\Field\Input\Hidden;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-typed-inputs">

	<!-- ===== EMAIL ===== -->
	<div class="e2e-email-section">
		<!-- Basic email -->
		<?php $this->component( new Input_Component(
			Email::make( 'email_basic' )
				->label( 'Email' )
				->add_class( 'e2e-email-basic' )
				->placeholder( 'user@example.com' )
				->set_existing( 'test@test.com' )
		) ); ?>

		<!-- Email with all traits -->
		<?php $this->component( new Input_Component(
			Email::make( 'email_full' )
				->label( 'Full Email' )
				->add_class( 'e2e-email-full' )
				->pattern( '[a-z]+@[a-z]+\\.[a-z]+' )
				->autocomplete( 'email' )
				->size( 40 )
				->datalist_items( array( 'admin@example.com', 'info@example.com' ) )
				->readonly( true )
				->minlength( 5 )
				->maxlength( 100 )
				->required( true )
		) ); ?>

		<!-- Email disabled -->
		<?php $this->component( new Input_Component(
			Email::make( 'email_disabled' )
				->label( 'Disabled Email' )
				->add_class( 'e2e-email-disabled' )
				->disabled( true )
		) ); ?>

		<!-- Email with notification -->
		<?php $this->component( new Input_Component(
			Email::make( 'email_notification' )
				->label( 'Email Notification' )
				->add_class( 'e2e-email-notification' )
				->error_notification( 'Invalid email' )
		) ); ?>

		<!-- Email with before/after -->
		<?php $this->component( new Input_Component(
			Email::make( 'email_wrapped' )
				->label( 'Wrapped Email' )
				->add_class( 'e2e-email-wrapped' )
				->before( '<span class="email-before">@</span>' )
				->after( '<span class="email-after">.com</span>' )
		) ); ?>

		<!-- Email with custom id and wrapper attrs -->
		<?php $this->component( new Input_Component(
			Email::make( 'email_ids' )
				->label( 'ID Email' )
				->add_class( 'e2e-email-ids' )
				->id( 'custom-email-id' )
				->wrapper_id( 'custom-email-wrapper' )
				->wrapper_data( 'validate', 'email' )
		) ); ?>

		<!-- Email no wrapper -->
		<?php $this->component( new Input_Component(
			Email::make( 'email_no_wrapper' )
				->add_class( 'e2e-email-no-wrapper' )
				->show_wrapper( false )
		) ); ?>

		<!-- Email custom style -->
		<?php
		$custom_email = new Email( 'email_custom_style', new E2E_Custom_Style() );
		$custom_email->label( 'Custom Styled Email' )->add_class( 'e2e-email-custom' );
		$this->component( new Input_Component( $custom_email ) );
		?>
	</div>

	<!-- ===== PASSWORD ===== -->
	<div class="e2e-password-section">
		<?php $this->component( new Input_Component(
			Password::make( 'password_basic' )
				->label( 'Password' )
				->add_class( 'e2e-password-basic' )
				->placeholder( 'Enter password' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Password::make( 'password_full' )
				->label( 'Full Password' )
				->add_class( 'e2e-password-full' )
				->autocomplete( 'new-password' )
				->minlength( 8 )
				->maxlength( 128 )
				->pattern( '(?=.*\\d)(?=.*[a-z])(?=.*[A-Z]).{8,}' )
				->readonly( true )
				->size( 30 )
				->disabled( true )
				->required( true )
		) ); ?>
	</div>

		<!-- Password with notification and before/after -->
		<?php $this->component( new Input_Component(
			Password::make( 'password_extras' )
				->label( 'Password Extras' )
				->add_class( 'e2e-password-extras' )
				->warning_notification( 'Weak password' )
				->before( '<span class="pw-before">Lock</span>' )
				->after( '<span class="pw-after">Strength</span>' )
				->id( 'custom-pw-id' )
				->wrapper_data( 'strength', 'weak' )
		) ); ?>

		<!-- Password no wrapper -->
		<?php $this->component( new Input_Component(
			Password::make( 'password_no_wrapper' )
				->add_class( 'e2e-password-no-wrapper' )
				->show_wrapper( false )
		) ); ?>
	</div>

	<!-- ===== SEARCH ===== -->
	<div class="e2e-search-section">
		<?php $this->component( new Input_Component(
			Search::make( 'search_basic' )
				->label( 'Search' )
				->add_class( 'e2e-search-basic' )
				->placeholder( 'Search...' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Search::make( 'search_full' )
				->label( 'Full Search' )
				->add_class( 'e2e-search-full' )
				->autocomplete( 'off' )
				->pattern( '[a-zA-Z0-9]+' )
				->datalist_items( array( 'Recent 1', 'Recent 2' ) )
				->disabled( true )
				->readonly( true )
				->required( true )
				->minlength( 2 )
				->maxlength( 200 )
				->inputmode( 'search' )
				->spellcheck( 'false' )
		) ); ?>
	</div>

		<!-- Search with notification -->
		<?php $this->component( new Input_Component(
			Search::make( 'search_notification' )
				->label( 'Search Notification' )
				->add_class( 'e2e-search-notification' )
				->info_notification( 'Search tip' )
				->before( '<span class="search-icon">Q</span>' )
				->wrapper_data( 'type', 'search' )
		) ); ?>
	</div>

	<!-- ===== TEL ===== -->
	<div class="e2e-tel-section">
		<?php $this->component( new Input_Component(
			Tel::make( 'tel_basic' )
				->label( 'Telephone' )
				->add_class( 'e2e-tel-basic' )
				->placeholder( '+44 7700 900000' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Tel::make( 'tel_full' )
				->label( 'Full Tel' )
				->add_class( 'e2e-tel-full' )
				->autocomplete( 'tel' )
				->pattern( '\\+?[0-9\\s]+' )
				->datalist_items( array( '+44 123', '+1 555' ) )
				->disabled( true )
				->readonly( true )
				->required( true )
				->minlength( 5 )
				->maxlength( 20 )
				->inputmode( 'tel' )
				->spellcheck( 'false' )
				->size( 20 )
		) ); ?>
	</div>

		<!-- Tel with notification -->
		<?php $this->component( new Input_Component(
			Tel::make( 'tel_notification' )
				->label( 'Tel Notification' )
				->add_class( 'e2e-tel-notification' )
				->success_notification( 'Valid number' )
				->before( '<span class="tel-prefix">+44</span>' )
		) ); ?>
	</div>

	<!-- ===== URL ===== -->
	<div class="e2e-url-section">
		<?php $this->component( new Input_Component(
			Url::make( 'url_basic' )
				->label( 'URL' )
				->add_class( 'e2e-url-basic' )
				->placeholder( 'https://example.com' )
		) ); ?>

		<?php $this->component( new Input_Component(
			Url::make( 'url_full' )
				->label( 'Full URL' )
				->add_class( 'e2e-url-full' )
				->autocomplete( 'url' )
				->pattern( 'https?://.+' )
				->datalist_items( array( 'https://google.com', 'https://github.com' ) )
				->disabled( true )
				->readonly( true )
				->required( true )
				->minlength( 10 )
				->maxlength( 2048 )
				->inputmode( 'url' )
				->spellcheck( 'false' )
				->size( 50 )
		) ); ?>
	</div>

		<!-- URL with notification -->
		<?php $this->component( new Input_Component(
			Url::make( 'url_notification' )
				->label( 'URL Notification' )
				->add_class( 'e2e-url-notification' )
				->error_notification( 'Invalid URL' )
				->after( '<span class="url-hint">Must start with https://</span>' )
		) ); ?>
	</div>

	<!-- ===== HIDDEN ===== -->
	<div class="e2e-hidden-section">
		<?php $this->component( new Input_Component(
			Hidden::make( 'hidden_basic' )
				->set_existing( 'hidden_value_123' )
		) ); ?>
	</div>

</div>
