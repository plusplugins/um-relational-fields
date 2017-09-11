=== UM Relational Fields ===
Contributors: plusplugins
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=59LKNNEUMFPKU
Tags: ultimatemember, ultimate member, relationships, relation, relational fields, link members, profile linking, connect members
Requires at least: 3.0.1
Tested up to: 4.8.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add relationships between users, post types and taxonomies in your Ultimate Member site.

== Description ==

Ultimate Member helps you build a community. A community is all about being linked. This plugin allows you to link users to other users, as well as post types and taxonomies. Now you can create a true, linked community!

See this [video](https://www.youtube.com/watch?v=yk5zPfK8zfE) for a demonstration/tutorial of the plugin.

[youtube https://www.youtube.com/watch?v=yk5zPfK8zfE]

= Important =

This plugin requires the [Ultimate Member](https://wordpress.org/plugins/ultimate-member/) plugin to be installed and activated.

If you like this plugin, please [rate and/or review](https://wordpress.org/support/plugin/um-relational-fields/reviews/) it. If you have ideas on how to make the plugin even better or if you have found any bugs, please report these in the [Support Forum](https://wordpress.org/support/plugin/um-relational-fields/) or in the [GitHub Repository](https://github.com/plusplugins/um-relational-fields/issues).

**More UM Extensions**

Extend your Ultimate Member site even more with the following plugins:

- [Ultimate Member Tabs Pro](https://plusplugins.com/downloads/ultimate-member-tabs-pro/)
- [Ultimate Member Maps](https://plusplugins.com/downloads/ultimate-member-maps/)
- [Ultimate Member Events Calendar](https://plusplugins.com/downloads/ultimate-member-events-calendar/)
- [Ultimate Member Contact Form](https://plusplugins.com/downloads/ultimate-member-contact-form/)

Want to extend your Ultimate Member site even more? Visit [PlusPlugins](https://plusplugins.com) for more Ultimate Member extensions.

**Ultimate Member Tabs Pro**

Need to integrate a UM form in your custom tab? Take a look at [Ultimate Member Tabs Pro](https://plusplugins.com/downloads/ultimate-member-tabs-pro/). Split your monolithic UM profile form into neat, easy-to-navigate tabs!

== Installation ==

Nothing new here. You know the drill.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

= Where do I add relationships? =

In WP Admin, go to Ultimate Member -> Settings -> Field Relationships

= In what order should I create a new relationship? =

First, create a new `dropdown` or `multiselect` field in the UM Form Builder. Note the field meta key.

Then, create a new relationship, using the meta key of the field you just created.

= What should I enter in the options area when I create a new field? =

Anything. UM won't allow you to create a new `dropdown` or `multiselect` field if you leave the options area empty, so just put in any value. This plugin wil override whatever you input there.

= How do I add a new Post Type or Taxonomy? =

If you're new to this, use something like the [Custom Post Type UI plugin](https://wordpress.org/plugins/custom-post-type-ui/) to create new post types and taxonomies.

= Video demonstration/tutorial of the plugin =
See this [video](https://www.youtube.com/watch?v=yk5zPfK8zfE) for a demonstration/tutorial of the plugin.

== Screenshots ==

1. Adding a new relationship.
2. The form edit view.
3. The result.

== Changelog ==

= 1.0 =
* Fix `Notice: Use of undefined constant PP_CONTACT_REQUIRES`

= 0.9 =
* Add Github Repo link

= 0.8 =
* Fix issue with empty meta_key

= 0.7 =
* Minor verbiage tweak

= 0.6 =
* Add video tutorial for UM Relational Fields plugin

= 0.5 =
* Show relations on Profile Card in Members Directory

= 0.4 =
* Add filters to override item values

= 0.3 =
* Add support for directory search
* Sorts options by default

= 0.2 =
* Add support for specific combination of user roles

= 0.1 =
* Initial release.

== Upgrade Notice ==

= 0.4 =
* Add filters to override item values

= 0.3 =
* Important bug fix 

= 0.3 =
* Add support for directory search
* Sorts options by default

= 0.2 =
* Add support for specific combination of user roles

= 0.1 =
* Initial release.
