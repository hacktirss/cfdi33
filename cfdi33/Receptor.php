<?php
/*
 * Receptor
 * cfdi33®
 * ® 2017, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since nov 2017
 */
namespace com\softcoatl\cfdi\v33\schema\Comprobante;

require_once (dirname(__FILE__)."/CFDIElement.php");

use com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class Receptor implements CFDIElement {

    private $Rfc;
    private $Nombre;
    private $ResidenciaFiscal;
    private $NumRegIdTrib;
    private $UsoCFDI;

    function getRfc() {
        return $this->Rfc;
    }

    function getNombre() {
        return $this->Nombre;
    }

    function getResidenciaFiscal() {
        return $this->ResidenciaFiscal;
    }

    function getNumRegIdTrib() {
        return $this->NumRegIdTrib;
    }

    function getUsoCFDI() {
        return $this->UsoCFDI;
    }

    function setRfc($Rfc) {
        $this->Rfc = $Rfc;
    }

    function setNombre($Nombre) {
        $this->Nombre = $Nombre;
    }

    function setResidenciaFiscal($ResidenciaFiscal) {
        $this->ResidenciaFiscal = $ResidenciaFiscal;
    }

    function setNumRegIdTrib($NumRegIdTrib) {
        $this->NumRegIdTrib = $NumRegIdTrib;
    }

    function setUsoCFDI($UsoCFDI) {
        $this->UsoCFDI = $UsoCFDI;
    }

    public function asJsonArray() {
        return array_filter(get_object_vars($this));        
    }

    /**
     * 
     * @param \DOMElement $root
     * @return \DOMNode
     */
    public function asXML($root) {

        $Receptor = $root->ownerDocument->createElement("cfdi:Receptor");
        $ov = array_filter(get_object_vars($this));
        foreach ($ov as $attr => $value) {
            $Receptor->setAttribute($attr, $value);
        }
        
        return $Receptor;
    }

    /**
     * 
     * @param \DOMElement $DOMReceptor
     * @return \cfdi33\Comprobante\Receptor
     */
    public static function parse($DOMReceptor) {

        $Receptor = new Receptor();
        \com\softcoatl\utils\Reflection::setAttributes($Receptor, $DOMReceptor);
        return $Receptor;
    }
}
