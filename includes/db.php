<?php
/*
 * File: webhooks/includes/db.php
 * Description: This file includes all database interactions and connections.
 * Version: 0.1
 * Contributors:
 *      Mandrill API    http://www.critsend.com/documentation/api/event/
 *      Blaine Moore    http://blainemoore.com
 *
 * Currently handles:
 *      webhooks_hard_bounce(email, additionalInfo): emails notified as non-existant
 *      webhooks_soft_bounce(email, additionalInfo): temporarily undeliverable emails
 *      webhooks_spam_report(email, additionalInfo): emails marked as spam & should be blacklisted
 *
 */
    $webhooks_time = time();

    function dbConnect() { //Connect to database
        // Access global variables
        global $mysqli;
        global $dbHost;
        global $dbUser;
        global $dbPass;
        global $dbName;

        if(!isset($mysqli)) {
            // Attempt to connect to database server
            if(isset($dbPort)) $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
            else $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

            // If connection failed...
            if ($mysqli->connect_error) {
                webhooks_debug(" == Failed to connect to database: ".$mysqli->connect_error." == ", true);
            } // if ($mysqli->connect_error)

            global $charset; mysqli_set_charset($mysqli, isset($charset) ? $charset : "utf8");
        } // if(!isset($mysqli))

        return $mysqli;
    } // function dbConnect()

    function fail() { //Database connection fails
        webhooks_debug(" == Failed to connect to database. == ", true);
    } // function fail() {

//----------------------------------------------------------------------------------//
//								SENDY INTERACTION FUNCTIONS
//----------------------------------------------------------------------------------//

    function webhooks_hard_bounce($email, $additionalInfo = '') {
        global $webhooks_time;
        $mysqli = dbConnect();

        webhooks_debug("Email hard bounced: $email\t$additionalInfo");
        $sql = 'UPDATE subscribers SET bounced = 1, timestamp = '.$webhooks_time.' WHERE email = "'.$email.'"';
        mysqli_query($mysqli, $sql);
    } // function webhooks_hard_bounce()

    function webhooks_soft_bounce($email, $additionalInfo = '') {
        global $webhooks_time;
        global $webhooks_bounce_limit;
        $mysqli = dbConnect();

        webhooks_debug("Email soft bounced: $email\t$additionalInfo");
        $sql = 'UPDATE subscribers SET bounce_soft = bounce_soft+1 WHERE email = "'.$email.'"';
        $r = mysqli_query($mysqli, $sql);

        // Check for bounce limit
        $sql = 'SELECT bounce_soft FROM subscribers WHERE email = "'.$email.'" LIMIT 1';
        $r2 = mysqli_query($mysqli, $sql);

        if ($r2 && mysqli_num_rows($r2) > 0)
        {
            while($row = mysqli_fetch_array($r2))
            {
                $bounce_soft = $row['bounce_soft'];
            } // while($row = mysqli_fetch_array($r2))

            if($bounce_soft >= $bounce_limit)
            {
                webhooks_hard_bounce($email, "--> Exceeded Soft Bounce Limit ($bounce_soft)");
            }  // if($bounce_soft >= 3)
        } // if ($r2 && mysqli_num_rows($r2) > 0)
    } // function webhooks_soft_bounce()

    function webhooks_spam_report($email, $additionalInfo = '') {
        global $webhooks_time;
        $mysqli = dbConnect();

        webhooks_debug("Email reported as spam: $email\t$additionalInfo");

        $sql = 'SELECT last_campaign, last_ares FROM subscribers WHERE email = "'.$email.'"';
        $r = mysqli_query($mysqli, $sql);
        if ($r && mysqli_num_rows($r) > 0)
        {
            while($row = mysqli_fetch_array($r))
            {
                $campaign_id = $row['last_campaign'];
                $ares_emails_id = $row['last_ares'];
                if($campaign_id=='') $campaign_id = 0;
                if($ares_emails_id=='') $ares_emails_id = 0;

                $sql = 'UPDATE subscribers SET unsubscribed = 0, bounced = 0, complaint = 1, timestamp = '.$webhooks_time.' WHERE email = "'.$email.'" AND (last_campaign = '.$campaign_id.' OR last_ares = '.$ares_emails_id.')';
                mysqli_query($mysqli, $sql);
            }  // while($row = mysqli_fetch_array($r))
        } // if ($r && mysqli_num_rows($r) > 0)
    } // function webhooks_spam_report()

//----------------------------------------------------------------------------------//
?>