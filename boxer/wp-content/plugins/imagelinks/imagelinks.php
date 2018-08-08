<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://avirtum.com
 * @since             1.0.0
 * @package           ImageLinks
 *
 * @wordpress-plugin
 * Plugin Name:       ImageLinks (PRO)
 * Plugin URI:        http://avirtum.com
 * Description:       ImageLinks allows you to easily create an interactive image for your site that empowers publishers and bloggers to create more engaging content by adding rich media links to photos. Use this plugin to create interactive news photography, infographics, imagemaps, floormaps and shoppable product catalogs in minutes.
 * Version:           1.4.1
 * Author:            Avirtum
 * Author URI:        http://codecanyon.net/user/avirtum
 * Text Domain:       imagelinks
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-imagelinks-activator.php
 */
function activate_imagelinks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-imagelinks-activator.php';
	ImageLinks_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-imagelinks-deactivator.php
 */
function deactivate_imagelinks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-imagelinks-deactivator.php';
	ImageLinks_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_imagelinks' );
register_deactivation_hook( __FILE__, 'deactivate_imagelinks' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-imagelinks.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_imagelinks() {
	$plugin = new ImageLinks();
	$plugin->run();
}
run_imagelinks();
