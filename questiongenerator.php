<?php
/*
Plugin Name: Question Generator
Plugin URI: https://www.question-generator.com/
Description: Your SEO Content Strategy, Smarter + Easier + Faster. Find relevant Search Intentions, run Smarter Content Marketing projects and Overcome Competitors by being the first to discover New Content Opportunities 
Author: squareMX
Version: 2022111601
*/

defined( 'WPINC' ) or die;
if(!defined('QUESTIONGENERATOR_PLUGIN_DIR'))   define ('QUESTIONGENERATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
if(!defined('QUESTIONGENERATOR_PLUGIN_URL'))   define ('QUESTIONGENERATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
if(!defined('QUESTIONGENERATOR_PLUGIN_NAME'))   define ('QUESTIONGENERATOR_PLUGIN_NAME', plugin_basename(__FILE__));
if(!defined('QUESTIONGENERATOR_PLUGIN_FILE'))   define ('QUESTIONGENERATOR_PLUGIN_FILE', __FILE__);

/*
 * Active Question Generator Plugin
 */
function questiongenerator_activate(){
    require_once QUESTIONGENERATOR_PLUGIN_DIR.'modele/QGActive.php';
    QUESTIONGENERATOR_Active::questiongenerator_activate();
}

/*
 * deActive Question Generator Plugin
 */
function questiongenerator_deactivate(){
    require_once QUESTIONGENERATOR_PLUGIN_DIR.'modele/QGDeactive.php';
    QUESTIONGENERATOR_Deactive::questiongenerator_deactivate();
}

/*
 * uninstal plugin
 */
function questiongenerator_uninstall(){
    require_once QUESTIONGENERATOR_PLUGIN_DIR.'modele/QGUninstall.php';
    QUESTIONGENERATOR_Uninstall::questiongenerator_uninstallation();
}

require_once QUESTIONGENERATOR_PLUGIN_DIR.'modele/QGConfig.php';
QUESTIONGENERATOR_Config::questiongenerator_init();

/*
 * Hooks
 */
register_activation_hook( __FILE__, 'questiongenerator_activate' );
register_deactivation_hook( __FILE__, 'questiongenerator_deactivate' );
register_uninstall_hook( __FILE__, 'questiongenerator_uninstall' );

require_once QUESTIONGENERATOR_PLUGIN_DIR.'connector/QGRestfull.php';
QUESTIONGENERATOR_Restfull::questiongenerator_init();
