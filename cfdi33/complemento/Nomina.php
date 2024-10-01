<?php
/*
 * Nomina
 * cfdi®
 * © 2018, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2018
 */
namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento;

use com\softcoatl\cfdi\v33\schema\CFDIElement as CFDIElement;

class Nomina implements CFDIElement {

    private $emisor;
    private $receptor;
    private $percepciones;
    /* @var $deducciones Nomina\Deducciones */
    private $deducciones;
    private $otrosPagos;
    private $incapacidades;
    private $version = "1.2";
    private $tipoNomina;
    private $fechaPago;
    private $fechaInicialPago;
    private $fechaFinalPago;
    private $numDiasPagados;
    private $totalPercepciones;
    private $totalDeducciones;
    private $totalOtrosPagos;

    function getEmisor() {
        return $this->emisor;
    }

    function getReceptor() {
        return $this->receptor;
    }

    function getPercepciones() {
        return $this->percepciones;
    }

    function getDeducciones() {
        return $this->deducciones;
    }

    function getOtrosPagos() {
        return $this->otrosPagos;
    }

    function getIncapacidades() {
        return $this->incapacidades;
    }

    function getVersion() {
        return $this->version;
    }

    function getTipoNomina() {
        return $this->tipoNomina;
    }

    function getFechaPago() {
        return $this->fechaPago;
    }

    function getFechaInicialPago() {
        return $this->fechaInicialPago;
    }

    function getFechaFinalPago() {
        return $this->fechaFinalPago;
    }

    function getNumDiasPagados() {
        return $this->numDiasPagados;
    }

    function getTotalPercepciones() {
        return $this->totalPercepciones;
    }

    function getTotalDeducciones() {
        return $this->totalDeducciones;
    }

    function getTotalOtrosPagos() {
        return $this->totalOtrosPagos;
    }

    function setEmisor($emisor) {
        $this->emisor = $emisor;
    }

    function setReceptor($receptor) {
        $this->receptor = $receptor;
    }

    function setPercepciones($percepciones) {
        $this->percepciones = $percepciones;
    }

    function setDeducciones($deducciones) {
        $this->deducciones = $deducciones;
    }

    function setOtrosPagos($otrosPagos) {
        $this->otrosPagos = $otrosPagos;
    }

    function setIncapacidades($incapacidades) {
        $this->incapacidades = $incapacidades;
    }

    function setVersion($version) {
        $this->version = $version;
    }

    function setTipoNomina($tipoNomina) {
        $this->tipoNomina = $tipoNomina;
    }

    function setFechaPago($fechaPago) {
        $this->fechaPago = $fechaPago;
    }

    function setFechaInicialPago($fechaInicialPago) {
        $this->fechaInicialPago = $fechaInicialPago;
    }

    function setFechaFinalPago($fechaFinalPago) {
        $this->fechaFinalPago = $fechaFinalPago;
    }

    function setNumDiasPagados($numDiasPagados) {
        $this->numDiasPagados = $numDiasPagados;
    }

    function setTotalPercepciones($totalPercepciones) {
        $this->totalPercepciones = $totalPercepciones;
    }

    function setTotalDeducciones($totalDeducciones) {
        $this->totalDeducciones = $totalDeducciones;
    }

    function setTotalOtrosPagos($totalOtrosPagos) {
        $this->totalOtrosPagos = $totalOtrosPagos;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }
}//Nomina

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\Nomina;

class Deducciones implements CFDIElement {

    private $deduccion = array();
    private $totalOtrasDeducciones;
    private $totalImpuestosRetenidos;

    function getDeduccion() {
        return $this->deduccion;
    }

    function getTotalOtrasDeducciones() {
        return $this->totalOtrasDeducciones;
    }

    function getTotalImpuestosRetenidos() {
        return $this->totalImpuestosRetenidos;
    }

    function setDeduccion($deduccion) {
        $this->deduccion = $deduccion;
    }

    function setTotalOtrasDeducciones($totalOtrasDeducciones) {
        $this->totalOtrasDeducciones = $totalOtrasDeducciones;
    }

    function setTotalImpuestosRetenidos($totalImpuestosRetenidos) {
        $this->totalImpuestosRetenidos = $totalImpuestosRetenidos;
    }

    public function asJsonArray() {

    }

    public function asXML($root) {
        
    }
}

class Emisor implements CFDIElement {

    private $entidadSNCF;
    private $curp;
    private $registroPatronal;
    private $rfcPatronOrigen;

    function getEntidadSNCF() {
        return $this->entidadSNCF;
    }

    function getCurp() {
        return $this->curp;
    }

    function getRegistroPatronal() {
        return $this->registroPatronal;
    }

    function getRfcPatronOrigen() {
        return $this->rfcPatronOrigen;
    }

    function setEntidadSNCF($entidadSNCF) {
        $this->entidadSNCF = $entidadSNCF;
    }

    function setCurp($curp) {
        $this->curp = $curp;
    }

    function setRegistroPatronal($registroPatronal) {
        $this->registroPatronal = $registroPatronal;
    }

