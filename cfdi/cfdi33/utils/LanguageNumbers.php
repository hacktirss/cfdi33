<?php
/*
 * LanguageNumbers
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */
namespace com\softcoatl\utils;

interface LanguageNumbers {

    
    public function getIniFactor();
    public function getMaxNumber();

    public function forcePrintNextScale($current, $scale);

    public function getHundreds($hundred);
    public function getTens($ten);
    public function getUnits($unit);

    public function getHundredsUnion($hundred, $ten, $unit);
    public function getTensUnion($ten, $unit);

    public function getOf();
    public function getZero();

    public function getOneHundred();
    public function getOneThousand();

    public function getOneNillion($exponent, $print);
    public function getNillions($exponent, $print);
}
