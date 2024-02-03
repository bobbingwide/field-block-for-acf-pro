README for PHPUnit tests for field-block-for-acf-pro

## Dependencies

The active theme should contain a folder called `acf-json` with
a series of `group_*.json` files that define each field type to be tested.

Each field group should be associated with the `test-acf-fields` CPT

In my local development environment the `test-acf-fields` CPT is defined using oik-types

Plugins that must be activated
- Advanced Custom Fields Pro 
- Field block for ACF Pro
- oik, oik-fields and oik-types

WordPress must be running with language `en_US`.

The PHPUnit tests are run In-Situ using oik-batch.
oik-batch v1.1.1 or above is required.

## Caveats

At present there are no unit tests that check the dependencies.
The tests expect posts with particular post IDs to be present
and that the field values have been set for the fields using ACF.

- post 19196 doesn't have any fields set.
- post 19274 does. 

The test data tests the expected functionality of the plugin.
It does not test the plugin's security against malicious content. 