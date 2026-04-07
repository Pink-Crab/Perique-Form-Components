# PinkCrab Form Components

A Perique framework library for rendering form fields as View Components. Build forms with a fluent PHP API, automatic HTML rendering, built-in sanitization and validation.

---

## Installation

```bash
composer require pinkcrab/form-components
```

Register the module in your Perique bootstrap:

```php
use PinkCrab\Form_Components\Module\Form_Components;

( new App_Factory() )
    ->default_setup()
    ->module( Form_Components::class )
    ->boot();
```

---

## Quick Start

Render a field in any Perique view template:

```php
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Element\Field\Input\Text;

$this->component( new Input_Component(
    Text::make( 'username' )
        ->label( 'Username' )
        ->placeholder( 'Enter your username' )
        ->required( true )
) );
```

Or use the `Make` helper for a more concise syntax:

```php
use PinkCrab\Form_Components\Util\Make;

$this->component( Make::text( 'username', fn( $f ) => $f
    ->label( 'Username' )
    ->placeholder( 'Enter your username' )
    ->required( true )
) );
```

---

## Field Types

### Text Inputs

| Field | Description |
|-------|-------------|
| [Text](fields/text.md) | Standard text input |
| [Email](fields/email.md) | Email with browser validation |
| [Password](fields/password.md) | Masked password input |
| [Search](fields/search.md) | Search input |
| [Tel](fields/tel.md) | Telephone number |
| [URL](fields/url.md) | URL with browser validation |

### Numeric Inputs

| Field | Description |
|-------|-------------|
| [Number](fields/number.md) | Numeric input with spinners |
| [Range](fields/range.md) | Slider control |

### Date & Time Inputs

| Field | Description |
|-------|-------------|
| [Date](fields/date.md) | Date picker |
| [Time](fields/time.md) | Time picker |
| [Datetime](fields/datetime.md) | Combined date and time |
| [Month](fields/month.md) | Month and year picker |
| [Week](fields/week.md) | Week picker |

### Special Inputs

| Field | Description |
|-------|-------------|
| [Color](fields/color.md) | Colour picker |
| [File](fields/file.md) | File upload |
| [Hidden](fields/hidden.md) | Hidden value field |
| [Checkbox](fields/checkbox.md) | Single checkbox |
| [Radio](fields/radio.md) | Single radio button |

### Selection Groups

| Field | Description |
|-------|-------------|
| [Select](fields/select.md) | Dropdown / multi-select |
| [Checkbox Group](fields/checkbox-group.md) | Multiple checkboxes |
| [Radio Group](fields/radio-group.md) | Multiple radio buttons |

### Other Elements

| Element | Description |
|---------|-------------|
| [Textarea](fields/textarea.md) | Multi-line text |
| [Button](fields/button.md) | Button element |

### Structural Elements

| Element | Description |
|---------|-------------|
| [Form](fields/form.md) | Form wrapper with method, action, nonce |
| [Fieldset](fields/fieldset.md) | Fieldset with optional legend |

---

## Architecture

- [Make Utility](make.md) - Factory helper for one-line field creation
- [Style System](style.md) - Customising CSS classes
- [Sanitization](sanitize.md) - Built-in sanitizers
- [Components](components.md) - How elements become renderable components
