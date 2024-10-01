<?php

namespace com\softcoatl\cfdi\v33;

class X509 {
    private $serialNumber;
    private $validFrom;
    private $validTo;
    private $certificate;
    private $issuer;

    function getSerialNumber() {
        return $this->serialNumber;
    }

    function getValidFrom() {
        return $this->validFrom;
    }

    function getValidTo() {
        return $this->validTo;
    }

    function getCertificate() {
        return $this->certificate;
    }

    function getIssuer() {
        return $this->issuer;
    }

    function setSerialNumber($serialNumber) {
        $this->serialNumber = $serialNumber;
    }

    function setValidFrom($validFrom) {
        $this->validFrom = $validFrom;
    }

    function setValidTo($validTo) {
        $this->validTo = $validTo;
    }

    function setCertificate($certificate) {
        $this->certificate = $certificate;
    }

    function setIssuer($issuer) {
        $this->issuer = $issuer;
    }

    public function validateVigencia() {
        if (!isset($this->from) || !isset($this->to)) {
            error_log("No es posible validar la vigencia del certificado");
            return true;
        }
        $now = new \DateTime();
        return $now >= $this->from && $now <= $this->to;
        
    }
    public function printVigencia() {
        if (isset($this->validFrom) && isset($this->validTo)) {
            return "Del " . mb_convert_case(strftime('%A %d de %B de %Y', $this->validFrom->getTimestamp()), MB_CASE_TITLE, "UTF-8") 
                    . " al " . mb_convert_case(strftime('%A %d de %B de %Y', $this->validTo->getTimestamp()), MB_CASE_TITLE, "UTF-8");
        }
    }
    public function printTo() {
        if (isset($this->validTo)) {
            return mb_convert_case(strftime('%A %d de %B de %Y', $this->validTo->getTimestamp()), MB_CASE_TITLE, "UTF-8");
        }
    }
    public function printFrom() {
        if (isset($this->validFrom)) {
            return mb_convert_case(strftime('%A %d de %B de %Y', $this->validFrom->getTimestamp()), MB_CASE_TITLE, "UTF-8");
        }
    }
    
    public function __toString() {
        return print_r($this, TRUE);
    }
}

class OpenSSL {

    /**
     * 
     * @param type $cerData
     * @return \cfdi33\X509
     */
    public static function loadX509($cerData) {
        $cerpem = openssl_x509_parse($cerData, TRUE);
        $x509 = new X509();
        if (array_key_exists("serialNumberHex", $cerpem)) {
            $x509->setSerialNumber(strtoupper(self::hex2dec($cerpem['serialNumberHex'])));
        } else {
            $x509->setSerialNumber(strtoupper(self::hex2dec(self::int2hex($cerpem['serialNumber']))));
        }
        if (array_key_exists("issuer", $cerpem)) {
            $x509->setIssuer($cerpem['issuer']['O']);
        }
        if (array_key_exists("validFrom_time_t", $cerpem) && array_key_exists("validTo_time_t", $cerpem)) {
            $x509->setValidFrom(new \DateTime("@" . $cerpem['validFrom_time_t']));
            $x509->setValidTo(new \DateTime("@" . $cerpem['validTo_time_t']));
        }
        $cerLines = explode("\n", $cerData);
        $certificate = "";
        foreach ($cerLines as $cerLine) {
            if (strstr($cerLine,"END CERTIFICATE")==FALSE && strstr($cerLine,"BEGIN CERTIFICATE")==FALSE) {
                $certificate .= trim($cerLine);
            }                
        }
        $x509->setCertificate($certificate);
        return $x509;
    }

    private static function int2hex($dec) {

        $hex = "";
        // Test if hex string is passed on (some ssl libraries automate conversion)
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

    private static function hex2dec($hex) {

        $dec = '';
        $i = -2;

        do {
            $dec .= chr(hexdec(substr($hex, $i+=2, 2)));
        } while ($i<strlen($hex));

        return $dec;
    } 
    
}
