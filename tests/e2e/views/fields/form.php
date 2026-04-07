<?php
/**
 * Form - Kitchen Sink
 *
 * Exercises the Form element and Form_Component.
 * Tests form wrapping fields, method, action, enctype, nonce, style classes.
 */

use PinkCrab\Form_Components\Component\Form\Form_Component;
use PinkCrab\Form_Components\Element\Form;
use PinkCrab\Form_Components\Element\Nonce;
use PinkCrab\Form_Components\Element\Button;
use PinkCrab\Form_Components\Element\Raw_HTML;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Input\Email;
use PinkCrab\Form_Components\Element\Field\Input\Hidden;
use PinkCrab\Form_Components\Element\Field\Textarea;
use PinkCrab\Form_Components\Element\Field\Select;

require_once __DIR__ . '/_custom-style.php';
?>
<div id="e2e-form">

	<!-- Basic form with POST method and fields -->
	<?php $this->component( new Form_Component(
		Form::make( 'basic_form' )
			->method( 'POST' )
			->action( '/submit' )
			->fields(
				Text::make( 'form_name' )
					->label( 'Name' )
					->required( true ),
				Email::make( 'form_email' )
					->label( 'Email' )
					->required( true ),
				Textarea::make( 'form_message' )
					->label( 'Message' )
					->rows( 4 ),
				Button::make( 'form_submit' )
					->type( 'submit' )
					->text( 'Send' )
			)
	) ); ?>

	<!-- Form with nonce -->
	<?php $this->component( new Form_Component(
		Form::make( 'nonce_form' )
			->method( 'POST' )
			->fields(
				Hidden::make( 'form_id' )
					->set_existing( '42' ),
				Nonce::make( 'save_form', 'form_nonce' ),
				Button::make( 'nonce_submit' )
					->type( 'submit' )
					->text( 'Save' )
			)
	) ); ?>

	<!-- Form with GET method -->
	<?php $this->component( new Form_Component(
		Form::make( 'search_form' )
			->method( 'GET' )
			->action( '/search' )
			->fields(
				Text::make( 'query' )
					->label( 'Search' )
					->placeholder( 'Search...' ),
				Button::make( 'search_submit' )
					->type( 'submit' )
					->text( 'Go' )
			)
	) ); ?>

	<!-- Form with enctype -->
	<?php $this->component( new Form_Component(
		Form::make( 'upload_form' )
			->method( 'POST' )
			->action( '/upload' )
			->enctype( 'multipart/form-data' )
			->fields(
				Text::make( 'file_label' )
					->label( 'File Label' ),
				Button::make( 'upload_submit' )
					->type( 'submit' )
					->text( 'Upload' )
			)
	) ); ?>

	<!-- Form with before/after content -->
	<?php $this->component( new Form_Component(
		Form::make( 'wrapped_form' )
			->method( 'POST' )
			->before( '<div class="form-header">Form Header</div>' )
			->after( '<div class="form-footer">Form Footer</div>' )
			->fields(
				Text::make( 'wrapped_field' )
					->label( 'Field' )
			)
	) ); ?>

	<!-- Form with custom style -->
	<?php
	$custom_form = new Form( 'custom_form', new E2E_Custom_Style() );
	$custom_form->method( 'POST' )
		->fields(
			Text::make( 'custom_field' )
				->label( 'Custom' )
		);
	$this->component( new Form_Component( $custom_form ) );
	?>

	<!-- Form with select and raw HTML -->
	<?php $this->component( new Form_Component(
		Form::make( 'complex_form' )
			->method( 'POST' )
			->fields(
				Raw_HTML::make( 'form_intro' )
					->html( '<p class="form-intro">Fill in the form below</p>' ),
				Select::make( 'form_category' )
					->label( 'Category' )
					->options( array(
						''    => 'Select...',
						'bug' => 'Bug Report',
						'feature' => 'Feature Request',
					) ),
				Text::make( 'form_subject' )
					->label( 'Subject' )
					->required( true ),
				Button::make( 'complex_submit' )
					->type( 'submit' )
					->text( 'Submit' )
			)
	) ); ?>

</div>
