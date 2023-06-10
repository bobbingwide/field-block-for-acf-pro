=== oik testimonials ===
Contributors: bobbingwide
Donate link: https://www.oik-plugins.com/oik/oik-donate/
Tags: testimonial, cycle.all, bw_testimonials, shortcodes, smart, lazy
Requires at least: 3.9
Tested up to: 5.2.2
Stable tag: 0.6.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
better by far testimonials plugin.

As with other testimonials plugins this plugin delivers a Custom Post Type (oik_testimonials) 
and a shortcode to display the testimonials [bw_testimonials].

Features: 
* [bw_testimonials] shortcode to display cycling testimonials
* uses oik base plugin for base functionality
* use for any post type - not just testimonials
* Uses oik-fields to define the Author Name field and Testimonial type taxonomy

acf-field block with ACF Pro

If you want to alter the output from the oik-testimonials/acf-field block
then you can hook into the `render_block_oik-testimonials/acf-field` filter.


== Installation ==
1. Upload the contents of the oik-testimonials plugin to the `/wp-content/plugins/oik-testimonials' directory
1. Activate the oik-testimonials plugin through the 'Plugins' menu in WordPress


== Screenshots ==
1. oik-testimonials in action

== Upgrade Notice ==
= 0.6.0 = 
Upgrade for block editor support 

= 0.5.1 = 
Tested with WordPress 4.6.1 and oik v3.0.3

= 0.5 = 
Upgrade if you need to select testimonials by testimonial_type.
Note: There is no migration tool. You will have to redefine the categories manually.
Use oik-types to re-define the original taxonomies: testimonial-type, oik_testimonials

= 0.4 = 
Required for oik-fields v1.19.1027

= 0.3 =
If you already have testimonials then you will need to create new category entrys in the custom category "Testimonials". 
Note: old category entries will not be deleted. If this causes you problems please let us know.

= 0.2 =
Dependent upon oik base plugin v2.0-beta for responsive [bw_testimonials] shortcode

= 0.1 =
Dependent upon oik base plugin v2.0-alpha and oik-fields

== Changelog ==
= 0.6.0 = 
* Changed: Support the WordPress block editor,[github bobbingwide oik-testimonials issues 3]
* Changed: Dependent upon oik v3.3 and oik-fields v1.51, [github bobbingwide oik-testimonials issues 2]
* Tested: With WordPress 5.2.2 and WordPress Multi Site
* Tested: With Gutenberg 6.1.1
* Tested: With PHP 7.2

= 0.5.1 =
* Tested: With WordPress 4.6.1 and oik v3.0.3

= 0.5 =
* Changed: Renamed custom category "testimonial-types" to "testimonial_types" since custom category names with hyphens cannot be used in shortcodes; shortcodes do not accept hyphenated parameter names.
* Unchanged: There is no migration facility. You will either have to manually update your entries, or apply an update using phpMyAdmin or the equivalent.

= 0.4 =
* Added: Implements "oik_set_spam_fields_oik_testimonials" filter 
* Added: _oik_testimonials_name is now a required field

= 0.3 = 
* Changed: Reverting to using custom category "testimonial-types" rather than attempting to piggy back on "category" used by posts
 
= 0.2 =
* Changed: Added parameters to make the cycler responsive
 
= 0.1 =
* Added: New plugin sponsored by Susan Cowe for Survive and Thrive after Trauma


== Further reading ==
If you want to read more about the oik plugins then please visit the
[oik plugin](https://www.oik-plugins.com/oik) 
**"the oik plugin - for often included key-information"**


Note: Better by Far is the title of an album by Caravan, the legendary Canterbury band.

