# Input `(Abstract)`

## Description
The Input class is an abstract class that represents an input element. It is the base class for all input elements.

## Shared Methods

### Set Existing \[HTTP Method\]
**public function set_existing( int|float|string|bool|Stringable $value )**
> @param int|float|string|bool|Stringable $value 
> @return \PinkCrab\Form_Components\Element\Field\Input\Abstract_Input

Creates a POST endpoint on the groups route. This works the same as $factory->post(..) but without the need to pass the route parameter. Once this is called, you can easily fluently add any additional parameters.

```php
// Text Input
$input = new Text( 'my_input' );
$input->set_existing( 'hello' );

// Number Input
$input = new Number( 'my_input' );
$input->set_existing( 10 );
```

> This allows for the setting of a value to the input, this can be called at any time before the Input is rendered.