    function setRfcPatronOrigen($rfcPatronOrigen) {
        $this->rfcPatronOrigen = $rfcPatronOrigen;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }

}

class Incapacidades implements CFDIElement {

    private $incapacidad = array();

    function getIncapacidad() {
        return $this->incapacidad;
    }

    function setIncapacidad($incapacidad) {
        $this->incapacidad = $incapacidad;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }
}

class OtrosPagos implements CFDIElement {

    private $otroPago= array();

    function getOtroPago() {
        return $this->otroPago;
    }

    function setOtroPago($otroPago) {
        $this->otroPago = $otroPago;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }
}

class Percepciones implements CFDIElement {

    private $percepcion = array();
    private $jubilacionPensionRetiro;
    private $separacionIndemnizacion;
    private $totalSueldos;
    private $totalSeparacionIndemnizacion;
    private $totalJubilacionPensionRetiro;
    private $totalGravado;
    private $totalExento;

    function getPercepcion() {
        return $this->percepcion;
    }

    function getJubilacionPensionRetiro() {
        return $this->jubilacionPensionRetiro;
    }

    function getSeparacionIndemnizacion() {
        return $this->separacionIndemnizacion;
    }

    function getTotalSueldos() {
        return $this->totalSueldos;
    }

    function getTotalSeparacionIndemnizacion() {
        return $this->totalSeparacionIndemnizacion;
    }

    function getTotalJubilacionPensionRetiro() {
        return $this->totalJubilacionPensionRetiro;
    }

    function getTotalGravado() {
        return $this->totalGravado;
    }

    function getTotalExento() {
        return $this->totalExento;
    }

    function setPercepcion($percepcion) {
        $this->percepcion = $percepcion;
    }

    function setJubilacionPensionRetiro($jubilacionPensionRetiro) {
        $this->jubilacionPensionRetiro = $jubilacionPensionRetiro;
    }

    function setSeparacionIndemnizacion($separacionIndemnizacion) {
        $this->separacionIndemnizacion = $separacionIndemnizacion;
    }

    function setTotalSueldos($totalSueldos) {
        $this->totalSueldos = $totalSueldos;
    }

    function setTotalSeparacionIndemnizacion($totalSeparacionIndemnizacion) {
        $this->totalSeparacionIndemnizacion = $totalSeparacionIndemnizacion;
    }

    function setTotalJubilacionPensionRetiro($totalJubilacionPensionRetiro) {
        $this->totalJubilacionPensionRetiro = $totalJubilacionPensionRetiro;
    }

    function setTotalGravado($totalGravado) {
        $this->totalGravado = $totalGravado;
    }

    function setTotalExento($totalExento) {
        $this->totalExento = $totalExento;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }

}

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\Nomina\Deducciones;

class Deduccion implements CFDIElement{

    private $tipoDeduccion;
    private $clave;
    private $concepto;
    private $importe;

    function getTipoDeduccion() {
        return $this->tipoDeduccion;
    }

    function getClave() {
        return $this->clave;
    }

    function getConcepto() {
        return $this->concepto;
    }

    function getImporte() {
        return $this->importe;
    }

    function setTipoDeduccion($tipoDeduccion) {
        $this->tipoDeduccion = $tipoDeduccion;
    }

    function setClave($clave) {
        $this->clave = $clave;
    }

    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setImporte($importe) {
        $this->importe = $importe;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }
}

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\Nomina\Emisor;

class EntidadSNCF implements CFDIElement {

    /* @var $origenRecurso \COrigenRecurso */
    private $origenRecurso;
    private $montoRecursoPropio;
    
    function getOrigenRecurso() {
        return $this->origenRecurso;
    }

    function getMontoRecursoPropio() {
        return $this->montoRecursoPropio;
    }

    function setOrigenRecurso($origenRecurso) {
        $this->origenRecurso = $origenRecurso;
    }

    function setMontoRecursoPropio($montoRecursoPropio) {
        $this->montoRecursoPropio = $montoRecursoPropio;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    } 
}

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\Nomina\Incapacidades;

class Incapacidad implements CFDIElement {
    
    private $diasIncapacidad;
    /* @var $tipoIncapacidad \CTipoIncapacidad */
    private $tipoIncapacidad;
    private $importeMonetario;
    
    function getDiasIncapacidad() {
        return $this->diasIncapacidad;
    }

    function getTipoIncapacidad() {
        return $this->tipoIncapacidad;
    }

    function getImporteMonetario() {
        return $this->importeMonetario;
    }

    function setDiasIncapacidad($diasIncapacidad) {
        $this->diasIncapacidad = $diasIncapacidad;
    }

    function setTipoIncapacidad($tipoIncapacidad) {
        $this->tipoIncapacidad = $tipoIncapacidad;
    }

    function setImporteMonetario($importeMonetario) {
        $this->importeMonetario = $importeMonetario;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }

}

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\Nomina\OtrosPagos;

class OtroPago implements CFDIElement {

