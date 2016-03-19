<?php
/*
 * File: webhooks/mailgun.php
 * Description: This file provides an endpoint for MailGun's Webhooks.
 * Version: 0.1
 * Contributors:
 *     Ravi K Shakya
 *
 * Requirements:
 *      $webhooks_mailgun_api_key must be set in config.php (Sendy's or Webhook's)
 */

include('includes/config.php');
$webhooks_provider = "Mailgun";

function verify($apiKey, $token, $timestamp, $signature)
{
    //check if the timestamp is fresh
    if (time()-$timestamp > 15) {
        return false;
    }
    //returns true if signature is valid
    return hash_hmac('sha256', $timestamp.$token, $apiKey) === $signature;
}
	
if (isset($_POST)) 
{	
    webhooks_debug("category: '".$_POST["event"]."' for: ".$_POST["recipient"]);
    if(verify($webhooks_mailgun_api_key, $_POST["token"], $_POST["timestamp"], $_POST["signature"]) == true){

        if (filter_var($_POST["recipient"],FILTER_VALIDATE_EMAIL)) {
	    
            switch($_POST["event"])
            {		
		
		
                case "dropped": webhooks_soft_bounce($_POST["recipient"], $_POST["code"]); break;
                case "bounced": webhooks_soft_bounce($_POST["recipient"], $_POST["code"]); break;
                case "complained": webhooks_spam_report($_POST["recipient"]); break;
                case "error": event_error($_POST["event"]); break;
                default: webhooks_debug(" == Invalid category: '".$_POST["event"]."' for: ".$_POST["recipient"]." ==");
            }
        } else { // invalid email address
            webhooks_debug(" == Invalid email address: '".$_POST["recipient"]."' ==");
        } // if (filter_var($event["recipient"],FILTER_VALIDATE_EMAIL))
    } else { // if(verify)
        webhooks_debug(" == Invalid request: '".$_POST["Message-Id"]."' ==");
    }
} // foreach($events as $event)
//----------------------------------------------------------------------------------//
//              Mailgum EVENTS UNHANDLED BY WEBHOOKS
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
    print "OK";
    http_response_code (200);
    //header("HTTP/1.1 200 OK");

    
?>



