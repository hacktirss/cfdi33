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

use com\softcoatl\utils\SOAPClient;

abstract class FacturaloCaller {

    protected $pac;
    protected $parameters;
    protected $response;
    protected $soapClient;
    protected $soapException;

    function __construct($pac) {
        $this->pac = $pac;
        $this->parameters = array(
            "user" => $this->pac->getUser(),
            "pass" => $this->pac->getPassword());  
        $this->soapClient = SOAPClient::getClient($this->pac->getUrl());
    }

    public abstract function call();

    protected function invoke($operation) {
        try {
            $this->response = $this->soapClient->call($operation, $this->parameters);
        } catch (\Exception $e) {
            $this->soapException = $e->getMessage();
        }

        return $this;
    }

    public function setParameter($key, $value) {

        $this->parameters[$key] = $value;
        return $this;
    }

    public function soapError() {
        return !empty($this->soapException);
    }

    public function success() {

        return !$this->soapError()
                && $this->errorCode(200)
                && $this->status("success");
    }

    public function errorCode($code) {
        return $this->getResponse("code")==$code;
    }

    public function status($status) {
        return $this->getResponse("status")==$status;
    }

    public function getErrorCode() {
        return $this->getResponse("code");
    }

    public function getStatus() {
        return $this->getResponse("status");
    }

    public function getHTMLError() {
        return empty($this->soapException) ? 
                "<b>ERROR REPORTADO POR EL SAT (" . $this->getErrorCode() . ")<br/>" . $this->getResponse("message") . ".</b><br>" . $this->getResponse("messageDetail") :
                "<b>ERROR DE PAC</b><br/>" . $this->soapException;
    }

    public function getResponse($field = "data") {
        return $this->response[$field];
    }
}

class FacturaloTimbrarV3Caller extends FacturaloCaller {

    private static $operation = "TimbrarV3";

    public function call() {

        error_log("Timbrando comprobante. Invocando " . $this->pac->getUrl());
        parent::invoke(static::$operation);
        return $this;
    }
}

abstract class FacturaloCancelarCaller extends FacturaloCaller {
    
    public function processCancelation() {
        if ($this->success()) {
            
            error_log("Se cancelo el Folio: " . $this->parameters['uuid']);
            return json_decode($this->response)->acuse;
        } else if ($facturalo->errorCode(201)) {

            error_log("Folio pendiente de cancelación: " . $this->parameters['uuid']);
            return json_decode($this->response)->acuse;
        } else if ($facturalo->errorCode(202)) {

            error_log("Folio previamente cancelado: " . $this->parameters['uuid']);
            return json_decode($this->response)->acuse;
        }
        return false;
    }
}

class FacturaloCancelarPorPFXCaller extends FacturaloCancelarCaller {

    private static $operation = "CancelarPorPFX";

    public function call() {

        error_log("Cancelando comprobante. Invocando " . $this->pac->getUrl());
        parent::invoke(static::$operation);
        return $this;
    }
}

class FacturaloCancelarPorCSDCaller extends FacturaloCancelarCaller {

    private static $operation = "CancelarPorCSD";

    public function call() {

        error_log("Cancelando comprobante. Invocando " . $this->pac->getUrl());
        parent::invoke(static::$operation);
        return $this;
    }
}

class FacturaloConsultarCfdiRelacionadosPorPFXCaller extends FacturaloCaller {
    
    private static $operation = "ConsultarCfdiRelacionadosPorPFX";

    public function call() {

        error_log("Consultando CFDI relacionados.  Invocando " . $this->pac->getUrl());
        parent::invoke(static::$operation);
        return $this;
    }
}

class FacturaloConsultarCfdiRelacionadosPorCSDCaller extends FacturaloCaller {
    
    private static $operation = "ConsultarCfdiRelacionadosPorCSD";

    public function call() {

        error_log("Consultando CFDI relacionados.  Invocando " . $this->pac->getUrl());
        parent::invoke(static::$operation);
        return $this;
    }
}

class FacturaloService extends BasePACService {

    /**
     * Constructor de la clase
     * @param PAC $PAC VO con los datos del PAC
     */
    function __construct($PAC) {

        parent::__construct($PAC);
    }

    public function timbraComprobante($xmlCFDI) {

        $facturalo = (new FacturaloTimbrarV3Caller($this->PAC))
                ->setParameter("cfdi", $xmlCFDI)
                ->call();
        
        if ($facturalo->success()) {

            error_log("Timbrando correctamente");
            return json_decode($facturalo->getResponse())->cfdi;
        } else if ($facturalo->errorCode(307)) {

            error_log("Comprobante previamente timbrado");
            return $facturalo->getResponse("messageDetail");
        } else {
            
            $this->error = $facturalo->getHTMLError();
            error_log("ERROR " . $this->error);
        }
        return false;
    }

