<?php
/* 
 * INE
 * cfdi®
 * © 2018, Softcoatl
 * http://www.softcoatl.com.mx
 * @author Rolando Esquivel Villafaña
 * @version 1.0
 * @since may 2018
 */
namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento;

use \com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class INE implements CFDIElement {

    private $Entidad = array();
    private $Version;
    private $TipoProceso;
    private $TipoComite;
    private $IdContabilidad;

    function getEntidad() {
        return $this->Entidad;
    }

    function getVersion() {
        if ($this->Version == null) {
            return "1.1";
        } else {
            return $this->Version;
        }
    }

    function getTipoProceso() {
        return $this->TipoProceso;
    }

    function getTipoComite() {
        return $this->TipoComite;
    }

    function getIdContabilidad() {
        return $this->IdContabilidad;
    }

    function addEntidad($entidad) {
        array_push($this->Entidad, $entidad);
    }

    function setEntidad($entidad) {
        $this->Entidad = $entidad;
    }

    function setVersion($version) {
        $this->Version = $version;
    }

    function setTipoProceso($tipoProceso) {
        $this->TipoProceso = $tipoProceso;
    }

    function setTipoComite($tipoComite) {
        $this->TipoComite = $tipoComite;
    }

    function setIdContabilidad($idContabilidad) {
        $this->IdContabilidad = $idContabilidad;
    }

    public function asJsonArray() {
        $ov = array_filter(get_object_vars($this), 
                        function ($val) { 
                            return !is_array($val) && !empty($val);
                        });
        $entidades = array();

        /* @var $entidades Entidad */
        foreach ($this->Entidad as $entidad) {
            $entidades[] = $entidad->asJsonArray();
        }

        $ov["Entidad"] = $entidades;

        return array("INE" => $ov);
    }

    public function asXML($root) {

        $root->setAttribute("xmlns:ine", "http://www.sat.gob.mx/ine");
        $INE = $root->ownerDocument->createElement("ine:INE");

        $ov = array_filter(get_object_vars($this), 
                        function ($val) { 
                            return !is_array($val) && !empty($val);
                        });
        foreach ($ov as $attr=>$value) {
            $INE->setAttribute($attr, $value);
        }

        /* @var $entidad Entidad */
        foreach ($this->Entidad as $entidad) {
            $INE->appendChild($entidad->asXML($root));
        }
        return $INE;
    }

    /**
     * 
     * @param \DOMElement $DOMIne
     * @return \cfdi33\complemento\INE
     */
    public static function parse($DOMIne) {

        if (strpos($DOMIne->nodeName, ':INE')) {
            $INE = new \cfdi33\complemento\INE();

            \com\softcoatl\Reflection::setAttributes($INE, $DOMIne);

            for ($i=0; $i<$DOMIne->childNodes->length; $i++) {
                /* @var $DOMIne \DOMElement */
                $DOMEntidad = $DOMIne->childNodes->item($i);
                if (strpos($DOMEntidad->nodeName, ':Entidad')) {

                    $Entidad = new INE\Entidad();
                    \com\softcoatl\Reflection::setAttributes($Entidad, $DOMEntidad);
                    for ($j=0; $j<$DOMEntidad->childNodes->length; $j++) {

                        /* @var $DOMContabilidad \DOMElement */
                        $DOMContabilidad = $DOMEntidad->childNodes->item($j);
                        if (strpos($DOMContabilidad->nodeName, ':Contabilidad')) {

                            $Contabilidad = new INE\Entidad\Contabilidad();
                            \com\softcoatl\Reflection::setAttributes($Contabilidad, $DOMContabilidad);
                            $Entidad->addContabilidad($Contabilidad);
                        }
                    }
                    $INE->addEntidad($Entidad);
                }
            }
            return $INE;
        }
        return false;
    }
}

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\INE;

use \com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class Entidad implements CFDIElement {

    private $Contabilidad = array();
    private $ClaveEntidad;
    private $Ambito;

    function getContabilidad() {
        return $this->Contabilidad;
    }

    function getClaveEntidad() {
        return $this->ClaveEntidad;
    }

    function getAmbito() {
        return $this->Ambito;
    }

    function addContabilidad($contabilidad) {
        array_push($this->Contabilidad, $contabilidad);
    }

    function setContabilidad($contabilidad) {
        $this->Contabilidad = $contabilidad;
    }

    function setClaveEntidad($claveEntidad) {
        $this->ClaveEntidad = $claveEntidad;
    }

    function setAmbito($ambito) {
        $this->Ambito = $ambito;
    }

    public function asJsonArray() {
        $ov = array_filter(get_object_vars($this), 
                        function ($val) { 
                            return !is_array($val) && !empty($val);
                        });
        $contabilidades = array();

        /* @var $contabilidades Contabilidad */
        foreach ($this->Contabilidad as $contabilidad) {
            $contabilidades[] = $contabilidad->asJsonArray();
        }

        $ov["Contabilidad"] = $contabilidades;

        return $ov;
    }

    public function asXML($root) {

        $Entidad = $root->ownerDocument->createElement("ine:Entidad");
        $ov = $this->getVarArray();
        foreach ($ov as $attr=>$value) {
            $Entidad->setAttribute($attr, $value);
        }

        if ($this->Contabilidad !== NULL) {
            /* @var $contab Contabilidad */
            foreach ($this->Contabilidad as $contab) {
                $Entidad->appendChild($contab->asXML($root));
            }
        }

        return $Entidad;
    }

}

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\INE\Entidad;

use \com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class Contabilidad implements CFDIElement {

    private $IdContabilidad;

    function getIdContabilidad() {
        return $this->IdContabilidad;
    }

    function setIdContabilidad($idContabilidad) {
        $this->IdContabilidad = $idContabilidad;
    }

    public function asJsonArray() {
        $ov = array_filter(get_object_vars($this), 
                        function ($val) { 
                            return !is_array($val) && !empty($val);
                        });
        return $ov;
    }

    public function asXML($root) {
        $Contabilidad = $root->ownerDocument->createElement("ine:Contabilidad");
        $Contabilidad->setAttribute("IdContabilidad", $this->IdContabilidad);
        return $Contabilidad;
    }

}