<?php
/*
 * File: webhooks/pepipost.php
 * Description: This file provides an endpoint for Pepipost's API.
 * Version: 0.1
 * Contributors:
 *      Pepipost API    https://docs.pepipost.com/
 *      Tabrez Shaikh    http://tabrez.me/
 *      Blaine Moore    http://blainemoore.com
 */

/**
* Example output:
{
"TRANSID":"14551290015805717",
"RCPTID":"12",
"RESPONSE":"smtp;250 2.0.0 Ok: queued as 824C61FBBB3\r",
"EMAIL":"toabc@abc.com",
"TIMESTAMP":"1455193720",
"CLIENTID":"18654",
"FROMADDRESS":"fromabc@abc.com",
"EVENT":"sent",
"USERAGENT":"1468"
}

// for debugging
function webhooks_debug($msg){
    $fp =fopen('webhooklog.txt','a');
    fwrite($fp,"\nmsg($msg)<br />\n");
    fclose($fp);
}
*/
include_once('includes/config.php');
$webhooks_provider = "Pepipost";

$HTTP_RAW_POST_DATA = @file_get_contents('php://input');
$json_payload = urldecode($HTTP_RAW_POST_DATA);

$event = json_decode($json_payload, true);

if (filter_var($event['EMAIL'],FILTER_VALIDATE_EMAIL)) {
    switch($event["EVENT"])
    {
        case "sent": event_send($event); break;
        case "opened": event_open($event); break;
        case "clicked": event_click($event); break;
        case "bounced":
            if($event['BOUNCE_TYPE'] === 'HARDBOUNCE') {
                webhooks_hard_bounce($event['EMAIL']);
            }
            else {
                webhooks_soft_bounce($event['EMAIL']);
            }
            break;
        case "unsubscribed": event_unsubscribe($event); break;
        case "abuse": webhooks_spam_report($event['EMAIL']);
        case "dropped": event_dropped($event); break;
        case "invalid": event_invalid($event); break;
        default: webhooks_debug(" == Invalid category: '".$event["EVENT"]."' for: ".$event["EMAIL"]." ==");
    }
} else { // invalid email address
    webhooks_debug(" == Invalid email address: '".$event['EMAIL']."' ==");
} // if (filter_var($event["recipient"],FILTER_VALIDATE_EMAIL))

//----------------------------------------------------------------------------------//
//              MANDRILL EVENTS UNHANDLED BY WEBHOOKS
//----------------------------------------------------------------------------------//
    function event_open($event) {
        webhooks_debug("Email opened: " . $event['EMAIL'] . " (" . $event['TRANSID'] . ")\t(Currently unhandled by Webhooks)");
    } // function event_open($event)

    function event_click($event) {
        webhooks_debug("Email clicked: " . $event['EMAIL'] . " (" . $event['TRANSID'] . ")\t(Currently unhandled by Webhooks)");
    } // function event_click($event)

    function event_unsubscribe($event) {
        webhooks_debug("Email unsubscribed: " . $event['EMAIL'] . " (" . $event['TRANSID'] . ")\t(Currently unhandled by Webhooks)");
    } // function event_unsubscribe($event)

    function event_send($event) {
        webhooks_debug("Email sent: " . $event['EMAIL'] . " (" . $event['TRANSID'] . ")\t(Currently unhandled by Webhooks)");
    } // function event_send($event)

    function event_dropped($event) {
        webhooks_debug("Email dropped: " . $event['EMAIL'] . " (" . $event['TRANSID'] . ")\t(Currently unhandled by Webhooks)");
    } // function event_dropped($event)

    function event_invalid($event) {
        webhooks_debug("Email invalid: " . $event['EMAIL'] . " (" . $event['TRANSID'] . ")\t(Currently unhandled by Webhooks)");
    } // function event_invalid($event)
//----------------------------------------------------------------------------------//

