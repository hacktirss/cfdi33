<?php
/*
 * Observaciones
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */
namespace com\softcoatl\cfdi\v33\schema\Comprobante\addenda;

use \com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class Observaciones implements CFDIElement {

    private $Observaciones = array();

    function getObservaciones() {
        return $this->Observaciones;
    }

    /* @var $observaciones */
    function addObservaciones($observacion) {
        array_push($this->Observaciones, $observacion);
    }

    public function asJsonArray() {
        $observaciones = array();

        /* @var $retencion Observacion */
        foreach ($this->Observaciones as $observacion) {
            $observaciones[] = $observacion->asJsonArray();
        }

        return array ("Observaciones" => array("Observacion" => $observaciones));
    }

    /**
     * 
     * @param \DOMDocument $document
     * @param \DOMElement $root
     * @return \DOMNode
     */
    public function asXML($root) {

        $root->setAttribute("xmlns:deti", "http://detisa.omicrom/");
        $Observaciones = $root->ownerDocument->createElement("deti:Observaciones");
        $Observaciones->setAttribute("xsi:schemaLocation", "http://detisa.omicrom/ http://www.detisa.com.mx/detifac/detisa.xsd");
        /* @var $observacion Observacion */
        foreach ($this->Observaciones as $observacion) {
            $Observaciones->appendChild($observacion->asXML($root));
        }
        return $Observaciones;
    }

    /**
     * 
     * @param DOMElement $DOMObservaciones
     * @return \Observaciones
     */
    public static function parse($DOMObservaciones) {

        if (strpos($DOMObservaciones->nodeName, ':Observaciones')) {
            $Observaciones = new Observaciones();
            for($i=0; $i<$DOMObservaciones->childNodes->length; $i++) {

                /* @var $DOMObservacion DOMElement */
                $DOMObservacion = $DOMObservaciones->childNodes->item($i);
                if (strpos($DOMObservacion->nodeName, ':Observacion')) {

                    $Observaciones->addObservaciones(new Observaciones\Observacion($DOMObservacion->getAttribute("Descripcion")));
                }
            }
            return $Observaciones;
        }
        return false;
    }
}

namespace com\softcoatl\cfdi\v33\schema\Comprobante\addenda\Observaciones;

use \com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class Observacion implements CFDIElement {

    private $Descripcion;

    function __construct($descripcion) {
        $this->Descripcion = $descripcion;
    }

    function getDescripcion() {
        return $this->Descripcion;
    }

    function setDescripcion($descripcion) {
        $this->Descripcion = $descripcion;
    }

    public function asJsonArray() {
        return array_filter(get_object_vars($this));        
    }

    /**
     * 
     * @param \DOMDocument $document
     * @param \DOMElement $root
     * @return \DOMNode
     */
    public function asXML($root) {

        $Observacion = $root->ownerDocument->createElement("deti:Observacion");
        $Observacion->setAttribute("Descripcion", $this->Descripcion);
        return $Observacion;
    }

}
