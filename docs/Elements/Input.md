# Input `(Abstract)`

## Description
The Input class is an abstract class that represents an input element. It is the base class for all input elements.

## Shared Methods

### Set Existing 
**public function set_existing( int|float|string|bool|Stringable $value )**  
> @param int|float|string|bool|Stringable $value   
> @return \PinkCrab\Form_Components\Element\Field\Input\Abstract_Input

This allows for the setting of a value to the input, this can be called at any time before the Input is rendered.

```php
// Text Input
$input = new Text( 'my_input' );
$input->set_existing( 'hello' );

// Number Input
$input = new Number( 'my_input' );
$input->set_existing( 10 );
```

### Set Tab Index

**public function tabindex( int|string $tab_index )**  
> @param int|string $tab_index  
> @return \PinkCrab\Form_Components\Element\Field\Input\Abstract_Input  

Sets the tabindex for the input.

```php
$input = new Text( 'my_input' );
$input->tabindex( 1 );
```

### Clear Tab Index

**public function clear_tabindex()**
> @return \PinkCrab\Form_Components\Element\Field\Input\Abstract_Input  

Clears the tabindex for the input.

```php
$input = new Text( 'my_input' );
$input->tabindex( 1 );
$input->clear_tabindex();
```

### Label

**public function label( string $label )**
> @param string $label  
> @return \PinkCrab\Form_Components\Element\Field\Input\Abstract_Input

Sets the label for the input.

```php
$input = new Text( 'my_input' );
$input->label( 'My Input' );
```

### Notifcation

**public function notification( string $notification, string $type )**
> @param string $notification  
> @param string $type  
> @return \PinkCrab\Form_Components\Element\Field\Input\Abstract_Input

Sets the notification for the input.

```php
$input = new Text( 'my_input' );
$input->notification( 'My Input', 'error' );

// Valid types are: error, warning, success, info
```

