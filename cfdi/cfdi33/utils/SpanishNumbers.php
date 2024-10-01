<?php
/*
 * SpanishNumbers
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */
namespace com\softcoatl\utils;

require_once ('LanguageNumbers.php');

class SpanishNumbers implements LanguageNumbers {

    public static $MAXINDEX = 1000000000000000000000000;
    public static $LIMIT = 999999999999999999999999.99;

    private static $UNITS = array( "", "UN ", "DOS ", "TRES ",
            "CUATRO ", "CINCO ", "SEIS ", "SIETE ", "OCHO ", "NUEVE ", "DIEZ ",
            "ONCE ", "DOCE ", "TRECE ", "CATORCE ", "QUINCE ", "DIECISEIS ",
            "DIECISIETE ", "DIECIOCHO ", "DIECINUEVE ", "VEINTE " );

    private static $TENS = array ( "", "" ,"VENTI", "TREINTA", "CUARENTA",
            "CINCUENTA", "SESENTA", "SETENTA", "OCHENTA", "NOVENTA" );

    private static $HUNDREDS = array ( "", "CIENTO", "DOSCIENTOS",
            "TRESCIENTOS", "CUATROCIENTOS", "QUINIENTOS", "SEISCIENTOS",
            "SETECIENTOS", "OCHOCIENTOS", "NOVECIENTOS" );

    public static $MILLION = array( "", "", "MILLÓN ", "MIL ", "BILLÓN ", "MIL ", "TRILLÓN ", "MIL ", "QUADRILLÓN ", "MIL " );
    public static $MILLIONS = array ( "", "", "MILLONES ", "MIL ", "BILLONES ", "MIL ", "TRILLONES ", "MIL ", "QUADRILLONES ", "MIL " );

    public function getIniFactor() {
        return SpanishNumbers::$MAXINDEX;
    }

    public function getMaxNumber() {
        return SpanishNumbers::$LIMIT;
    }

    public function getHundreds($hundred) {
        if ($hundred>count(SpanishNumbers::$HUNDREDS)) throw new \Exception("Valor inválido para las centenas " . $hundred);
        return SpanishNumbers::$HUNDREDS[$hundred];
    }

    public function getTens($ten) {
        if ($ten>count(SpanishNumbers::$TENS)) throw new \Exception("Valor inválido para las decenas " . $ten);
        return SpanishNumbers::$TENS[$ten];
    }

    public function getUnits($unit) {
        if ($unit>count(SpanishNumbers::$UNITS)) throw new \Exception("Valor inválido para las unidades " . $unit);
        return SpanishNumbers::$UNITS[$unit];
    }

    public function getTensUnion($ten, $unit) {
        return $ten > 2 && $unit > 0 ? " Y " : "";        
    }

    public function getHundredsUnion($hundred, $ten, $unit) {
        return " ";
    }

    public function getOf() {
        return "DE ";
    }

    public function getZero() {
        return "CERO ";
    }

    public function getOneHundred() {
        return "CIEN ";
    }

    public function getOneThousand() {
        return "MIL ";        
    }

    public function getOneNillion($exponent, $print) {
        return $print ? SpanishNumbers::$MILLION[$exponent] : "";        
    }

    public function getNillions($exponent, $print) {
        return $print ? SpanishNumbers::$MILLIONS[$exponent] : "";        
    }

    public function forcePrintNextScale($current, $scale) {
        return  ("" != $current) 
                && ($scale==1e+21
                        || $scale==1e+15 
                        || $scale==1e+9 
                        || $scale==1e+3);        
    }
}//SpanishNumbers
