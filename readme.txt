=== Field block for ACF PRO ===
Contributors: bobbingwide
Donate link: https://www.oik-plugins.com/oik/oik-donate/
Tags: ACF, field, block
Requires at least: 6.2
Tested up to: 6.7.1
Stable tag: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

No code solution to display ACF fields using the ACF field block.

== Description ==
The ACF field block displays individual fields registered using ACF.

It supports the following field types:

- Basic: Text, Text Area, Number, Range, Email, URL, Password
- Content: Image, File, WYSIWYG Editor, oEmbed, Gallery
- Choice: Select, Checkbox, Radio Button, Button Group, True / False
- Relational: Link, Post Object, Page Link, Relationship, Taxonomy, User
- Advanced: Google Map, Date Picker, Date Time Picker, Time Picker, Color Picker
- Layout: Group, Clone, Repeater, Flexible Content

Features

- Uses a single block – ACF field
- The block can be used within the Query Loop
- Most field types are rendered in the block editor.
- Supports display of the field's label, if required.

This plugin depends on Advanced Custom Fields PRO; it uses the plugin's server side rendering block logic.
If ACF PRO is not activated then the ACF field block will not be registered.

For PHP developers
The extensible architecture allows:
- overrides to rendering logic by field type,
- rendering for custom field types,
- rendering for custom field names

If you want to alter the output from the `acf-field-block/acf-field` block
then you can either hook into the `render_block_acf-field-block/acf-field` filter to 
modify the generated HTML,
or the `acf_field_block_get_renderer` filter to set your own callback function/method
for a field type or specific field.

Google Map fields use JavaScript from maps.googleapis.com.
The Google Map field requires an API key which you can obtain from
https://developers.google.com/maps/documentation/javascript/get-api-key

== Installation ==
1. Upload the contents of the field-block-for-acf-pro plugin to the `/wp-content/plugins/field-block-for-acf-pro' directory
1. Activate the field-block-for-acf-pro plugin through the 'Plugins' menu in WordPress
1. Use the ACF field block in the block editor

== Screenshots ==
1. ACF field block - Edit mode 
1. ACF field block - Preview mode
1. ACF field block - Settings

== Upgrade Notice ==

= 1.4.0 = 
Tested with WordPress 6.7.1, and Advanced Custom Fields Pro v6.3.11, PHP 8.4 and PHPUnit 10 & 11

== Changelog ==
= 1.4.0 = 
* Changed: Update tests for PHPUnit 10 & 11 #27
* Changed: Update tests for WordPress 6.7.1 and ACF PRO 6.3.11 #27
* Changed: Add 'strip' parameter to wp_kses() #19
* Tested: With WordPress 6.7.1 and WordPress Multisite
* Tested: With Advanced Custom Fields PRO v6.3.1
* Tested: With Gutenberg 19.7.0
* Tested: With PHP 8.3 & 8.4
* Tested: With PHPUnit 10 & 11