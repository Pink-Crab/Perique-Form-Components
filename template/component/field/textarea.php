<?php
/**
 * The Textarea component template
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var Textarea $field
 * @var string $before_field
 * @var string $after_field
 * @var string $field_attributes
 * @var string $wrapper_attributes
 * @var bool $show_wrapper
 */

use function PinkCrab\FunctionConstructors\Objects\usesTrait;
?>
<?php if ( $show_wrapper ) : ?>
	<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start( $wrapper_attributes, $before_field ) ); ?>
<?php endif; ?>
	<?php if ( $field->has_label() ) : ?>
		<?php $this->component( new PinkCrab\Form_Components\Component\Field\Label_Component( $field->get_label(), $field->get_name(), $field->get_style()->label_class() ) ); ?>
	<?php endif; ?>

	<textarea
		name="<?php echo esc_attr( $field->get_name() ); ?>"
		<?php echo $field_attributes; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, parts escaped before composition. ?>
		<?php echo $field->has_rows() ? 'rows="' . esc_attr( (string) $field->get_rows() ) . '"' : ''; ?>
		<?php echo $field->has_cols() ? 'cols="' . esc_attr( (string) $field->get_cols() ) . '"' : ''; ?>
	><?php echo $field->has_value() ? esc_textarea( (string) $field->get_value() ) : ''; ?></textarea>

	<?php if ( usesTrait( PinkCrab\Form_Components\Element\Field\Attribute\Notification::class )( $field ) && $field->has_notification() ) : ?>
		<?php $this->component( new PinkCrab\Form_Components\Component\Field\Notification_Component( $field ) ); ?>
	<?php endif; ?>

<?php if ( $show_wrapper ) : ?>
	<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End( $after_field ) ); ?>
<?php endif; ?>
