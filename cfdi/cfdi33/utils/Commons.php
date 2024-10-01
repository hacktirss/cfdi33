<?php
/*
 * Utils
 * cfdi®
 * © 2020, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since Jan 2020
 */
namespace com\softcoatl\cfdi\utils;

class Utils {

    public static function xml2array($xml) {
        return json_decode(
                    json_encode(
                        simplexml_load_string(
                                str_replace("s:", "", 
                                        str_replace("a:", "", 
                                                str_replace("o:", "", 
                                                        str_replace("u:", "", 
                                                                str_replace("h:", "", '<?xml version="1.0" encoding="utf-8"?>' . $xml))))))), true);
    }

    public static function guidv4($data) {
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function getHeaders($post, $soapAction, $token = "") {
        return array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                empty($token) ? "" : ("Authorization: WRAP access_token=\"" . urldecode($token) . "\"" ),
                "SOAPAction: " . $soapAction,
                "Content-length: " . strlen($post),
        );
    }
    
    public static function canonize($xml) {
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        return $dom->C14N();
    }
}
