<?php

/*
 * PACFactory
 * cfdi®
 * © 2018, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since ene 2018
 */

namespace com\softcoatl\cfdi\v33;

include_once ('PAC.php');
include_once ('SifeiPACWrapper.php');

class PACFactory {
    
    public static function getPAC($url, $user, $password, $pac) {

        switch ($pac) {
            case 'SIFEI' : return new SifeiPAC($url, $user, $password, $pac);
            default : return new PAC($url, $user, $password, $pac);
        }
    }//getPAC
}//PACFactory
