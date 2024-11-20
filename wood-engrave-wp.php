<?php
/**
 * 
 * Plugin Name: Wood Engrave WP
 * plugin URI: https://souloftware.com/
 * version:1.0.0
 * Description: This plugin allows users to upload their photos and instantly preview how they will look when engraved on wood. It provides a realistic simulation of the final product, enabling users to visualize their design before making a purchase. With an intuitive interface and seamless integration with your eCommerce platform, customers can customize, preview, and order their engraved wooden products with ease.
 * Author: Souloftware
 * Author URI: https://souloftware.com/
 */

// first need do somethings beffore call all the functions like create databases and tables like this time


require_once plugin_dir_path(__FILE__) . './admin/activationPlugin/activatePlugin.php';
// ACTIIVATION PLUGIN FUNCTION -CREATE TABLES -
register_activation_hook(__FILE__, 'createAllTables');

// register_deactivation_hook(__FILE__, 'deactivationSetNull');

register_uninstall_hook(__FILE__, 'removeAllTables');



// Include mfp-functions.php, use require_once to stop the script if mfp-functions.php is not found
require_once plugin_dir_path(__FILE__) . 'utils/clt_functions.php';

// include_once plugin_dir_path(__FILE__) . 'admin/notificaciones/tablasCreadas.php';