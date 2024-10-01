<?php
/*
 * Pagos
 * cfdi33®
 * ® 2017, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since nov 2017
 */
namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento;

use \com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class Pagos implements CFDIElement {

    private $Version = '1.0';
    private $Pagos = array();

    function getVersion() {
        return $this->Version;
    }

    function getPagos() {
        return $this->Pagos;
    }

    function setVersion($version) {
        $this->Version = $version;
    }

    function addPagos($pago) {
        array_push($this->Pagos, $pago);
    }

    public function asJsonArray() {
        $ov = array_filter(get_object_vars($this), 
                        function ($val) { 
                            return !is_array($val) && !empty($val);
                        });
        $pagos = array();

        /* @var $pago DoctoRelacionado */
        foreach ($this->Pagos as $pago) {
            $pagos[] = $pago->asJsonArray();
        }

        $ov["Pago"] = $pagos;

        return array("Pagos" => $ov);
    }//Pagos::asArray

    /**
     * 
     * @param \DOMElement $root
     * @return \DOMNode
     */
    public function asXML($root) {

        $root->setAttribute("xmlns:pago10", "http://www.sat.gob.mx/Pagos");
        $root->setAttribute("xsi:schemaLocation", $root->getAttribute("xsi:schemaLocation") . " http://www.sat.gob.mx/Pagos http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd");
        $Pagos = $root->ownerDocument->createElement("pago10:Pagos");
        $Pagos->setAttribute("Version", $this->Version);
        /* @var $pago Pagos\Pago */
        foreach ($this->Pagos as $pago) {
            $Pagos->appendChild($pago->asXML($root));
        }
        return $Pagos;
    }

    /**
     * 
     * @param \DOMElement $DOMPagos
     * @return \cfdi33\complemento\Pagos
     */
    public static function parse($DOMPagos) {

        if (strpos($DOMPagos->nodeName, ':Pagos')) {
            $Pagos = new Pagos();
            $Pagos->setVersion($DOMPagos->getAttribute('Version'));

            for ($i=0; $i<$DOMPagos->childNodes->length; $i++) {
                /* @var $DOMPago \DOMElement */
                $DOMPago = $DOMPagos->childNodes->item($i);
                if (strpos($DOMPago->nodeName, ':Pago')) {

                    $Pago = new Pagos\Pago();
                    \com\softcoatl\utils\Reflection::setAttributes($Pago, $DOMPago);
                    for ($j=0; $j<$DOMPago->childNodes->length; $j++) {

                        /* @var $DOMDoctoRelacionado \DOMElement */
                        $DOMDoctoRelacionado = $DOMPago->childNodes->item($j);
                        if (strpos($DOMDoctoRelacionado->nodeName, ':DoctoRelacionado')) {

                            $DoctoRelacionado = new Pagos\Pago\DoctoRelacionado();
                            \com\softcoatl\utils\Reflection::setAttributes($DoctoRelacionado, $DOMDoctoRelacionado);
                            $Pago->addDoctoRelacionado($DoctoRelacionado);
                        }
                    }
                    $Pagos->addPagos($Pago);
                }
            }
            return $Pagos;
        }
        return false;
    }
}//cfdi33\complemento\Pago

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\Pagos;

