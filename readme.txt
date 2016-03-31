=== Gravity Flow Parent-Child Extension ===
Contributors: stevehenty
Tags: gravity forms, approvals, workflow
Requires at least: 4.0
Tested up to: 4.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Create parent-child relationships between forms in Gravity Flow.

== Description ==

The Gravity Flow Parent-Child Extension is an extension for Gravity Flow.

Gravity Flow is a commercial Add-On for [Gravity Forms](https://gravityflow.io/gravityforms)

Facebook: [Steven Henty](https://www.facebook.com/gravityflow.io)

= Requirements =

1. [Purchase and install Gravity Forms](https://gravityflow.io/gravityforms)
1. [Purchase and install Gravity Flow](https://gravityflow.io)
1. Wordpress 4.3+
1. Gravity Forms 1.9.4+
1. Gravity Flow 1.1.0.4+


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

= 1.0.0.3 =
- Added the gravityflowparentchild_form_settings capability

= 1.0.0.2 =
- Added the gravityflowparentchild_add_child_entry_link filter.
    Example:
    add_filter( 'gravityflowparentchild_add_child_entry_link', 'sh_filter_gravityflowparentchild_add_child_entry_link', 10, 5 );
    function sh_filter_gravityflowparentchild_new_entry_link( $form_link, $form_url, $parent_form_id, $child_form_id, $parent_entry_id ) {
        $form_url = site_url() . '/new-child-entry-form/?workflow_parent_entry_id=' . $parent_entry_id;
        $form_url = esc_url( $form_url );
        $form_link = sprintf( '<a href="%s">New Evaluation</a>', $form_url );
        return $form_link;
    }

= 1.0.0.1 =
- Added the gravityflowparentchild_view_all capability to control who can see the child entries.

= 1.0.0 =
All new!
