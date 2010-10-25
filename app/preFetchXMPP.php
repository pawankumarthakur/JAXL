<?php

    /**
     * Pre-fetch XMPP/Jabber data for webpage without using BOSH XEP or Ajax requests
     *
     * This sample application demonstrate how to pre-fetch XMPP data from the jabber server
     * Specifically, this app will fetch logged in user VCard from the jabber server
     * Pre-fetched data can later be htmlized and displayed on the webpage
     *
     * Usage:
     * ------
     * 1) Put this file under your web folder
     * 2) Edit user/pass/domain/host below for your account
     * 3) Hit this file in your browser
     *
     * View jaxl.log for detail
    */

    // include JAXL core
    define('JAXL_BASE_PATH', '/usr/share/php/jaxl');
    require_once JAXL_BASE_PATH.'/core/jaxl.class.php';
    
    // initialize JAXL instance
    $xmpp = new JAXL(array(
        'user'=>'',
        'pass'=>'',
        'domain'=>'localhost',
        'host'=>'localhost',
        'logLevel'=>5
    ));

    // Force CLI mode since we don't intend to use BOSH or Ajax
    $xmpp->mode = "cli";

    // Demo requires VCard XEP
    $xmpp->requires('JAXL0054');
    
    function postConnect() {
        global $xmpp;
        $xmpp->startStream();
    }

    function doAuth($mechanism) {
        global $xmpp;
        $xmpp->auth('DIGEST-MD5');
    }

    function postAuth() {
        global $xmpp;
        $xmpp->JAXL0054('getVCard', false, $xmpp->jid, 'handleVCard');
    }

    function handleVCard($payload) {
        global $xmpp;
        echo "<b>Successfully fetched VCard</b><br/>";
        print_r($payload);
        $xmpp->shutdown();
    }

    function postAuthFailure() {
        echo "OOPS! Auth failed";
    }

    // Register callbacks for required events
    JAXLPlugin::add('jaxl_post_connect', 'postConnect');
    JAXLPlugin::add('jaxl_get_auth_mech', 'doAuth');
    JAXLPlugin::add('jaxl_post_auth', 'postAuth');
    JAXLPlugin::add('jaxl_post_auth_failure', 'postAuthFailure');

    // Run JAXL, Wroom Wroom!
    try {
        if($xmpp->connect()) {
            while($xmpp->stream) {
                $xmpp->getXML();
            }
        }
    }
    catch(Exception $e) {
        die($e->getMessage);
    }

    // Exit after we are done
    exit;

?>