use \com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class Pago implements CFDIElement {

    private $DoctoRelacionado = array();
    private $Impuestos = array();

    private $FechaPago;
    private $FormaDePagoP;
    private $MonedaP;
    private $TipoCambioP;
    private $Monto;
    private $NumOperacion;
    private $RfcEmisorCtaOrd;
    private $NomBancoOrdExt;
    private $CtaOrdenante;
    private $RfcEmisorCtaBen;
    private $CtaBeneficiario;
    private $TipoCadPago;
    private $CertPago;
    private $CadPago;
    private $SelloPago;

    function getDoctoRelacionado() {
        return $this->DoctoRelacionado;
    }

    function getImpuestos() {
        return $this->Impuestos;
    }

    function getFechaPago() {
        return $this->FechaPago;
    }

    function getFormaDePagoP() {
        return $this->FormaDePagoP;
    }

    function getMonedaP() {
        return $this->MonedaP;
    }

    function getTipoCambioP() {
        return $this->TipoCambioP;
    }

    function getMonto() {
        return $this->Monto;
    }

    function getNumOperacion() {
        return $this->NumOperacion;
    }

    function getRfcEmisorCtaOrd() {
        return $this->RfcEmisorCtaOrd;
    }

    function getNomBancoOrdExt() {
        return $this->NomBancoOrdExt;
    }

    function getCtaOrdenante() {
        return $this->CtaOrdenante;
    }

    function getRfcEmisorCtaBen() {
        return $this->RfcEmisorCtaBen;
    }

    function getCtaBeneficiario() {
        return $this->CtaBeneficiario;
    }

    function getTipoCadPago() {
        return $this->TipoCadPago;
    }

    function getCertPago() {
        return $this->CertPago;
    }

    function getCadPago() {
        return $this->CadPago;
    }

    function getSelloPago() {
        return $this->SelloPago;
    }

    function setDoctoRelacionado($DoctoRelacionado) {
        $this->DoctoRelacionado = $DoctoRelacionado;
    }

    function addDoctoRelacionado($DoctoRelacionado) {
        array_push($this->DoctoRelacionado, $DoctoRelacionado);
    }

    function addImpuestos($Impuestos) {
        array_push($this->Impuestos, $Impuestos);
    }

    function setFechaPago($FechaPago) {
        $this->FechaPago = $FechaPago;
    }

    function setFormaDePagoP($FormaDePagoP) {
        $this->FormaDePagoP = $FormaDePagoP;
    }

    function setMonedaP($MonedaP) {
        $this->MonedaP = $MonedaP;
    }

    function setTipoCambioP($TipoCambioP) {
        $this->TipoCambioP = $TipoCambioP;
    }

    function setMonto($Monto) {
        $this->Monto = $Monto;
    }

    function setNumOperacion($NumOperacion) {
        $this->NumOperacion = $NumOperacion;
    }

    function setRfcEmisorCtaOrd($RfcEmisorCtaOrd) {
        $this->RfcEmisorCtaOrd = $RfcEmisorCtaOrd;
    }

    function setNomBancoOrdExt($NomBancoOrdExt) {
        $this->NomBancoOrdExt = $NomBancoOrdExt;
    }

    function setCtaOrdenante($CtaOrdenante) {
        $this->CtaOrdenante = $CtaOrdenante;
    }

    function setRfcEmisorCtaBen($RfcEmisorCtaBen) {
        $this->RfcEmisorCtaBen = $RfcEmisorCtaBen;
    }

    function setCtaBeneficiario($CtaBeneficiario) {
        $this->CtaBeneficiario = $CtaBeneficiario;
    }

    function setTipoCadPago($TipoCadPago) {
        $this->TipoCadPago = $TipoCadPago;
    }

    function setCertPago($CertPago) {
        $this->CertPago = $CertPago;
    }

    function setCadPago($CadPago) {
        $this->CadPago = $CadPago;
    }

    function setSelloPago($SelloPago) {
        $this->SelloPago = $SelloPago;
    }

    private function getVarArray() {
        return array_filter(get_object_vars($this), 
            function ($val) { 
                return !is_array($val) 
                    && ($val === '0' || $val === 0 || $val === 0.0 ||  !empty($val));
            });
    }

    public function asJsonArray() {

        $ov = $this->getVarArray();

        if ($this->DoctoRelacionado != NULL) {
            $doctoRelacionado = array();
            /* @var $docto DoctoRelacionado */
            foreach ($this->DoctoRelacionado as $docto) {
                $doctoRelacionado[] = $docto->asJsonArray();
            }

            $ov["DoctoRelacionado"] = $doctoRelacionado;
        }

        if ($this->Impuestos != NULL) {
            $impuestos = array();
            /* @var $docto DoctoRelacionado */
            foreach ($this->Impuestos as $impuesto) {
                $impuestos[] = $impuesto->asJsonArray();
            }

            $ov["Impuestos"] = $impuestos;
        }

        return $ov;
    }//Pago::asJsonArray

    /**
     * 
     * @param \DOMElement $root
     * @return \DOMNode
     */
    public function asXML($root) {

        $Pago = $root->ownerDocument->createElement("pago10:Pago");
        $ov = $this->getVarArray();
        foreach ($ov as $attr=>$value) {
            $Pago->setAttribute($attr, $value);
        }

        if ($this->DoctoRelacionado !== NULL) {
            /* @var $docto Pago\DoctoRelacionado */
            foreach ($this->DoctoRelacionado as $docto) {
                $Pago->appendChild($docto->asXML($root));
            }
        }

        return $Pago;
    }//Pago

}//Pago

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\Pagos\Pago;

