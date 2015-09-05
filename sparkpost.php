<?php
/*
 * File: webhooks/sparkpost.php
 * Description: This file provides an endpoint for Sparkposts's API.
 * Version: 0.1
 * Contributors:
 *      Sparkpost API   https://support.sparkpost.com/customer/portal/articles/1976204-webhook-event-reference
 *      Blaine Moore    http://blainemoore.com
 *      Micah Topping   https://github.com/mtopping
 *
 */

include_once('includes/config.php');
$webhooks_provider = "Sparkpost";
$hard_bounce_classes = [10, 30, 90, 25, 50, 51, 52, 53, 54];

$json_payload = file_get_contents('php://input');
$events = json_decode($json_payload, true);

foreach($events as $event)
{
    if (filter_var($event['msys']['message_event']['rcpt_to'],FILTER_VALIDATE_EMAIL)) {
        switch($event["msys"]["message_event"]["type"])
        {
            case "policy_rejection": 
                webhooks_hard_bounce($event['msys']['message_event']['rcpt_to'], 'Policy Rejection: ' . $event['msys']['message_event']['reason']); break;
            case "out_of_band": 
                if(in_array($event["msys"]["message_event"]["bounce_class"], $hard_bounce_classes)){
                    webhooks_hard_bounce($event['msys']['message_event']['rcpt_to'], 'Asynchronous Rejection: ' . $event['msys']['message_event']['reason']);
                }else{
                    webhooks_soft_bounce($event['msys']['message_event']['rcpt_to'], 'Asynchronous Rejection: ' . $event['msys']['message_event']['reason']);
                }
                break;
            case "spam_complaint": webhooks_spam_report($event['msys']['message_event']['rcpt_to']); break;
            case "bounce": 
                if(in_array($event["msys"]["message_event"]["bounce_class"], $hard_bounce_classes)){
                    webhooks_hard_bounce($event['msys']['message_event']['rcpt_to'], 'Bounce: ' . $event['msys']['message_event']['reason']);
                }else{
                    webhooks_soft_bounce($event['msys']['message_event']['rcpt_to'], 'Bounce: ' . $event['msys']['message_event']['reason']);
                }
                break;
            case "delay": webhooks_soft_bounce($event['msys']['message_event']['rcpt_to'], 'Delay: ' . $event['msys']['message_event']['reason']); break;
            default: 
                webhooks_debug(" == Invalid category: '".$event["category"]."' for: ".$event["recipient"]." ==");
        }
    } else { // invalid email address
        webhooks_debug(" == Invalid email address: '".$event['msg']['email']."' ==");
    } // if (filter_var($event["recipient"],FILTER_VALIDATE_EMAIL))
} // foreach($events as $event)