<?php
/*
 * ElectronicSignatureCertificate
 * Objeto del e.firma, requerida para presentar solicutudes de trámites y servicios.
 * cfdi®
 * © 2020, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since Jan 2020
 */
namespace com\softcoatl\security\commons;

class Commons {

    public static $PEMHeader = "-----BEGIN";
    public static $PEMFooter = "-----END";

    public static function x509CertificateToPEM($X509Certificate) {

        $pemCertificate = "";
        openssl_x509_export($X509Certificate, $pemCertificate);
        return $pemCertificate;
    }

    public static function privateKeyToPEM($PrivateKey) {

        $pemPrivateKey = "";
        openssl_pkey_export($PrivateKey, $pemPrivateKey);
        return $pemPrivateKey;
    }

    public static function pemToBase64($pemData) {

        $pemLines = explode("\n", $pemData);
        $b64 = "";
        foreach ($pemLines as $pemLine) {
            if (!strstr($pemLine, self::$PEMHeader) && !strstr($pemLine, self::$PEMFooter)) {
                $b64 .= $pemLine;
            }
        }
        return empty($b64) ? false : $b64;
    }

    public static function digest($digestData) {

        return base64_encode(sha1($digestData, true));        
    }

    public static function hex2dec($hex) {

        $dec = '';
        $i = -2;
        do {
            $dec .= chr(hexdec(substr($hex, $i+=2, 2)));
        } while ($i<strlen($hex)-2);

        return $dec;
    } 

    public static function int2hex($dec) {

        $hex = "";
        if (substr($dec, 0, 2)==="0x") {
            $hex = substr($dec, 2);
        } else {

            do {    
                $last = bcmod($dec, 16);
                $hex = dechex($last).$hex;
                $dec = bcdiv(bcsub($dec, $last), 16);
            } while($dec>0);
        }
        return $hex;
    }
}
