<?php
/*
 * SOAPClient
 * cfdi33®
 * ® 2017, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */
namespace com\softcoatl\utils;

require_once ("nusoap/nusoap.php");

class SOAPClient {
    
    /**
     * 
     * @param String $endpoint
     * @param int $connTimeout
     * @param int $respTimeout
     * @return \nusoap_client
     */
    public static function getClient($endpoint, $connTimeout = 60, $respTimeout = 60) {

        $client = new \nusoap_client(trim($endpoint), true);
        $client->timeout = $connTimeout;
        $client->response_timeout = $respTimeout;
        $client->soap_defencoding = "UTF-8";
        $client->decode_utf8 = false;
        $client->http_encoding = "UTF-8";
        $client->namespaces = array("SOAP-ENV" => "http://schemas.xmlsoap.org/soap/envelope/");        
        $client->debugLevel = 9;

        return $client;
    }//getWSClient
}//SOAPClient
