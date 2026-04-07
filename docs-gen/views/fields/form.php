<?php
/**
 * Form - Documentation Examples
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
?>
<div class="doc-example" id="basic">
	<p class="doc-example-title">Basic Form (POST)</p>
	<?php $this->component( new Form_Component(
		Form::make( 'contact' )
			->method( 'POST' )
			->action( '/submit' )
			->fields(
				Text::make( 'name' )
					->label( 'Name' )
					->required( true ),
				Email::make( 'email' )
					->label( 'Email' )
					->required( true ),
				Textarea::make( 'message' )
					->label( 'Message' )
					->rows( 4 ),
				Button::make( 'submit' )
					->type( 'submit' )
					->text( 'Send' )
			)
	) ); ?>
</div>

<div class="doc-example" id="get-form">
	<p class="doc-example-title">method( 'GET' )</p>
	<?php $this->component( new Form_Component(
		Form::make( 'search_form' )
			->method( 'GET' )
			->action( '/search' )
			->fields(
				Text::make( 'query' )
					->label( 'Search' )
					->placeholder( 'Search...' ),
				Button::make( 'go' )
					->type( 'submit' )
					->text( 'Go' )
			)
	) ); ?>
</div>

<div class="doc-example" id="enctype">
	<p class="doc-example-title">enctype()</p>
	<?php $this->component( new Form_Component(
		Form::make( 'upload_form' )
			->method( 'POST' )
			->action( '/upload' )
			->enctype( 'multipart/form-data' )
			->fields(
				Text::make( 'file_label' )
					->label( 'File Label' ),
				Button::make( 'upload' )
					->type( 'submit' )
					->text( 'Upload' )
			)
	) ); ?>
</div>

<div class="doc-example" id="nonce">
	<p class="doc-example-title">With Nonce</p>
	<?php $this->component( new Form_Component(
		Form::make( 'secure_form' )
			->method( 'POST' )
			->fields(
				Hidden::make( 'form_id' )
					->set_existing( '42' )
					->show_wrapper( false ),
				Nonce::make( 'save_action', 'form_nonce' ),
				Text::make( 'title' )
					->label( 'Title' ),
				Button::make( 'save' )
					->type( 'submit' )
					->text( 'Save' )
			)
	) ); ?>
</div>

<div class="doc-example" id="before-after">
	<p class="doc-example-title">before() / after()</p>
	<?php $this->component( new Form_Component(
		Form::make( 'wrapped_form' )
			->method( 'POST' )
			->before( '<div style="padding:8px 0;color:#374151;font-weight:500;">Contact Us</div>' )
			->after( '<div style="padding:8px 0;color:#6b7280;font-size:13px;">We will respond within 24 hours.</div>' )
			->fields(
				Text::make( 'subject' )
					->label( 'Subject' ),
				Button::make( 'send' )
					->type( 'submit' )
					->text( 'Send' )
			)
	) ); ?>
</div>

<div class="doc-example" id="complex">
	<p class="doc-example-title">Complex Form with Raw HTML</p>
	<?php $this->component( new Form_Component(
		Form::make( 'complex_form' )
			->method( 'POST' )
			->fields(
				Raw_HTML::make( 'intro' )
					->html( '<p style="color:#6b7280;margin:0 0 12px;">Fill in all required fields.</p>' ),
				Select::make( 'category' )
					->label( 'Category' )
					->options( array(
						''        => 'Select...',
						'bug'     => 'Bug Report',
						'feature' => 'Feature Request',
					) )
					->required( true ),
				Text::make( 'subject' )
					->label( 'Subject' )
					->required( true ),
				Textarea::make( 'details' )
					->label( 'Details' )
					->rows( 4 ),
				Button::make( 'submit' )
					->type( 'submit' )
					->text( 'Submit' )
			)
	) ); ?>
</div>
