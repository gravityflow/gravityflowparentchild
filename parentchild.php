<?php
/*
Plugin Name: Gravity Flow Parent-Child Forms Extension
Plugin URI: https://gravityflow.io
Description: Gravity Flow Parent-Child Forms Extension for Gravity Flow.
Version: 1.1-dev
Author: Gravity Flow
Author URI: https://gravityflow.io
License: GPL-3.0+

------------------------------------------------------------------------
Copyright 2015-2018 Steven Henty S.L.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'GRAVITY_FLOW_PARENT_CHILD_VERSION', '1.1-dev' );
define( 'GRAVITY_FLOW_PARENT_CHILD_EDD_ITEM_NAME', 'Parent-Child Forms' );

add_action( 'gravityflow_loaded', array( 'Gravity_Flow_Parent_Child_Bootstrap', 'load' ), 1 );

class Gravity_Flow_Parent_Child_Bootstrap {

	public static function load() {

		require_once( 'class-parent-child.php' );

		// Registers the class name with GFAddOn.
		GFAddOn::register( 'Gravity_Flow_Parent_Child' );
	}
}

function gravity_flow_parent_child() {
	if ( class_exists( 'Gravity_Flow_Parent_Child' ) ) {
		return Gravity_Flow_Parent_Child::get_instance();
	}
}


add_action( 'admin_init', 'gravityflowparentchild_edd_plugin_updater', 0 );

function gravityflowparentchild_edd_plugin_updater() {

	if ( ! function_exists( 'gravity_flow_parent_child' ) ) {
		return;
	}

	$gravity_flow_parent_child = gravity_flow_parent_child();
	if ( $gravity_flow_parent_child ) {
		$settings = $gravity_flow_parent_child->get_app_settings();

		$license_key = trim( rgar( $settings, 'license_key' ) );

		$edd_updater = new Gravity_Flow_EDD_SL_Plugin_Updater( GRAVITY_FLOW_EDD_STORE_URL, __FILE__, array(
			'version'   => GRAVITY_FLOW_PARENT_CHILD_VERSION,
			'license'   => $license_key,
			'item_name' => GRAVITY_FLOW_PARENT_CHILD_EDD_ITEM_NAME,
			'author'    => 'Steven Henty',
		) );
	}

}
