<?php
/**
 * The Textarea component template.
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var Textarea $field              The textarea element being rendered.
 * @var string   $before_field       HTML rendered before the field's inner content.
 * @var string   $after_field        HTML rendered after the field's inner content.
 * @var string   $field_attributes   Pre-escaped HTML attributes for the textarea element.
 * @var string   $wrapper_attributes Pre-escaped HTML attributes for the wrapper element.
 * @var bool     $show_wrapper       Whether to render the surrounding wrapper div.
 */

use function PinkCrab\FunctionConstructors\Objects\usesTrait;
?>
<?php if ( $show_wrapper ) : ?>
	<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start( $wrapper_attributes ) ); ?>
<?php endif; ?>
	<?php if ( null !== $before_field && '' !== $before_field ) : ?>
		<?php echo wp_kses_post( $before_field ); ?>
	<?php endif; ?>
	<?php if ( $field->has_label() ) : ?>
		<?php $this->component( new PinkCrab\Form_Components\Component\Field\Label_Component( $field->get_label(), $field->get_name(), $field->get_style()->label_class() ) ); ?>
	<?php endif; ?>

	<?php if ( $field->has_pre_description() ) : ?>
		<p class="<?php echo esc_attr( $field->get_style()->description_class() ); ?>"><?php echo wp_kses_post( $field->get_pre_description() ); ?></p>
	<?php endif; ?>

	<textarea
		name="<?php echo esc_attr( $field->get_name() ); ?>"
		<?php echo $field_attributes; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, parts escaped before composition. ?>
		<?php echo $field->has_rows() ? 'rows="' . esc_attr( (string) $field->get_rows() ) . '"' : ''; ?>
		<?php echo $field->has_cols() ? 'cols="' . esc_attr( (string) $field->get_cols() ) . '"' : ''; ?>
	><?php echo $field->has_value() ? esc_textarea( (string) $field->get_value() ) : ''; ?></textarea>

	<?php if ( $field->has_post_description() ) : ?>
		<p class="<?php echo esc_attr( $field->get_style()->description_class() ); ?>"><?php echo wp_kses_post( $field->get_post_description() ); ?></p>
	<?php endif; ?>

	<?php if ( usesTrait( PinkCrab\Form_Components\Element\Field\Attribute\Notification::class )( $field ) && $field->has_notification() ) : ?>
		<?php $this->component( new PinkCrab\Form_Components\Component\Field\Notification_Component( $field ) ); ?>
	<?php endif; ?>

	<?php if ( null !== $after_field && '' !== $after_field ) : ?>
		<?php echo wp_kses_post( $after_field ); ?>
	<?php endif; ?>

<?php if ( $show_wrapper ) : ?>
	<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End() ); ?>
<?php endif; ?>
