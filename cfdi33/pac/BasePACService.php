<?php

/*
 * BasePACService
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since ene 2018
 */

namespace com\softcoatl\cfdi\v33;

require_once ("PACService.php");

abstract class BasePACService implements PACService {

    /* @var $PAC PAC */
    protected $PAC;
    protected $error;
    protected $errorSeparator = "\n";

    function __construct($PAC) {
        $this->PAC = $PAC;
    }

    function getPAC() {
        return $this->PAC;
    }

    function getError() {
        return $this->error;
    }

    function setPAC($PAC) {
        $this->PAC = $PAC;
    }
}//BasePACService