use \com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class DoctoRelacionado implements CFDIElement {

    private $IdDocumento;
    private $Serie;
    private $Folio;
    private $MonedaDR;
    private $TipoCambioDR;
    private $MetodoDePagoDR;
    private $NumParcialidad;
    private $ImpSaldoAnt;
    private $ImpPagado;
    private $ImpSaldoInsoluto;

    function getIdDocumento() {
        return $this->IdDocumento;
    }

    function getSerie() {
        return $this->Serie;
    }

    function getFolio() {
        return $this->Folio;
    }

    function getMonedaDR() {
        return $this->MonedaDR;
    }

    function getTipoCambioDR() {
        return $this->TipoCambioDR;
    }

    function getMetodoDePagoDR() {
        return $this->MetodoDePagoDR;
    }

    function getNumParcialidad() {
        return $this->NumParcialidad;
    }

    function getImpSaldoAnt() {
        return $this->ImpSaldoAnt;
    }

    function getImpPagado() {
        return $this->ImpPagado;
    }

    function getImpSaldoInsoluto() {
        return $this->ImpSaldoInsoluto;
    }

    function setIdDocumento($IdDocumento) {
        $this->IdDocumento = $IdDocumento;
    }

    function setSerie($Serie) {
        $this->Serie = $Serie;
    }

    function setFolio($Folio) {
        $this->Folio = $Folio;
    }

    function setMonedaDR($MonedaDR) {
        $this->MonedaDR = $MonedaDR;
    }

    function setTipoCambioDR($TipoCambioDR) {
        $this->TipoCambioDR = $TipoCambioDR;
    }

    function setMetodoDePagoDR($MetodoDePagoDR) {
        $this->MetodoDePagoDR = $MetodoDePagoDR;
    }

    function setNumParcialidad($NumParcialidad) {
        $this->NumParcialidad = $NumParcialidad;
    }

    function setImpSaldoAnt($ImpSaldoAnt) {
        $this->ImpSaldoAnt = $ImpSaldoAnt;
    }

    function setImpPagado($ImpPagado) {
        $this->ImpPagado = $ImpPagado;
    }

    function setImpSaldoInsoluto($ImpSaldoInsoluto) {
        $this->ImpSaldoInsoluto = $ImpSaldoInsoluto;
    }

    public function asJsonArray() {
        return array_filter(get_object_vars($this));
    }//DoctoRelacionado::asArray

    /**
     * 
     * @param \DOMElement $root
     * @return \DOMNode
     */
    public function asXML($root) {

        $root->setAttribute("xmlns:pago10", "http://www.sat.gob.mx/Pagos");
        $DoctoRelacionado = $root->ownerDocument->createElement("pago10:DoctoRelacionado");

        $ov = array_filter(get_object_vars($this));
        foreach ($ov as $attr=>$value) {
            $DoctoRelacionado->setAttribute($attr, $value);
        }
        return $DoctoRelacionado;
    }

}//DoctoRelacionado