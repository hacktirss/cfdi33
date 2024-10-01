<?php
/*
 * OrderOfMagnitude
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */
namespace com\softcoatl\utils;

class OrderOfMagnitude {

    public static function get($number, $base) {
        $orderOfMagnitude = 0;
        for ($i = 10; $number/$i>1; $i*=$base) {
            $orderOfMagnitude++;
        }
        return $orderOfMagnitude;
    }
}
