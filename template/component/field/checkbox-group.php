<?php
/**
 * The Checkbox Group component template
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var Checkbox_Group $field
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
		<legend><?php echo esc_html( $field->get_label() ); ?></legend>
	<?php endif; ?>

	<?php foreach ( $field->get_options() as $value => $label ) : ?>
		<label class="<?php echo esc_attr( sprintf( $field->get_style()->group_option_class(), 'checkbox-group' ) ); ?>">
			<input
				type="checkbox"
				name="<?php echo esc_attr( $field->get_name() ); ?>[]"
				value="<?php echo esc_attr( (string) $value ); ?>"
				<?php echo $field->is_selected( (string) $value ) ? 'checked' : ''; ?>
				<?php echo $field->is_disabled() ? 'disabled' : ''; ?>
			/>
			<?php echo esc_html( $label ); ?>
		</label>
	<?php endforeach; ?>

	<?php if ( usesTrait( PinkCrab\Form_Components\Element\Field\Attribute\Notification::class )( $field ) && $field->has_notification() ) : ?>
		<?php $this->component( new PinkCrab\Form_Components\Component\Field\Notification_Component( $field ) ); ?>
	<?php endif; ?>

<?php if ( $show_wrapper ) : ?>
	<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End( $after_field ) ); ?>
<?php endif; ?>
