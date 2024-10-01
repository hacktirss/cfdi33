<?php
/*
 * IEDU Complemento para Instituciones Educativas
 * cfdi®
 * © 2018, Softcoatl
 * http://www.softcoatl.com.mx
 * @author Rolando Esquivel Villafaña
 * @version 1.0
 * @since oct 2018
 */
namespace com\softcoatl\cfdi\v33\schema\Comprobante\Conceptos\Concepto\complemento;

use com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class IEDU implements CFDIElement {

    private $version;
    private $nombreAlumno;
    private $CURP;
    private $nivelEducativo;
    private $autRVOE;
    private $rfcPago;

    function getVersion() {
        if ($this->Version == null) {
            return "1.0";
        } else {
            return $this->Version;
        }
    }

    function getNombreAlumno() {
        return $this->nombreAlumno;
    }

    function getCURP() {
        return $this->CURP;
    }

    function getNivelEducativo() {
        return $this->nivelEducativo;
    }

    function getAutRVOE() {
        return $this->autRVOE;
    }

    function getRfcPago() {
        return $this->rfcPago;
    }

    function setVersion($version) {
        $this->version = $version;
    }

    function setNombreAlumno($nombreAlumno) {
        $this->nombreAlumno = $nombreAlumno;
    }

    function setCURP($CURP) {
        $this->CURP = $CURP;
    }

    function setNivelEducativo($nivelEducativo) {
        $this->nivelEducativo = $nivelEducativo;
    }

    function setAutRVOE($autRVOE) {
        $this->autRVOE = $autRVOE;
    }

    function setRfcPago($rfcPago) {
        $this->rfcPago = $rfcPago;
    }

    public function asJsonArray() {
        return array_filter(get_object_vars($this));
    }

    public function asXML($root) {

        $IEDU = $root->ownerDocument->createElement("iedu:instEducativas");

        $IEDU->setAttribute("xsi:schemaLocation", "http://www.sat.gob.mx/iedu http://www.sat.gob.mx/sitio_internet/cfd/iedu/iedu.xsd");
        $IEDU->setAttribute("xmlns:iedu", "http://www.sat.gob.mx/iedu");

        $ov = array_filter(get_object_vars($this));
        foreach ($ov as $attr=>$value) {
            $IEDU->setAttribute($attr, $value);
        }

        return $IEDU;
    }

    /**
     * 
     * @param type $DOMIEDU
     * @return \cfdi33\complemento\IEDU
     */
   public static function parse($DOMIEDU) {

       $IEDU = new IEDU();
       \com\softcoatl\Reflection::setAttributes($IEDU, $DOMIEDU);
       return $IEDU;
   }
}//IEDU
