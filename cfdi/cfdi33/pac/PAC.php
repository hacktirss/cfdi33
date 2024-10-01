<?php

/*
 * PAC
 * VO del Prooveedor Autorizado de Certificación (PAC)
 * cfdi33®
 * ® 2017, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */

namespace com\softcoatl\cfdi\v33;

class PAC {

    private $pac; // Clave del PAC
    private $url; // URL del servicio
    private $urlCancelacion; // URL del servicio de cancelación
    private $user; // Usuario del servicio
    private $password; // Password del usuario

    function __construct($url = '', $user = '', $password = '', $pac = '') {
        $this->url = $url;
        $this->user = $user;
        $this->password = $password;
        $this->pac = $pac;
    }

    function getUrl() {
        return $this->url;
    }

    function getUrlCancelacion() {
        return $this->urlCancelacion;
    }

    function getUser() {
        return $this->user;
    }

    function getPassword() {
        return $this->password;
    }

    function getPac() {
        return $this->pac;
    }

    function setUrl($url) {
        $this->url = $url;
    }

    function setUrlCancelacion($urlCancelacion) {
        $this->urlCancelacion = $urlCancelacion;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setPac($pac) {
        $this->pac = $pac;
    }

    function setPassword($password) {
        $this->password = $password;
    }

}//PAC
