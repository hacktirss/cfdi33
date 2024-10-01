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

use com\softcoatl\cfdi\v33\schema\Comprobante;
use com\softcoatl\utils\SOAPClient;

class SifeiException {

    private $sifeiException;

    function __construct($sifeiExcetion) {
        $this->sifeiException = $sifeiExcetion;
    }

    function exists() {
        return !empty($this->sifeiException);
    }
    function is($code) {
        return $this->code()==$code;
    }
    function code() {
        return $this->sifeiException['codigo'];
    }
    function error() {
        return $this->sifeiException['error'];
    }
    function message() {
        return $this->sifeiException['mensaje'];
    }
    function toHTML() {
        return "<b>ERROR REPORTADO POR EL SAT (" . $this->code() . ")<br/>" . $this->error() . ".</b><br>" . $this->message();
    }
}

abstract class SifeiCaller {

    public static $ns = "http://MApeados/";

    protected $pac;
    protected $parameters;
    protected $response;
    protected $soapClient;
    protected $soapException;
    protected $sifeException;

    function __construct($pac) {
        $this->pac = $pac;
        $this->parameters = array();
    }

    public abstract function call();

    protected function invoke($operation) {
        try {
            $this->response = $this->soapClient->call($operation, $this->parameters, static::$ns);
            $this->sifeException = new SifeiException($this->response['detail']['SifeiException']);
        } catch (\Exception $e) {
            $this->soapException = $e->getMessage();
        }

        return $this;
    }

    public function setParameter($key, $value) {

        $this->parameters[$key] = $value;
        return $this;
    }

    public function sifeiError() {
        return $this->sifeException->exists();
    }

    public function soapError() {
        return !empty($this->soapException);
    }

    public function success() {
        return !$this->sifeiError()
                && !$this->soapError();
    }

    public function errorCode($code) {
        return $this->sifeException->is($code);
    }

    public function getErrorCode() {
        return $this->sifeException->code();
    }

    public function getHTMLError() {
        return $this->sifeiError() ? 
                $this->sifeException->toHTML() : 
                "<b>ERROR<br/>" . explode("<br>", $this->soapException)[0];
    }

    public function getResponse($field = "return") {
        return $this->response[$field];
    }
}

class SifeiGetCFDICaller extends SifeiCaller {

    private static $operation = "getCFDI";

    function __construct($pac) {
        parent::__construct($pac);
        $this
            ->setParameter("Usuario", $this->pac->getUser())
            ->setParameter("Password", $this->pac->getPassword())
            ->setParameter("Serie", $this->pac->getSerie())
            ->setParameter("IdEquipo", $this->pac->getIdEquipo());  
        $this->soapClient = SOAPClient::getClient($this->pac->getUrl());
    }

    public function call() {

        error_log("Timbrando comprobante. Invocando " . $this->pac->getUrl());
        parent::invoke(static::$operation);
        return $this;
    }
}

class SifeiGetXMLCaller extends SifeiCaller {

    private static $operation = "getXML";

    function __construct($pac) {
        parent::__construct($pac);
        $this
            ->setParameter("rfc", $this->pac->getUser())
            ->setParameter("pass", $this->pac->getPassword());  
        $this->soapClient = SOAPClient::getClient($this->pac->getUrl());
    }

    public function call() {

        error_log("Recuperando timbre.  Invocando " . $this->pac->getUrl());
        parent::invoke(static::$operation);
        return $this;
    }
}

class SifeiCancelaCFDICaller extends SifeiCaller {
    
    private static $operation = "cancelaCFDI";

    function __construct($pac) {
        parent::__construct($pac);
        $this
            ->setParameter("usuarioSIFEI", $this->pac->getUser())
            ->setParameter("passwordSifei", $this->pac->getPassword());  
        $this->soapClient = SOAPClient::getClient($this->pac->getUrlCancelacion());
    }

    public function call() {

        error_log("Consultando CFDI Relacionados. Invocando " . $this->pac->getUrlCancelacion());
        parent::invoke(static::$operation);
        return $this;
    }
}

class SifeiCfdiRelacionadoCaller extends SifeiCaller {
    
    private static $operation = "cfdiRelacionado";

    function __construct($pac) {
        parent::__construct($pac);
        $this
            ->setParameter("usuarioSIFEI", $this->pac->getUser())
            ->setParameter("passwordSifei", $this->pac->getPassword());  
        $this->soapClient = SOAPClient::getClient($this->pac->getUrl());
    }

    public function call() {

        error_log("Cancelando comprobante. Invocando " . $this->pac->getUrlCancelacion());
        parent::invoke(static::$operation);
        return $this;
    }
}

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

        return false;
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

        return false;
    }//unzip

    /**
     * 
     * @param string $xmlCFDI XML del CFDI que será timbrado
     * @return boolean
     */
    public function timbraComprobante($xmlCFDI) {

        $sifei = (new SifeiGetCFDICaller($this->PAC))
                ->setParameter("archivoXMLZip", base64_encode($this->zip($xmlCFDI)))
                ->call();

        if ($sifei->success()) {

            error_log("Timbrando correctamente");
            return $this->unzip(base64_decode($sifei->getResponse()));
        } else if ($sifei->errorCode(307)) {

            error_log("Comprobante previamente timbrado.");
            return $this->getTimbre($xmlCFDI);
        } else {

            $this->error = $sifei->getHTMLError();
            error_log("ERROR " . $this->error);
        }

        return false;
    }//timbraComprobante

    /**
     * 
     * @param string $xmlCFDI XML del Timbre Fiscal Digital que será recuperado
     * @return string|boolean
     */
    public function getTimbre($xmlCFDI) {

        $sifei = (new SifeiGetXMLCaller($this->PAC))
                ->setParameter("hash", hash("sha256", Comprobante::getOriginalBytes($xmlCFDI)))
                ->call();
        if ($sifei->success()) {

            error_log("Timbre recuperado");
            $tfd =  new \DOMDocument("1.0","UTF-8");
            $tfd->loadXML($sifei->getResponse());
            $timbre = Comprobante\complemento\TimbreFiscalDigital::parse($tfd->childNodes->item(0));
            $comprobante = Comprobante::parse($xmlCFDI);
            $comprobante->addComplemento($timbre);
            return $comprobante->asXML()->saveXML();
        } else {
            
            $this->error = $sifei->getHTMLError();
            error_log("ERROR " . $this->error);
        }

        return false;
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

        $sifei = (new SifeiCancelaCFDICaller($this->PAC))
                ->setParameter("rfcEmisor", $rfc)
                ->setParameter("pfx", base64_encode($pfxBA))
                ->setParameter("passwordPfx", $pass)
                ->setParameter("uuids", array($uuid))
                ->call();
        if ($sifei->success()) {

            error_log("Comprobante ". $uuid . " cancelado");
            return $sifei->getResponse();
        } else {

            $this->error = $sifei->getHTMLError();            
            error_log("Error cancelando " . $this->error);
        }

        return false;
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

        $sifei = (new SifeiCfdiRelacionadoCaller($this->PAC))
                ->setParameter("rfcReceptor", $rfc)
                ->setParameter("uuid", $uuid)
                ->setParameter("pfx", base64_encode($pfxBA))
                ->setParameter("passwordPfx", $pass)
                ->call();

        if ($sifei->success()) {

            $respuesta = new \DOMDocument("1.0","UTF-8");
            $respuesta->loadXML($sifei->getResponse());
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
        } else {
            
            $this->error = $sifei->getHTMLError();            
            error_log("Error recuperando CFDI Relacionados " . $this->error);
        }

        return false;        
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
