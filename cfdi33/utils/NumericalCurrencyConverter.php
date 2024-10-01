<?php
/*
 * NumericalCurrencyConverter
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */
namespace com\softcoatl\utils;

require_once ('NumberToLetterConverter.php');
require_once ('OrderOfMagnitude.php');

class NumericalCurrencyConverter implements NumberToLetterConverter {

    /* @var $language LanguageNumbers */
    private $language;
    /* @var $currency Currency */
    private $currency;

    function __construct($language, $currency) {
        $this->language = $language;
        $this->currency = $currency;
    }

    function getLanguage() {
        return $this->language;
    }

    function getCurrency() {
        return $this->currency;
    }

    private function formatTriad($number) {

        $letter = "";

        $hundred = intval($number/100);
        $ten     = intval(fmod($number, 100)/10);
        $unit    = intval(fmod($number, 10));

        if ($hundred + $ten + $unit == 0) {
            return "";
        }

        if ($hundred == 1 && ($ten + $unit) == 0) {
            return $this->language->getOneHundred();
        }

        $letter .= $this->language->getHundreds($hundred);
        $letter .= $this->language->getHundredsUnion($hundred, $ten, $unit);

        if ($number%100 <= 20) {
            $letter .= $this->language->getUnits($number%100);
        } else {
            $letter .= $this->language->getTens($ten);
            $letter .= $this->language->getTensUnion($ten, $unit);
            $letter .= $this->language->getUnits($unit);
        }
        return $letter;
    }//formatTriad

    public function convert($number) {
        
        $letter = "";
        $formatedTriad = "";
        $posfix = "";
        $decimalPart = "";

        $a = $number;
        $b = $number;
        $index = $this->language->getIniFactor();

        $next = false;

        if ($number>$this->language->getMaxNumber()) {
            throw new \Exception("El mayor número que puede convertirse es " . number_format($this->language->getMaxNumber(), 2, '.', ','));
        } else {
            while ($index>1) { // Iterate over defined triads
                $index = round($index/1000, 2);
                $a = intval($b/$index);
                $b = fmod($b, $index);
                $formatedTriad = $this->formatTriad(round($a));

                if ($index>1e+6) {
                    $posfix = $this->language->getNillions(OrderOfMagnitude::get($index, 1000), ""!=$formatedTriad || $next);
                } else if ($index==1e+6) {
                    $posfix = $a==1 ?
                            $this->language->getOneNillion(2, ""!=$formatedTriad || $next) :
                            $this->language->getNillions(2, ""!=$formatedTriad || $next);
                } else if ($index==1e+3) {
                    $posfix = ""==$formatedTriad ? "" : $this->language->getOneThousand();
                } else {
                    $posfix = (!$next && ""==$formatedTriad) ? $this->language->getOf() : "";
                }

                $letter .= $formatedTriad . $posfix;
                $next = $this->language->forcePrintNextScale($posfix, $index);
            }
        }

        $decimalPart = sprintf("%02d", number_format(round($number*100)%100, 0, '', ''));
        $letter .= ( strlen($letter)==0 ? $this->language->getZero() : "") 
            . " " . (round($number)==1 ? $this->currency->getSingularCurrency() : $this->currency->getPluralCurrency())
            . " " . $decimalPart . "/100";
        return $letter;
    }//convert
}//NumericalCurrencyConverter
