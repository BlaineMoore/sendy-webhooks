<?php
/*
 * File: webhooks/critsend.php
 * Description: This file provides an endpoint for CritSend's API.
 * Version: 0.1
 * Contributors:
 *      Critsend API    http://www.critsend.com/documentation/api/event/
 *      Blaine Moore    http://blainemoore.com
 *
 * Requirements:
 *      $webhooks_critsend_api_key must be set in config.php (Sendy's or Webhook's)
 */

include_once('includes/config.php');
$webhooks_provider = "CritSend";

$critsend_signature = $_SERVER["HTTP_X_CRITSEND_WEBHOOKS_SIGNATURE"];
$json_payload = file_get_contents('php://input');

# Check if payload is valid
if($critsend_signature != hash_hmac("sha256", $json_payload, $critsend_webhooks_key)) {
    webhooks_debug(" == Invalid payload according to our webhooks key == ", true);
} // if($critsend_signature != hash_hmac("sha256", $json_payload, $critsend_webhooks_key))
	
$events = json_decode($json_payload, true);
foreach($events as $event)
{
    if (filter_var($event["recipient"],FILTER_VALIDATE_EMAIL)) {
        switch($event["category"])
        {
            case "open": event_open($event); break;
            case "click": event_click($event); break;
            case "unsubscribed": event_unsubscribe($event); break;
            case "hard_bounce": webhooks_hard_bounce($event["recipient"], $event["status-code"]); break;
            case "soft_bounce": webhooks_soft_bounce($event["recipient"], $event["status-code"]); break;
            case "bounce": webhooks_soft_bounce($event["recipient"], $event["status-code"]); break;
            case "blocked": event_blocked($event); break;
            case "spam_report": webhooks_spam_report($event["recipient"]); break;
            case "filtered": event_filtered($event); break;
            case "error": event_error($event); break;
            default: webhooks_debug(" == Invalid category: '".$event["category"]."' for: ".$event["recipient"]." ==");
        }
    } else { // invalid email address
        webhooks_debug(" == Invalid email address: '".$event["recipient"]."' ==");
    } // if (filter_var($event["recipient"],FILTER_VALIDATE_EMAIL))
} // foreach($events as $event)

//----------------------------------------------------------------------------------//
//              CRITSEND EVENTS UNHANDLED BY WEBHOOKS
//----------------------------------------------------------------------------------//
	function event_open($event) {
        webhooks_debug("Email opened: " . $event["recipient"] . "\t(Currently unhandled by Webhooks)");
	} // function event_open($event)

	function event_click($event) {
        webhooks_debug("Email clicked: " . $event["recipient"] . "\t(Currently unhandled by Webhooks)");
	} // function event_click($event)

	function event_unsubscribe($event) {
        webhooks_debug("Email unsubscribed: " . $event["recipient"] . "\t(Currently unhandled by Webhooks)");
	} // function event_unsubscribe($event)

	function event_blocked($event) {
        webhooks_debug("Email blocked: " . $event["recipient"] . "\t(Currently unhandled by Webhooks)");
	} // function event_blocked($event)

	function event_filtered($event) {
        webhooks_debug("Email filtered: " . $event["recipient"] . "\t(Currently unhandled by Webhooks)");
	} // function event_filtered($event)

	function event_error($event) {
        webhooks_debug("Email error: " . $event["recipient"] . "\t(Currently unhandled by Webhooks)");
	} // function event_error($event)
//----------------------------------------------------------------------------------//

?>
Success.