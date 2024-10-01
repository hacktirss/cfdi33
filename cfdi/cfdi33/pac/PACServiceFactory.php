<?php

/*
 * PACServiceFactory
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since ene 2018
 */

namespace com\softcoatl\cfdi\v33;

include_once ('SifeiService.php');
include_once ('FacturaloService.php');
include_once ('DFactureService.php');

class PACServiceFactory {

    /**
     * 
     * @param type $PAC
     * @return \cfdi33\PACService|boolean
     */
    public static function getPACService($PAC) {

        $pacKeyword = $PAC->getPac();
        switch ($pacKeyword) {
            case 'SIFEI':       return new SifeiService($PAC); 
            case 'MX_DFACTURE': return new DFactureService($PAC);
            case 'MULTIPAC': return new FacturaloService($PAC);
        }
        return FALSE;
    }
}//PACServiceFactory
