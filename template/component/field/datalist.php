<?php
/**
 * The template for rendering a datalist for an input field.
 *
 * @package PinkCrab\Form_Fields
 *
 * // Expected Variables
 * @var string $id
 * @var array<string, string|int|float|null> $items
 */
?>
<datalist id="<?php echo esc_attr( $id ); ?>">
	<?php foreach ( $items as $key => $value ) : ?>
		<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
	<?php endforeach; ?>
</datalist>