    public function getTimbre($xmlCFDI) {
        return timbraComprobante($xmlCFDI);
    }

    public function cancelaComprobantePFXBA($rfc, $uuid, $pass, $pfxBA) {

        $facturalo = (new FacturaloCancelarPorPFXCaller($this->PAC))
                ->setParameter("uuid", $uuid)
                ->setParameter("rfcEmisor", $rfc)
                ->setParameter("b64Pfx", base64_encode($pfxBA))
                ->setParameter("passwordPfx", $pass)
                ->call();

        if ($facturalo->success()) {

            return $facturalo->processCancelation();
        } else {

            $this->error = $facturalo->getHTMLError();
            error_log("ERROR " . $this->error);
        }
        return false;
    }

    public function cancelaComprobantePFX($rfc, $uuid, $pass, $pfx = 'certificado/pfx.pfx') {

        return $this->cancelaComprobantePFXBA($rfc, $uuid, $pass, file_get_contents($pfx));
    }

    public function cancelaComprobanteSignatureBA($rfcEmisor, $rfcReceptor, $total, $uuid, $pass, $cerBA, $keyBA) {

        $facturalo = (new FacturaloCancelarPorPFXCaller($this->PAC))
                ->setParameter("uuid", $uuid)
                ->setParameter("rfcEmisor", $rfc)
                ->setParameter("b64Cer", base64_encode($cerBA))
                ->setParameter("b64Key", base64_encode($keyBA))
                ->setParameter("passwordSAT", $pass)
                ->call();

        if ($facturalo->success()) {

            return $facturalo->processCancelation();
        } else {

            $this->error = $facturalo->getHTMLError();
            error_log("ERROR " . $this->error);
        }
        return false;
    }

    public function cancelaComprobanteSignature($rfcEmisor, $rfcReceptor, $total, $uuid, $pass, $cer = 'certificado/cer.cer', $key = 'certificado/key.key') {
        return $this->cancelaComprobanteSignatureBA($rfcEmisor, $rfcReceptor, $total, $uuid, $pass, file_get_contents($cer), file_get_contents($key));
    }

    public function getCFDIRelacionadosPFXBA($rfc, $uuid, $pass, $pfxBA) {

        $facturalo = (new FacturaloConsultarCfdiRelacionadosPorPFXCaller($this->PAC))
                ->setParameter("uuid", $uuid)
                ->setParameter("rfcReceptor", $rfc)
                ->setParameter("b64Pfx", base64_encode($pfxBA))
                ->setParameter("passwordPfx", $pass)
                ->call();

        if ($facturalo->success()) {
            
            error_log("Recuperando resultados");
            return json_decode($facturalo->getResponse());
        } else {

            $this->error = $facturalo->getHTMLError();
            error_log("ERROR " . $this->error);
        }
        return false;
    }

    public function getCFDIRelacionadosPFX($rfc, $uuid, $pass, $pfx = 'certificado/pfx.pfx') {
        return $this->getCFDIRelacionadosPFXBA($rfc, $uuid, $pass, file_get_contents($pfx));
    }

    public function getCFDIRelacionadosSignatureBA($rfc, $uuid, $pass, $cerBA, $keyBA) {

        $facturalo = (new FacturaloConsultarCfdiRelacionadosPorPFXCaller($this->PAC))
                ->setParameter("uuid", $uuid)
                ->setParameter("rfcReceptor", $rfc)
                ->setParameter("b64Cer", base64_encode($cerBA))
                ->setParameter("b64Key", base64_encode($keyBA))
                ->setParameter("passwordSAT", $pass)
                ->call();

        if ($facturalo->success()) {
            
            error_log("Recuperando resultados");
            return json_decode($facturalo->getResponse());
        } else {

            $this->error = $facturalo->getHTMLError();
            error_log("ERROR " . $this->error);
        }
        return false;
    }

    public function getCFDIRelacionadosSignature($rfc, $uuid, $pass, $cer = 'certificado/cer.cer', $key = 'certificado/key.key') {
        return $this->getCFDIRelacionadosSignatureBA($rfc, $uuid, $pass, file_get_contents($cer), file_get_contents($key));
    }

    public function getAcuseCancelacion($uuid) {
        throw new \Exception("Función getAcuseCancelacion no implementada por el Servicio Facturalo");
    }
}//FacturaloService
