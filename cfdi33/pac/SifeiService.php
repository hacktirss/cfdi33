<?php

/*
 * SifeiService
 * cfdi33®
 * ® 2017, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */

namespace com\softcoatl\cfdi\v33;

require_once ("cfdi33/utils/SOAPClient.php");
require_once ("BasePACService.php");

use com\softcoatl\cfdi\v33\schema\Comprobante as Comprobante;

class SifeiService extends BasePACService {

    /**
     * 
     * @param PAC $PAC
     */
    function __construct($PAC) {
        parent::__construct($PAC);
    }

    private function zip($xmlCFDI) {

        $file = tempnam("tmp", "zip");
        error_log("Zipping into file " . $file);   
        $zip = new \ZipArchive();
        if ($zip->open($file, \ZipArchive::OVERWRITE)) {

            $zip->addFromString('.xml', $xmlCFDI);
            $zip->close();

            $contents = file_get_contents($file);

            return $contents;
        }

        return FALSE;
    }//zip

    private function unzip($xmlCFDIZipped) {

        $file = tempnam("tmp", "zip");
        error_log("Unzipping into file " . $file);   
        file_put_contents($file, $xmlCFDIZipped);
        $zip = new \ZipArchive();
        if ($zip->open($file)) {

            $zip->renameIndex(0, '.xml');
            $cfdiTimbrado = $zip->getFromIndex(0);
            $zip->close();
            return $cfdiTimbrado;
        }

        return FALSE;
    }//unzip

