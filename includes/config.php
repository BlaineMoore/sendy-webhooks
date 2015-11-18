<?php
/*
 * File: webhooks/includes/config.php
 * Description: This file includes other required files and provides an optional place for settings.
 * Version: 0.1
 * Contributors:
 *      Blaine Moore    http://blainemoore.com
 *
 * Suggestion:
 *      Copy the variables below to the standard sendy includes/config.php file and set them there.
 *      That way you can update the webhooks files from git without losing your settings and the
 *      only place to worry about overwriting is a file that is already part of the normal Sendy
 *      update process.
 *
 * Warning:
 *      If this folder is placed anywhere other than at the root of the Sendy installation,
 *      be sure to update the compulsory setting below. If you do change that setting, then
 *      be sure not to overwrite this file when performing future updates.
 */

//----------------------------------------------------------------------------------//
//								  COMPULSORY SETTINGS
//----------------------------------------------------------------------------------//

    // This needs to point to the Sendy /includes/config.php file
    // Only change this is you do not place the webhooks folder in the Sendy root folder
    include_once(dirname(__FILE__)."/../../includes/config.php");

//----------------------------------------------------------------------------------//

//----------------------------------------------------------------------------------//
//								  config.php SETTINGS
//----------------------------------------------------------------------------------//

    // I recommend copying these settings to the Sendy /includes/config.php file
    // Optionally, you can update the default values here.
    if(!isset($webhooks_debugger_log))
        $webhooks_debugger_log = 0; // 0 = no debug log, 1 = debug log
    if(!isset($webhooks_bounce_limit))
        $webhooks_bounce_limit = 3; // How many times can an email soft bounce before it is removed?
    if(!isset($webhooks_debug_timezone))
        $webhooks_debug_timezone = 'America/New_York'; // debug log will use this timezone
    if(!isset($webhooks_critsend_api_key))
        $webhooks_critsend_api_key = ""; // Only enter this if using Critsend

//----------------------------------------------------------------------------------//

include_once('debug.php');
include_once('db.php');

?>
