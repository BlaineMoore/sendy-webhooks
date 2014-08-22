<?php
/*
 * File: webhooks/includes/debug.php
 * Description: This file provides code for tracking interactions and the debug log.
 * Version: 0.1
 * Contributors:
 *      Blaine Moore    http://blainemoore.com
 *
 */
    date_default_timezone_set($webhooks_debug_timezone);

    function webhooks_debug($entry, $error = false) {
        global $webhooks_debugger_log;
        global $webhooks_provider;
        if(1 == $webhooks_debugger_log) {
            $now = date("Y-m-d H:i:s e");
            $file = "debuglog.txt";
            $entry = "[$now]\t$webhooks_provider\t$entry\n";
            file_put_contents($file, $entry, FILE_APPEND);
            if($error) { throw new exception($entry); exit; }
        } // if(1 == $debug_log)
    } // function debug_log($entry)
?>