    public function timbraComprobante($xmlCFDI) {

        $zipped = $this->zip($xmlCFDI);
        $b64Zipped = base64_encode($zipped);

        $params = array(
            "Usuario" => $this->PAC->getUser(),
            "Password" => $this->PAC->getPassword(),
            "archivoXMLZip" => $b64Zipped,
            "Serie" => $this->PAC->getSerie(),
            "IdEquipo" => $this->PAC->getIdEquipo()  
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Timbrando comprobante");
            $wsResponse = $wsClient->call("getCFDI", $params, "http://MApeados/");
            $wsError = $wsClient->getError();
            if ($wsError) {

                $codigo = $wsResponse['detail']['SifeiException']['codigo']; 
                if ($codigo == '307') {

                    // Previamente timbrado
                    error_log("Comprobante previamente timbrado");
                    return $this->getTimbre($xmlCFDI);
                } else {

                    error_log("ERROR (" . $codigo . "): " . $wsResponse['detail']['SifeiException']['error']);
                    $this->error = "<b>ERROR REPORTADO POR EL SAT (" . $codigo . ")<br/>" . $wsResponse['detail']['SifeiException']['error'] . ".</b><br>" . $wsResponse['detail']['SifeiException']['message'];
                    return FALSE;
                }
            } else {

                error_log("Timbrando correctamente");
                return $this->unzip(base64_decode($wsResponse['return']));
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }

        return FALSE;
    }//timbraComprobante

    public function getTimbre($xmlCFDI) {

        $originalBytes = Comprobante::getOriginalBytes($xmlCFDI);
        $digestion = hash("sha256", $originalBytes);

        $params = array(
            "rfc" => $this->PAC->getUser(),
            "pass" => $this->PAC->getPassword(),
            "hash" => $digestion
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Recuperando timbre");
            $wsResponse = $wsClient->call("getXML", $params, "http://MApeados/");
            $wsError = $wsClient->getError();

            if ($wsError) {

                error_log("ERROR: " . $wsResponse['detail']['SifeiException']['error']);
                $this->error = $wsResponse['detail']['SifeiException']['error'];
                return FALSE;
            } else {

                error_log("Timbre recuperado " . $wsResponse['return']);
                // Carga el timbre
                $tfd =  new \DOMDocument("1.0","UTF-8");
                $tfd->loadXML($wsResponse['return']);
                $timbre = Comprobante\complemento\TimbreFiscalDigital::parse($tfd->childNodes->item(0));
                // Carga el CFDI
                $comprobante = Comprobante::parse($xmlCFDI);
                // Coloca el timbre en el comprobante
                $comprobante->addComplemento($timbre);
                return $comprobante->asXML()->saveXML();
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }

        return FALSE;
    }//getTimbre

    /**
     * 
     * @param String $rfc   RFC del Emisor
     * @param String $uuid  UUID del Comprobante
     * @param String $pass  Password del archivo PFX
     * @param String $pfxBA Archivo PFX
     * @return boolean
     */
    public function cancelaComprobantePFXBA($rfc, $uuid, $pass, $pfxBA) {

        $params = array(
            "usuarioSIFEI" => $this->PAC->getUser(),
            "passwordSifei" => $this->PAC->getPassword(),
            "rfcEmisor" => $rfc,
            "pfx" => base64_encode($pfxBA),
            "passwordPfx" => $pass,
            "uuids"=> array($uuid)
        );
        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrlCancelacion());

        try {
            error_log("Cancelando comprobante " . $uuid);
            $wsResponse = $wsClient->call("cancelaCFDI", $params);
            $wsError = $wsClient->getError();

            if ($wsError) {

                error_log($wsError);
                $this->error = $wsResponse['detail']['SifeiException']['error'];
                return FALSE;
            } else {

                return $wsResponse['return'];
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->error = $e->getMessage();
        }

        return FALSE;
    }

    public function cancelaComprobantePFX($rfc, $uuid, $pass, $pfx = 'certificado/pfx.pfx') {
        return $this->cancelaComprobantePFXBA($rfc, $uuid, $pass, file_get_contents($pfx));
    }

    public function cancelaComprobanteSignatureBA($rfcEmisor, $rfcReceptor, $total, $uuid, $pass, $cerBA, $keyBA) {
        throw new \Exception("Función cancelaComprobanteSignatureBA no implementada para el servicio SIFEI");
    }

    public function cancelaComprobanteSignature($rfcEmisor, $rfcReceptor, $total, $uuid, $pass, $cer = 'certificado/cer.cer', $key = 'certificado/key.key') {
        return $this->cancelaComprobanteSignatureBA($rfcEmisor, $rfcReceptor, $total, $uuid, $pass, file_get_contents($cer), file_get_contents($key));
    }

    public function getCFDIRelacionadosPFXBA($rfc, $uuid, $pass, $pfxBA) {

        $params = array(
            "usuarioSIFEI" => $this->PAC->getUser(),
            "passwordSIFEI" => $this->PAC->getPassword(),
            "rfcReceptor" => $rfc,
            "uuid" => $uuid,
            "pfx" => base64_encode($pfxBA),
            "passwordPfx" => $pass,
        );

        /* @var $wsClient nusoap_client */
        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrlCancelacion());

        try {
            $wsResponse = $wsClient->call("cfdiRelacionado", $params);
            $wsError = $wsClient->getError();

            if ($wsError) {

                error_log($wsResponse['detail']['SifeiException']['error']);
                $this->error = $wsResponse['detail']['SifeiException']['error'];
                return FALSE;
            } else {

                $respuesta = new \DOMDocument("1.0","UTF-8");
                $respuesta->loadXML($wsResponse['return']);
                if ($respuesta->hasChildNodes()) {
                    /* @var $ProcesarRespuestaResult DOMElement */
                    $ProcesarRespuestaResult = $respuesta->firstChild; // ProcesarRespuestaResult
                    if ($ProcesarRespuestaResult->nodeName=="ProcesarRespuestaResult") {

                        for ($i=0; $i<$ProcesarRespuestaResult->childNodes->length; $i++) {

                            $node = $ProcesarRespuestaResult->childNodes->item($i);
                            if ($node->nodeName=="Resultado") {

                                return $node->nodeValue; // TODO parse resultado
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }

        return FALSE;        
    }
    
    public function getCFDIRelacionadosPFX($rfc, $uuid, $pass, $pfx = 'certificado/pfx.pfx') {
        return $this->getCFDIRelacionadosPFXBA($rfc, $uuid, $pass, file_get_contents($pfx));
    }

    public function getCFDIRelacionadosSignatureBA($rfc, $uuid, $pass, $cerBA, $keyBA) {
        throw new \Exception("Función getCFDIRelacionadosSignatureBA no implementada para el servicio SIFEI");
    }

    public function getCFDIRelacionadosSignature($rfc, $uuid, $pass, $cer = 'certificado/cer.cer', $key = 'certificado/key.key') {
        return $this->getCFDIRelacionadosSignatureBA($rfc, $uuid, $pass, file_get_contents($cer), file_get_contents($key));
    }

    public function getAcuseCancelacion($uuid) {
        throw new \Exception("Función getAcuseCancelacion no implementada para el servicio SIFEI");
    }
    
}//SifeiService
