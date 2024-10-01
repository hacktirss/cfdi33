<?php
/*
 * Currency
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */
namespace com\softcoatl\utils;

class Currency {

    private $pluralCurrency;
    private $singularCurrency;

    function __construct($pluralCurrency, $singularCurrency) {
        $this->pluralCurrency = $pluralCurrency;
        $this->singularCurrency = $singularCurrency;
    }

    function getPluralCurrency() {
        return $this->pluralCurrency;
    }

    function getSingularCurrency() {
        return $this->singularCurrency;
    }

    function setPluralCurrency($pluralCurrency) {
        $this->pluralCurrency = $pluralCurrency;
    }

    function setSingularCurrency($singularCurrency) {
        $this->singularCurrency = $singularCurrency;
    }    
}//Currency