    private $subsidioAlEmpleo;
    private $compensacionSaldosAFavor;
    private $tipoOtroPago;
    private $clave;
    private $concepto;
    private $importe;
    
    function getSubsidioAlEmpleo() {
        return $this->subsidioAlEmpleo;
    }

    function getCompensacionSaldosAFavor() {
        return $this->compensacionSaldosAFavor;
    }

    function getTipoOtroPago() {
        return $this->tipoOtroPago;
    }

    function getClave() {
        return $this->clave;
    }

    function getConcepto() {
        return $this->concepto;
    }

    function getImporte() {
        return $this->importe;
    }

    function setSubsidioAlEmpleo($subsidioAlEmpleo) {
        $this->subsidioAlEmpleo = $subsidioAlEmpleo;
    }

    function setCompensacionSaldosAFavor($compensacionSaldosAFavor) {
        $this->compensacionSaldosAFavor = $compensacionSaldosAFavor;
    }

    function setTipoOtroPago($tipoOtroPago) {
        $this->tipoOtroPago = $tipoOtroPago;
    }

    function setClave($clave) {
        $this->clave = $clave;
    }

    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setImporte($importe) {
        $this->importe = $importe;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }
}

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\Nomina\OtrosPagos\OtroPago;

class CompensacionSaldosAFavor implements CFDIElement {

    private $saldoAFavor;
    private $año;
    private $remanenteSalFav;

    function getSaldoAFavor() {
        return $this->saldoAFavor;
    }

    function getAño() {
        return $this->año;
    }

    function getRemanenteSalFav() {
        return $this->remanenteSalFav;
    }

    function setSaldoAFavor($saldoAFavor) {
        $this->saldoAFavor = $saldoAFavor;
    }

    function setAño($año) {
        $this->año = $año;
    }

    function setRemanenteSalFav($remanenteSalFav) {
        $this->remanenteSalFav = $remanenteSalFav;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }
}

class SubsidioAlEmpleo implements CFDIElement {

    private $subsidioCausado;

    function getSubsidioCausado() {
        return $this->subsidioCausado;
    }

    function setSubsidioCausado($subsidioCausado) {
        $this->subsidioCausado = $subsidioCausado;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }
}

namespace com\softcoatl\cfdi\v33\schema\Comprobante\complemento\Nomina\Percepciones;

class JubilacionPensionRetiro implements CFDIElement {

    private $totalUnaExhibicion;
    private $totalParcialidad;
    private $montoDiario;
    private $ingresoAcumulable;
    private $ingresoNoAcumulable;
    
    function getTotalUnaExhibicion() {
        return $this->totalUnaExhibicion;
    }

    function getTotalParcialidad() {
        return $this->totalParcialidad;
    }

    function getMontoDiario() {
        return $this->montoDiario;
    }

    function getIngresoAcumulable() {
        return $this->ingresoAcumulable;
    }

    function getIngresoNoAcumulable() {
        return $this->ingresoNoAcumulable;
    }

    function setTotalUnaExhibicion($totalUnaExhibicion) {
        $this->totalUnaExhibicion = $totalUnaExhibicion;
    }

    function setTotalParcialidad($totalParcialidad) {
        $this->totalParcialidad = $totalParcialidad;
    }

    function setMontoDiario($montoDiario) {
        $this->montoDiario = $montoDiario;
    }

    function setIngresoAcumulable($ingresoAcumulable) {
        $this->ingresoAcumulable = $ingresoAcumulable;
    }

    function setIngresoNoAcumulable($ingresoNoAcumulable) {
        $this->ingresoNoAcumulable = $ingresoNoAcumulable;
    }

    public function asJsonArray() {
        
    }

    public function asXML($root) {
        
    }
}

class Percepcion implements CFDIElement {

    private $accionesOTitulos;
    private $horasExtra = array();
    private $tipoPercepcion;
    private $clave;
    private $concepto;
    private $importeGravado;
    private $importeExento;

    function getAccionesOTitulos() {
        return $this->accionesOTitulos;
    }

    function getHorasExtra() {
        return $this->horasExtra;
    }

    function getTipoPercepcion() {
        return $this->tipoPercepcion;
    }

    function getClave() {
        return $this->clave;
    }

    function getConcepto() {
        return $this->concepto;
    }

    function getImporteGravado() {
        return $this->importeGravado;
    }

    function getImporteExento() {
        return $this->importeExento;
    }

    function setAccionesOTitulos($accionesOTitulos) {
        $this->accionesOTitulos = $accionesOTitulos;
    }

    function setHorasExtra($horasExtra) {
        $this->horasExtra = $horasExtra;
    }

    function setTipoPercepcion($tipoPercepcion) {
        $this->tipoPercepcion = $tipoPercepcion;
    }

    function setClave($clave) {
        $this->clave = $clave;
    }

    function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    function setImporteGravado($importeGravado) {
        $this->importeGravado = $importeGravado;
    }

    function setImporteExento($importeExento) {
        $this->importeExento = $importeExento;
    }

    public function asJsonArray(){
    }            public

    function asXML($root) {
    } 
}