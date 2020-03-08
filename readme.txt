=== Gravity Flow Parent-Child Extension ===
Contributors: stevehenty
Tags: gravity forms, approvals, workflow
Requires at least: 4.0
Tested up to: 5.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create parent-child relationships between forms in Gravity Flow.

== Description ==

The Gravity Flow Parent-Child Extension is an extension for Gravity Flow.

Gravity Flow is a commercial Add-On for [Gravity Forms](https://gravityflow.io/gravityforms)

Facebook: [Gravity Flow](https://www.facebook.com/gravityflow.io)

= Requirements =

1. [Purchase and install Gravity Forms](https://gravityflow.io/gravityforms)
1. [Purchase and install Gravity Flow](https://gravityflow.io)
1. Wordpress 4.3+
1. The latest version of Gravity Forms
1. The latest version of Gravity Flow


= Support =
If you find any that needs fixing, or if you have any ideas for improvements, please get in touch:
https://gravityflow.io/contact/


== Installation ==

1.  Download the zipped file.
1.  Extract and upload the contents of the folder to /wp-contents/plugins/ folder
1.  Go to the Plugin management page of WordPress admin section and enable the 'Gravity Flow Parent-Child Extension' plugin.

== Frequently Asked Questions ==

= Which license of Gravity Flow do I need? =
The Gravity Flow Parent-Child Extension will work with any license of [Gravity Flow](https://gravityflow.io).


== ChangeLog ==

= 1.2.1 =
- Added security enhancements.

= 1.2 =
- Added support for the license key constant GRAVITY_FLOW_PARENT_CHILD_LICENSE_KEY.


= 1.1 =
- Added the Form ID to the entry meta labels.
- Added the gravityflowparentchild_child_entry_url filter to allow the child entry URL to be modified.
- Added the gravityflowparentchild_form_settings capability.
- Added the gravityflowparentchild_add_child_entry_link filter.
    Example:
    add_filter( 'gravityflowparentchild_add_child_entry_link', 'sh_filter_gravityflowparentchild_add_child_entry_link', 10, 5 );
    function sh_filter_gravityflowparentchild_new_entry_link( $form_link, $form_url, $parent_form_id, $child_form_id, $parent_entry_id ) {
        $form_url = site_url() . '/new-child-entry-form/?workflow_parent_entry_id=' . $parent_entry_id;
        $form_url = esc_url( $form_url );
        $form_link = sprintf( '<a href="%s">New Evaluation</a>', $form_url );
        return $form_link;
    }
- Added the gravityflowparentchild_view_all capability to control who can see the child entries.
- Fixed an issue with the capabilities required to view the parent and child entry metaboxes.
- Updated Members 2.0 integration to use human readable labels for the capabilities. Requires Gravity Flow 1.8.1 or greater.

= 1.0.0 =
All new!
