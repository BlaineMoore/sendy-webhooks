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
 *      define('MANDRILL_WEBHOOK_SECRET', 'asdfASDFasdfASDF1234'); // obtained from Mandrill Webhook control panel
 *      define('MANDRILL_WEBHOOK_URL', 'http://sendy_url/webhooks/mandrill.php');
 */

include_once('includes/config.php');
$webhooks_provider = "Mandrill";

// Ensure Mandrill signature is present
if ((!isset($_POST['mandrill_events'])) || (!isset($_SERVER['HTTP_X_MANDRILL_SIGNATURE']))) {
   echo ("Invalid webhook or missing signature.\n");
   exit;
}

// Verify Mandrill Webhook signature (optional; see requirements)
if (!empty(MANDRILL_WEBHOOK_SECRET) && !empty(MANDRILL_WEBHOOK_URL)) {

  $server_key = $_SERVER['HTTP_X_MANDRILL_SIGNATURE'];
  $test_key = generateSignature(MANDRILL_WEBHOOK_SECRET, MANDRILL_WEBHOOK_URL, $_POST);

  if ($test_key != $server_key) {
    echo ("Invalid API signature.\n");
    exit;
  }
}

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


//----------------------------------------------------------------------------------//
//              MANDRILL SIGNATURE VERIFICATION
//----------------------------------------------------------------------------------//

    function generateSignature($secret, $url, $params) {
    
      $signed_data = $url;
      ksort($params);
      foreach ($params as $key => $value) {
        $signed_data .= $key;
        $signed_data .= $value;
      }

      return base64_encode(hash_hmac('sha1', $signed_data, $secret, true));
    }
