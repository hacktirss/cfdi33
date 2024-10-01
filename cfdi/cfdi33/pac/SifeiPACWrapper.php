<?php

/*
 * SifeiPACWrapper
 * Clase wrapper del PAC con datos adicionales para SIFEI (PAC)
 * cfdi33®
 * ® 2017, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */

namespace com\softcoatl\cfdi\v33;

require_once 'PAC.php';

class SifeiPAC extends PAC {

    private $urlCancelacion; // URL de los servicios de cancelación
    private $Serie; // Serie de la licencia
    private $IdEquipo; // ID del equipo


    function __construct($url, $user, $password, $pac) {
        parent::__construct($url, $user, $password, $pac);
    }

    function getUrlCancelacion() {
        return $this->urlCancelacion;
    }

    function getSerie() {
        return $this->Serie;
    }

    function getIdEquipo() {
        return $this->IdEquipo;
    }

    function setUrlCancelacion($urlCancelacion) {
        $this->urlCancelacion = $urlCancelacion;
    }

    function setSerie($Serie) {
        $this->Serie = $Serie;
    }

    function setIdEquipo($IdEquipo) {
        $this->IdEquipo = $IdEquipo;
    }
}
