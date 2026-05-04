<?php
/**
 * The Radio Group component template.
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var Radio_Group $field              The radio group element being rendered.
 * @var string      $before_field       HTML rendered before the field's inner content.
 * @var string      $after_field        HTML rendered after the field's inner content.
 * @var string      $field_attributes   Pre-escaped HTML attributes for each radio input.
 * @var string      $wrapper_attributes Pre-escaped HTML attributes for the wrapper element.
 * @var bool        $show_wrapper       Whether to render the surrounding wrapper div.
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
		<legend><?php echo esc_html( $field->get_label() ); ?></legend>
	<?php endif; ?>

	<?php if ( $field->has_pre_description() ) : ?>
		<p class="<?php echo esc_attr( $field->get_style()->description_class() ); ?>"><?php echo wp_kses_post( $field->get_pre_description() ); ?></p>
	<?php endif; ?>

	<?php foreach ( $field->get_options() as $value => $label ) : ?>
		<label class="<?php echo esc_attr( sprintf( $field->get_style()->group_option_class(), 'radio-group' ) ); ?>">
			<input
				type="radio"
				name="<?php echo esc_attr( $field->get_name() ); ?>"
				value="<?php echo esc_attr( (string) $value ); ?>"
				<?php echo $field->is_selected( (string) $value ) ? 'checked' : ''; ?>
				<?php echo $field->is_disabled() ? 'disabled' : ''; ?>
			/>
			<?php echo esc_html( $label ); ?>
		</label>
	<?php endforeach; ?>

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
