<?php

/*
 * FacturaloService
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

class FacturaloService extends BasePACService {

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
            "pass" => $this->PAC->getPassword(),
            "cfdi" => $xmlCFDI
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Timbrando comprobante");
            $wsResponse = $wsClient->call("TimbrarV3", $params);
            $wsError = $wsClient->getError();
            if ($wsError) {

                $this->error = $wsError;
                return FALSE;
            } else if ($wsResponse['code']==200 && $wsResponse['status']=='success') {

                error_log("Timbrando correctamente");
                $cfdiJson = json_decode($wsResponse['data']);
                $xml = $cfdiJson->cfdi;
                return $xml;
            } else if ($wsResponse['code']==307) {

                error_log("Comprobante previamente timbrado");
                $xml = $wsResponse['messageDetail'];
                return $xml;
            } else {

                error_log("ERROR: ". $wsResponse['message']);
                $this->error = $wsResponse['message'];
                return FALSE;
            }
        } catch (\Exception $e) {

            error_log($e->getMessage());
            $this->error = $e->getMessage();
        }

        return FALSE;
    }

    public function getTimbre($xmlCFDI) {
        return timbraComprobante($xmlCFDI);
    }

    public function cancelaComprobantePFXBA($rfc, $uuid, $pass, $pfxBA) {

        $params = array(
            "user" => $this->PAC->getUser(),
            "pass" => $this->PAC->getPassword(),
            "uuid" => $uuid,
            "rfcEmisor" => $rfc,
            "b64Pfx" => base64_encode($pfxBA),
            "passwordPfx" => $pass
        );
        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Cancelando comprobante " . $uuid);
            $wsResponse = $wsClient->call("CancelarPorPFX", $params);
            $wsError = $wsClient->getError();

            if ($wsError) {

                $this->error = $wsError;
                return FALSE;
            } else if ($wsResponse['code']==200 && $wsResponse['status']=='success') {

                error_log("Se cancelo el Folio: " . $uuid . ", Respuesta: " . $wsResponse['menssage']);
                $acuseJson = json_decode($wsResponse['data']);
                $xml = $acuseJson->acuse;
                return $xml;
            } else if ($wsResponse['code']==201) {

                error_log("Folio pendiente de cancelación: " . $uuid . ", Respuesta: " . $wsResponse['message']);
                return $wsResponse['message'];
            } else if ($wsResponse['code']==202) {

                error_log("Folio previamente cancelado: " . $uuid . ", Respuesta: " . $wsResponse['message']);
                $acuseJson = json_decode($wsResponse['data']);
                $xml = $acuseJson->acuse;
                return $xml;
            } else {

                error_log("Error cancelando Folio: " . $uuid . ", Respuesta: " . $wsResponse['message']);
                $this->error = $wsResponse['message'];
                return FALSE;
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
            "pass" => $this->PAC->getPassword(),
            "uuid" => $uuid,
            "rfcEmisor" => $rfcEmisor,
            "b64Cer" => base64_encode($cerBA),
            "b64Key" => base64_encode($keyBA),
            "passwordSAT" => $pass
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Cancelando comprobante " . $uuid);
            $wsResponse = $wsClient->call("CancelarPorCSD", $params);
            $wsError = $wsClient->getError();

            if ($wsError) {

                $this->error = $wsError;
                return FALSE;
            } else if ($wsResponse['code']==200 && $wsResponse['status']=='success') {

                error_log("Se cancelo el Folio: " . $uuid . ", Respuesta: " . $wsResponse['menssage']);
                $acuseJson = json_decode($wsResponse['data']);
                $xml = $acuseJson->acuse;
                return $xml;
            } else if ($wsResponse['code']==201) {

                error_log("Folio pendiente de cancelación: " . $uuid . ", Respuesta: " . $wsResponse['message']);
                return $wsResponse['message'];
            } else if ($wsResponse['code']==202) {

                error_log("Folio previamente cancelado: " . $uuid . ", Respuesta: " . $wsResponse['message']);
                $acuseJson = json_decode($wsResponse['data']);
                $xml = $acuseJson->acuse;
                return $xml;
            } else {

                error_log("Error cancelando Folio: " . $uuid . ", Respuesta: " . $wsResponse['message']);
                $this->error = $wsResponse['menssage'];
                return FALSE;
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
        $params = array(
            "user" => $this->PAC->getUser(),
            "pass" => $this->PAC->getPassword(),
            "uuid" => $uuid,
            "rfcReceptor" => $rfc,
            "b64Pfx" => base64_encode($pfxBA),
            "passwordPfx" => $pass
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Consultando CFDI relacionados a " . $uuid);
            $wsResponse = $wsClient->call("ConsultarCfdiRelacionadosPorPFX", $params);
            $wsError = $wsClient->getError();

            if ($wsError) {

                error_log("ERROR: " . $wsError);
                $this->error = $wsError;
                return FALSE;
            } else if ($wsResponse['code']==200 && $wsResponse['status']=='success') {

                $relacionados = json_decode($wsResponse['data']);
                $acuse = "ConsultaRelacionados " . print_r($relacionados, true);
                return $acuse;
            }
        } catch (\Exception $e) {

            error_log($e->getMessage());
            $this->error = $e->getMessage();
        }

        return FALSE;
    }

    public function getCFDIRelacionadosPFX($rfc, $uuid, $pass, $pfx = 'certificado/pfx.pfx') {
        return $this->getCFDIRelacionadosPFXBA($rfc, $uuid, $pass, file_get_contents($pfx));
    }

    public function getCFDIRelacionadosSignatureBA($rfc, $uuid, $pass, $cerBA, $keyBA) {

        $params = array(
            "user" => $this->PAC->getUser(),
            "pass" => $this->PAC->getPassword(),
            "uuid" => $uuid,
            "rfcReceptor" => $rfc,
            "b64Cer" => base64_encode($cerBA),
            "b64Key" => base64_encode($keyBA),
            "passwordSAT" => $pass
        );

        $wsClient = \com\softcoatl\utils\SOAPClient::getClient($this->PAC->getUrl());

        try {

            error_log("Consultando CFDI relacionados a " . $uuid);
            $wsResponse = $wsClient->call("ConsultarCfdiRelacionadosPorCSD", $params);
            $wsError = $wsClient->getError();

            if ($wsError) {

                error_log("ERROR: " . $wsError);
                $this->error = $wsError;
                return FALSE;
            } else if ($wsResponse['code']==200 && $wsResponse['status']=='success') {

                $relacionados = json_decode($wsResponse['data']);
                $acuse = "ConsultaRelacionados " . print_r($relacionados, true);
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
        throw new \Exception("Función getAcuseCancelacion no implementada por el Servicio Facturalo");
    }

}//FacturaloService
