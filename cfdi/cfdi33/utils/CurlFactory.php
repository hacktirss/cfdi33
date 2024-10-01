<?php
/*
 * CurlFactory
 * cfdi33®
 * ® 2017, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since feb 2020
 */
namespace com\softcoatl\utils;

class CurlFactory {

    /**
     * CurlFactory::create
     * @param string $target Target URL
     * @param string $post SOAP message
     * @param array $headers Request headers
     * @return resource
     * @throws Exception
     */
    public static function create($target, $post, $headers) {
        
        if (function_exists("curl_init")) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl, CURLOPT_URL, $target);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($curl, CURLOPT_TIMEOUT, 720);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            return $curl;
        } else {
            throw new \Exception("CURL no está instalado, favor de informar a Soporte");
        }
    }
}
