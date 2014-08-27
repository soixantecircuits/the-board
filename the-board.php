<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   The Board
 * @author    Soixante circuits
 * @license   GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       The Board
 * Description:       Board manager
 * Version:           1.0.0
 * Author:            Soixante circuits
 * Author URI:        http://soixantecircuits.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @theboard:
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/the-board.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @theboard:
 *
 */
register_activation_hook( __FILE__, array( 'The_Board', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'The_Board', 'deactivate' ) );

/*
 * @theboard:
 *
 */
add_action( 'plugins_loaded', array( 'The_Board', 'get_instance' ) );
require_once plugin_dir_path( __FILE__ ) . '/includes/functions.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/shortcodes.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/groups_order.php';

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @theboard:
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin()  ) {
  require_once plugin_dir_path( __FILE__ ) . 'admin/tb_list.php';
  require_once plugin_dir_path( __FILE__ ) . 'admin/tb_list_groups.php';
  require_once( plugin_dir_path( __FILE__ ) . 'admin/the-board-admin.php' );
	add_action( 'plugins_loaded', array( 'The_Board_Admin', 'get_instance' ) );

}
