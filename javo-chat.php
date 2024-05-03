<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://javothemes.com
 * @since             1.0.0
 * @package           Javo_Chat
 *
 * @wordpress-plugin
 * Plugin Name:       Javo Chat
 * Plugin URI:        https://javothemes.com
 * Description:       Javo Chat!
 * Version:           1.0.0.18
 * Author:            Javo
 * Author URI:        https://javothemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       javo-chat
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('JAVO_CHAT_VERSION', '1.0.0.16');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-javo-chat-activator.php
 */
function activate_javo_chat()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-javo-chat-activator.php';
	Javo_Chat_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-javo-chat-deactivator.php
 */
function deactivate_javo_chat()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-javo-chat-deactivator.php';
	Javo_Chat_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_javo_chat');
register_deactivation_hook(__FILE__, 'deactivate_javo_chat');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-javo-chat.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_javo_chat()
{

	$plugin = new Javo_Chat();
	$plugin->run();
}
run_javo_chat();
