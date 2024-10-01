<?php
/*
 * CFDIValidator
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since nov 2018
 */
namespace com\softcoatl\cfdi\v33\validation;

require_once ("cfdi33/utils/CurlFactory.php");
require_once ("cfdi33/utils/Commons.php");

class CFDIValidator {

    private static function getHeaders($post) {
        return array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: http://tempuri.org/IConsultaCFDIService/Consulta", 
                "Content-length: " . strlen($post),
        );        
    }

    private static function getSoap($rfcEmisor, $rfcReceptor, $total, $uuid){
        $expresionImpresa = '?re=' . $rfcEmisor . '&rr=' . $rfcReceptor . '&tt=' . $total . '&id=' . $uuid ;
        return '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                               <soapenv:Header/>
                               <soapenv:Body>
                                  <tem:Consulta>
                                      <tem:expresionImpresa><![CDATA['.$expresionImpresa.']]></tem:expresionImpresa>
                                  </tem:Consulta>
                               </soapenv:Body>
                            </soapenv:Envelope>';
    }

    static function CallAPI($rfcEmisor, $rfcReceptor, $total, $uuid, $url = 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc?wsdl') {

        $obj = array();
        try {
            $post = CFDIValidator::getSoap($rfcEmisor, $rfcReceptor, $total, $uuid);
            $headers = CFDIValidator::getHeaders($post);
            $curl = \com\softcoatl\utils\CurlFactory::create($url, $post, $headers);
            $soap = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if($err) {
                    $obj['Error'] = $err;
            } else {
                $soap = \com\softcoatl\cfdi\utils\Commons::xml2array($soap);
                $obj['CodigoEstatus']       = $soap["Body"]["ConsultaResponse"]["ConsultaResult"]["CodigoEstatus"];
                $obj['Estado']              = $soap["Body"]["ConsultaResponse"]["ConsultaResult"]["Estado"];
                $obj['EsCancelable']        = $soap["Body"]["ConsultaResponse"]["ConsultaResult"]["EsCancelable"];
                $obj['EstatusCancelacion']  = $soap["Body"]["ConsultaResponse"]["ConsultaResult"]["EstatusCancelacion"];
            }
        } catch(Exception $e) {
            $obj['Error'] = $e->get;
        }
        return (object) $obj;
    }
}
