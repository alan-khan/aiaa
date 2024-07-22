<?php
/*
Plugin Name: AIAA
Description: Private plugin to support AIAA web app.
Version: 1.0
Author: Five Pack Creative, LLC
Author URI: https://www.fivepackcreative.com
License: GPL2
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

// Start the output buffer
ob_start();

// Define some constants
define('AIAA_VERSION', '1.0');
define('AIAA_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include the plugin class file
require_once(AIAA_PLUGIN_DIR . 'inc/class.aiaa.php');

// Initialize the plugin
if (class_exists('AIAA')) {
    global $aiaa;
    $aiaa = new AIAA(__FILE__);
}