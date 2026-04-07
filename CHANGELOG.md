# Changelog

## 2.1.0 - 2026-04-07

### Added
- Comprehensive unit tests for all attribute traits (Autocomplete, Checked, Disabled, Input_Mode, Length, Notification, Pattern, Placeholder, Range, Read_Only, Required, Size, Spellcheck)
- Unit tests for Radio input element
- Unit tests for Abstract_Input methods (tabindex, set_existing)
- Full Component_Factory test coverage for all `from_*` methods and `from_elements`
- Unit tests for all Component classes (Datalist, Label, Notification, Group, Field_Wrapper_End, Field_Wrapper_Start, Form_Component, Fieldset_Component)
- Unit tests for Style_Provider and Default_Style
- Unit tests for Validation trait
- Unit tests for Fields trait (add_field, fields, nested fields, validation rules, style cascade)
- Unit tests for Sanitize utility (text, textarea, url, hex_color, email, number, noop)
- Unit tests for Form_Components Module (lifecycle methods, component alias registration, template paths)
- Validator_Stub test fixture for Respect\Validation dependency
- Codecov badge to README

### Changed
- Made `get_attribute()` and `get_attributes()` abstract in the Attributes trait (all consumers already override them)
- Coverage improved from 68% to 98%+
