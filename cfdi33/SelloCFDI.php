<?php
/*
 * SelloCFDI
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */
namespace com\softcoatl\cfdi\v33;

use com\softcoatl\cfdi\v33\schema\Comprobante as Comprobante;
use com\softcoatl\cfdi\v33\security\Certificate as csd;

class SelloCFDI {

    /* @var $csd csd */
    private $csd;

    function __construct($csd) {

        $this->csd = $csd;
    }

    public function sellaComprobante($Comprobante) {

        $Comprobante->setNoCertificado($this->csd->getSerialNumber());
        $Comprobante->setCertificado($this->csd->getBase64Certificate());

        $originalBytes = Comprobante::getOriginalBytes($Comprobante->asXML()->saveXML());
        $signature = $this->csd->signature($originalBytes, "sha256WithRSAEncryption");
        $Comprobante->setSello($signature);
    }
}
