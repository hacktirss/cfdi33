<?php
/*
 * RFCValidator
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since mar 2020
 */
namespace com\softcoatl\utils;

class RFCValidator {

    const DICCIONARIO = "0123456789ABCDEFGHIJKLMN&OPQRSTUVWXYZ Ñ";
    const GENERICO = "XAXX010101000";

    public static function personaFisica($rfc) {
        return RFCValidator::isGeneric($rfc)
                || (preg_match("^[A-Z&-]{4}[0-9]{2}(0[1-9]|1[0-2])([0-2][0-9]|3[0-1])[A-Z0-9]{3}", $rfc) 
                        && RFCValidator::digitoVerificador($rfc));
    }
    public static function personaMoral($rfc) {
        return preg_match("^[A-Z&-]{3}[0-9]{2}(0[1-9]|1[0-2])([0-2][0-9]|3[0-1])[A-Z0-9]{3}", $rfc) 
                && RFCValidator::digitoVerificador($rfc);
    }
    public static function isGeneric($rfc) {
        return $rfc===RFCValidator::GENERICO;
    }
    public static function digitoVerificador($rfc) {
        foreach (str_split($rfc) as $char) {
            $suma = $i === (strlen($rfc)-1) ? $suma%11 : $suma+strpos(RFCValidator::DICCIONARIO, $char)*(14-($i+++1));
        }
        return $suma === 0 ? '0' : ( 11-$suma === 10 ? 'A' : 11-$suma);
    }
}
