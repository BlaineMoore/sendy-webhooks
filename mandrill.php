<?php
/*
 * File: webhooks/mandrill.php
 * Description: This file provides an endpoint for Mandrill's API.
 * Version: 0.1
 * Contributors:
 *      Mandrill API    https://mandrillapp.com/api/docs/webhooks.php.html
 *      Blaine Moore    http://blainemoore.com
 *
 * Requirements:
 *      $webhooks_critsend_api_key must be set in config.php (Sendy's or Webhook's)
 */

include_once('includes/config.php');
$webhooks_provider = "Mandrill";

$HTTP_RAW_POST_DATA = @file_get_contents('php://input');
$dec_url = urldecode($HTTP_RAW_POST_DATA);
$json_payload = substr($dec_url, 16);
$events = json_decode($json_payload, true);

foreach($events as $event)
{
    if (filter_var($event['msg']['email'],FILTER_VALIDATE_EMAIL)) {
        switch($event["event"])
        {
            case "send": event_send($event); break;
            case "deferral": webhooks_soft_bounce($event['msg']['email'], "deferral: " . $event['msg']['bounce_description']); break;
            case "hard_bounce": webhooks_hard_bounce($event['msg']['email'], $event['msg']['bounce_description']); break;
            case "soft_bounce": webhooks_soft_bounce($event['msg']['email'], $event['msg']['bounce_description']); break;
            case "open": event_open($event); break;
            case "click": event_click($event); break;
            case "spam": webhooks_spam_report($event['msg']['email']); break;
            case "spamreport": webhooks_spam_report($event['msg']['email']); break;
            case "unsub": event_unsubscribe($event); break;
            case "reject": webhooks_hard_bounce($event['msg']['email'], "reject: " . $event['msg']['bounce_description']); break;
            default: webhooks_debug(" == Invalid category: '".$event["category"]."' for: ".$event["recipient"]." ==");
        }
    } else { // invalid email address
        webhooks_debug(" == Invalid email address: '".$event['msg']['email']."' ==");
    } // if (filter_var($event["recipient"],FILTER_VALIDATE_EMAIL))
} // foreach($events as $event)

//----------------------------------------------------------------------------------//
//              MANDRILL EVENTS UNHANDLED BY WEBHOOKS
//----------------------------------------------------------------------------------//
    function event_open($event) {
        webhooks_debug("Email opened: " . $event['msg']['email'] . " (" . $event['msg']['subject'] . ")\t(Currently unhandled by Webhooks)");
    } // function event_open($event)

    function event_click($event) {
        webhooks_debug("Email clicked: " . $event['msg']['email'] . " (" . $event['msg']['clicks']['url'] . ")\t(Currently unhandled by Webhooks)");
    } // function event_click($event)

    function event_unsubscribe($event) {
        webhooks_debug("Email unsubscribed: " . $event['msg']['email'] . "\t(Currently unhandled by Webhooks)");
    } // function event_unsubscribe($event)

    function event_send($event) {
        webhooks_debug("Email sent: " . $event['msg']['email'] . "\t(Currently unhandled by Webhooks)");
    } // function event_send($event)

//----------------------------------------------------------------------------------//
