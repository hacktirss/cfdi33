<?php
/*
 * Emisor
 * cfdi33®
 * ® 2017, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since nov 2017
 */
namespace com\softcoatl\cfdi\v33\schema\Comprobante;

use \com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class Emisor implements CFDIElement {

    private $Rfc;
    private $Nombre;
    private $RegimenFiscal;

    function getRfc() {
        return $this->Rfc;
    }

    function getNombre() {
        return $this->Nombre;
    }

    function getRegimenFiscal() {
        return $this->RegimenFiscal;
    }

    function setRfc($Rfc) {
        $this->Rfc = $Rfc;
    }

    function setNombre($Nombre) {
        $this->Nombre = $Nombre;
    }

    function setRegimenFiscal($RegimenFiscal) {
        $this->RegimenFiscal = $RegimenFiscal;
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

        $Emisor = $root->ownerDocument->createElement("cfdi:Emisor");
        $ov = array_filter(get_object_vars($this));
        foreach ($ov as $attr => $value) {
            $Emisor->setAttribute($attr, $value);
        }
        return $Emisor;
    }

    /**
     * 
     * @param \DOMElement $DOMEmisor
     * @return \cfdi33\Comprobante\Emisor
     */
    public static function parse($DOMEmisor) {

        $Emisor = new Emisor();
        \com\softcoatl\utils\Reflection::setAttributes($Emisor, $DOMEmisor);
        return $Emisor;
    }
}//cfdi33\Comprobante\Emisor
