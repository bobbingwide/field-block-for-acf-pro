# ACF Field block 
![banner](assets/acf-field-block-banner-772x250.jpg)
* Contributors: bobbingwide
* Donate link: https://www.oik-plugins.com/oik/oik-donate/
* Tags: ACF, field, block
* Requires at least: 6.2
* Tested up to: 6.2.2
* Stable tag: 0.1.0
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description 
No code solution to display ACF Fields using the ACF field block.
The ACF field block displays individual fields registered using ACF.

It supports the following field types:

* - Basic: Text, Text Area, Number, Range, Email, URL, Password
* - Content: Image, File, WYSIWYG Editor, oEmbed, Gallery
* - Choice: Select, Checkbox, Radio Button, Button Group, True / False
* - Relational: Link, Post Object, Page Link, Relationship, Taxonomy, User
* - Advanced: Google Map, Date Picker, Date Time Picker, Time Picker, Color Picker
* - Layout: Group, Clone, Repeater, Flexible Content

Features

- Uses a single block – ACF field
- The block can be used within the Query Loop
- Most field types are rendered in the block editor.


This plugin depends on Advanced Custom Fields PRO; it uses the plugin's block logic.


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


## Installation 
1. Upload the contents of the acf-field-block plugin to the `/wp-content/plugins/acf-field-block' directory
1. Activate the acf-field-block plugin through the 'Plugins' menu in WordPress
1. Use the ACF field block


## Screenshots 
1. acf-field-block in action

## Upgrade Notice 
# 0.1.0 
No code solution to display ACF Fields using the ACF field block

## Changelog 
# 0.1.0 
* Added: google_map field type #10
* Changed: Don't handle fields with no name #3
* Changed: Set list-style:none for gallery list items
* Added: clone field type #9
* Changed: Set Google Maps API key from oik options if available and not already set #10
* Added: flexible_content field type #6
* Added: Add render_no_field_info() method. #7
* Added: group field type #4
* Added: repeater field type #5
* Changed: Cater for field;s with no value #7
* Added: Add date_picker, date_time_picker, time_picker, color_picker #1
* Added: Add render_acf_field_block_user to display user's display name #1
* Changed: Refactor to use methods. Allow extensibility using acf_field_block_get_renderer filter #1
* Changed: Refactor to use acf_field_block_renderer class #1
* Changed: Improve comments, cater for missing oik-bwtrace functions #1

# 0.0.0 
* Changed: Refactored to acf-field block ( acf-field/acf-field ) #1
* Added: Created from oik-testimonials ( commit 6b2d92c ) #1
* Tested: With WordPress 6.2.2 and WordPress Multi Site
* Tested: With Advanced Custom Fields PRO v6.1.6
* Tested: With PHP 8
