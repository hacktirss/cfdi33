<?php

/*
 * DFactureService
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

class DFactureService extends BasePACService {

    /**
     * Constructor de la clase
     * @param PAC $PAC VO con los datos del PAC
     */
    function __construct($PAC) {

        parent::__construct($PAC);
    }

    public function timbraComprobante($xmlCFDI) {

        $params = array(
            "user" => $this->PAC->getUser(),
            "password" => $this->PAC->getPassword(),
            "xml" => base64_encode($xmlCFDI)
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Timbrando comprobante");
            $wsResponse = $wsClient->call("TimbrarCFDI33", $params);
            $wsError = $wsClient->getError();

            if ($wsError) {

                error_log($wsError);
                $this->error = $wsError;
                return FALSE;
            } else {

                if ($wsResponse['TimbrarCFDI33Result']['codigo']==100) {

                    error_log("Timbrando correctamente");
                    return base64_decode($wsResponse['TimbrarCFDI33Result']['xml']);
                } else if (strpos($wsResponse['TimbrarCFDI33Result']['mensaje'], "El comprobante fue previamente timbrado")) {

                    error_log("Comprobante previamente timbrado");
                    return base64_decode($wsResponse['TimbrarCFDI33Result']['xml']);
                } else {

                    error_log("ERROR: ". $wsResponse['TimbrarCFDI33Result']['mensaje']);
                    $this->error = $wsResponse['TimbrarCFDI33Result']['mensaje'];
                    return FALSE;
                }
            }
        } catch (\Exception $e) {

            error_log($e->getMessage());
            $this->error = $e->getMessage();
        }

        return FALSE;
    }

    public function getTimbre($xmlCFDI) {
        throw new \Exception("Función getTimbre no implementada por el Servicio DFacture");
    }

    public function cancelaComprobantePFXBA($rfc, $uuid, $pass, $pfxBA) {

        $params = array(
            "user" => $this->PAC->getUser(),
            "password" => $this->PAC->getPassword(),
            "rfc" => $rfc,
            "uuid" => $uuid,
            "pfx" => base64_encode($pfxBA),
            "password_llave" => $pass
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Cancelando comprobante " . $uuid);
            $wsResponse = $wsClient->call("CancelarCFDIPFX", $params);
            $wsError = $wsClient->getError();

            if ($wsError) {

                $this->error = $wsError;
                return FALSE;
            } else {

                if ($wsResponse['CancelarCFDIPFXResult']['codigo']==201) {

                    error_log("Se cancelo el Folio: " . $uuid . ", Respuesta: " . $wsResponse['CancelarCFDIPFXResult']['mensaje']);
                    return $this->getAcuseCancelacion($uuid);
                } else if (strpos($wsResponse['CancelarCFDIPFXResult']['mensaje'], "Comprobante cancelado previamente")) {

                    error_log("Folio previamente cancelado: " . $uuid . ", Respuesta: " . $wsResponse['CancelarCFDIPFXResult']['mensaje']);
                    return $this->getAcuseCancelacion($uuid);
                } else {

                    error_log("Error cancelando Folio: " . $uuid . ", Respuesta: " . $wsResponse['CancelarCFDIPFXResult']['mensaje']);
                    $this->error = $wsResponse['CancelarCFDIPFXResult']['mensaje'];
                    return FALSE;
                }
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

        $params = array(
            "user" => $this->PAC->getUser(),
            "password" => $this->PAC->getPassword(),
            "rfcEmisor" => $rfcEmisor,
            "rfcReceptor" => $rfcReceptor,
            "total" => $total,
            "uuid" => $uuid,
            "certificado" => base64_encode($cerBA),
            "llave" => base64_encode($keyBA),
            "password_llave" => $pass
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Cancelando comprobante " . $uuid);
            $wsResponse = $wsClient->call("CancelarCFDI", $params);
            $wsError = $wsClient->getError();

            if ($wsError) {

                error_log("ERROR: ". $wsError);
                $this->error = $wsError;
                return FALSE;
            } else {

                if ($wsResponse['CancelarCFDIResult']['codigo']==201) {

                    error_log("Se cancelo el Folio: " . $uuid . ", Respuesta: " . $wsResponse['CancelarCFDIResult']['mensaje']);
                    return $this->getAcuseCancelacion($uuid);
                } else if (strpos($wsResponse['CancelarCFDIResult']['mensaje'], "Comprobante cancelado previamente")) {

                    error_log("Folio previamente cancelado: " . $uuid . ", Respuesta: " . $wsResponse['CancelarCFDIResult']['mensaje']);
                    return $this->getAcuseCancelacion($uuid);
                } else {

                    error_log("Error cancelando Folio: " . $uuid . ", Respuesta: " . $wsResponse['CancelarCFDIResult']['mensaje']);
                    $this->error = $wsResponse['CancelarCFDIResult']['mensaje'];
                    return FALSE;
                }
            }
        } catch (\Exception $e) {

            error_log($e->getMessage());
            $this->error = $e->getMessage();
        }

        return FALSE;
    }

    public function cancelaComprobanteSignature($rfcEmisor, $rfcReceptor, $total, $uuid, $pass, $cer = 'certificado/cer.cer', $key = 'certificado/key.key') {
        return $this->cancelaComprobanteSignatureBA($rfcEmisor, $rfcReceptor, $total, $uuid, $pass, file_get_contents($cer), file_get_contents($key));
    }

    public function getCFDIRelacionadosPFXBA($rfc, $uuid, $pass, $pfxBA) {
        throw new \Exception("Función getCFDIRelacionadosPFXBA no implementada por el Servicio DFacture");
    }

    public function getCFDIRelacionadosPFX($rfc, $uuid, $pass, $pfx = 'certificado/pfx.pfx') {
        return $this->getCFDIRelacionadosPFXBA($rfc, $uuid, $pass, file_get_contents($pfx));
    }

    public function getCFDIRelacionadosSignatureBA($rfc, $uuid, $pass, $cerBA, $keyBA) {

        $params = array(
            "user" => $this->PAC->getUser(),
            "password" => $this->PAC->getPassword(),
            "uuid" => $uuid,
            "rfc" => $rfc,
            "certificado" => base64_encode($cerBA),
            "llave" => base64_encode($keyBA),
            "password_llave" => $pass
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Consultando CFDI relacionados a " . $uuid);
            $wsResponse = $wsClient->call("ConsultarRelacionadosCFDI", $params);
            $wsError = $wsClient->getError();

            if ($wsError) {

                error_log("ERROR: " . $wsError);
                $this->error = $wsError;
                return FALSE;
            } else {

                $acuse = "ConsultaRelacionados " . $wsResponse['ConsultarRelacionadosCFDIResult']['mensaje'] .  " HIJOS " . $wsResponse['ConsultarRelacionadosCFDIResult']['UuidsRelacionadosHijos'] . " PADRES " . $wsResponse['ConsultarRelacionadosCFDIResult']['UuidsRelacionadosPadres'];
                return $acuse;
            }
        } catch (\Exception $e) {

            error_log($e->getMessage());
            $this->error = $e->getMessage();
        }

        return FALSE;
    }

    public function getCFDIRelacionadosSignature($rfc, $uuid, $pass, $cer = 'certificado/cer.cer', $key = 'certificado/key.key') {
        return $this->getCFDIRelacionadosSignatureBA($rfc, $uuid, $pass, file_get_contents($cer), file_get_contents($key));
    }

    public function getAcuseCancelacion($uuid) {

        $params = array(
            "user" => $this->PAC->getUser(),
            "password" => $this->PAC->getPassword(),
            "uuid" => $uuid
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Recuperando Acuse para el Folio " . $uuid);
            $wsResponse = $wsClient->call("AcuseCancelacion", $params);
            error_log("DFacture Service Response :: " . print_r($wsResponse, TRUE));
            error_log("DFacture Service Log :: " . $wsClient->debug_str);
            $wsError = $wsClient->getError();
            if ($wsError) {

                error_log("ERROR: ". $wsError);
                $this->error = $wsError;
                return FALSE;
            } else {

                if ($wsResponse['AcuseCancelacionResult']['acuse']!=NULL && $wsResponse['AcuseCancelacionResult']['acuse']!='') {

                    $dfAcuse = base64_decode($wsResponse['AcuseCancelacionResult']['acuse']);
                    $startTimbreTag = "<CancelaCFDResult";
                    $endTimbreTag = "</CancelaCFDResult>";
                    $init = strpos($dfAcuse, $startTimbreTag);
                    $end = strpos($dfAcuse, $endTimbreTag);
                    $acuse = substr($dfAcuse, $init, $end - $init + strlen($endTimbreTag));
                    $acuse = str_replace("CancelaCFDResult", "Acuse", $acuse);
                    error_log("Acuse de cancelación recuperado para el folio " . $uuid . " : " . $acuse);
                    return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . $acuse;
                } else {

                    error_log("ERROR: ". $wsResponse['AcuseCancelacionResult']['mensaje']);
                    $this->error = $wsResponse['AcuseCancelacionResult']['mensaje'];
                    return FALSE;
                }
            }
        } catch (\Exception $e) {

            error_log($e->getMessage());
            $this->error = $e->getMessage();
        }

        return FALSE;
    }

}//DFactureService
