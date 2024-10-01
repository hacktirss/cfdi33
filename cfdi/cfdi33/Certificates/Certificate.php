<?php
/*
 * Certificate
 * Objeto del certificado
 * cfdi®
 * © 2020, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since Jan 2020
 */
namespace com\softcoatl\security\commons;

class Certificate {
    
    const KEY_BEGIN = "-----BEGIN PRIVATE KEY-----";
    const KEY_END = "-----END PRIVATE KEY-----";
    const CER_BEGIN = "-----BEGIN CERTIFICATE-----";
    const CER_END = "-----END CERTIFICATE-----";

    private $PrivateKey;
    private $X509Certificate;
    private $X509CertificateData;
    private $PKCS12KeyStore;
    private $passPhrase;
    private $readed;

    function __construct($Certificate, $PrivateKey, $passPhrase = "") {

        error_log("Generando llaves");
        $this->PrivateKey = openssl_pkey_get_private($PrivateKey, $passPhrase);
        $this->X509Certificate = openssl_x509_read($Certificate);
        $this->X509CertificateData = openssl_x509_parse($this->X509Certificate);
        $this->readed = !empty($this->PrivateKey) 
                && !empty($this->X509Certificate)
                && !empty($this->X509CertificateData);
        $this->passPhrase = $passPhrase;
        error_log("Generando PFX");
        if (openssl_pkcs12_export($this->X509Certificate, $this->PKCS12KeyStore, $this->PrivateKey, $this->passPhrase)) {
            error_log("Se generó el PFX Key Store");
        }
    }

    function readed() {
        return $this->readed;
    }

    public function getPEMCertificate() {
        return Commons::x509CertificateToPEM($this->X509Certificate);
    }
 
    public function getPEMPrivateKey() {
        return Commons::privateKeyToPEM($this->PrivateKey);
    }

    public function getBase64Certificate() {
        return Commons::pemToBase64(Commons::x509CertificateToPEM($this->X509Certificate));
    }

    public function getBase64PrivateKey() {
        return Commons::pemToBase64(Commons::privateKeyToPEM($this->PrivateKey));
    }

    public function getBase64KeyStore() {
        return Commons::pemToBase64(Commons::x509CertificateToPEM($this->X509Certificate));
    }

    function getPKCS12KeyStore() {
        return $this->PKCS12KeyStore;
    }

    public function getHexSerialNumber() {

        if (array_key_exists("serialNumberHex", $this->X509CertificateData)) {
            return strtoupper($this->X509CertificateData['serialNumberHex']);
        } else {
            return strtoupper(Commons::int2hex($this->X509CertificateData['serialNumber']));
        }
    }

    public function getDECSerialNumber() {
        return Commons::bchexdec($this->getHexSerialNumber());
    }

    public function getSerialNumber() {

        return Commons::hex2dec($this->getHexSerialNumber());
    }

    public function signature($dataSigned, $algorithm = OPENSSL_ALGO_SHA1) {
        $signature = "";
        openssl_sign($dataSigned, $signature, $this->PrivateKey, $algorithm);
        return base64_encode($signature);
    }
    
    public function serializeIssuer() {
        $serializedIssuer = "";
        foreach ($this->X509CertificateData["issuer"] as $key => $value) {
            $serializedIssuer .= $key . '=' . $value . ',';
        }
        return substr($serializedIssuer, 0, -1);
    }

    public function validFrom() {

        if (array_key_exists("validFrom_time_t", $this->X509CertificateData)) {
            return new \DateTime("@" . $this->X509CertificateData['validFrom_time_t']);
        }
    }
    
    public function validTo() {

        if (array_key_exists("validTo_time_t", $this->X509CertificateData)) {
            return new \DateTime("@" . $this->X509CertificateData['validTo_time_t']);
        }        
    }

    private function hasVigencyDates() {
        return array_key_exists("validFrom_time_t", $this->X509CertificateData)
            && array_key_exists("validTo_time_t", $this->X509CertificateData);
    }

    public function isValid() {
        if (!$this->hasVigencyDates()) {
            error_log("Vigency dates not present!");
            return false;
        }
        $today = new \DateTime();
        return $today > $this->validFrom()
                && $today < $this->validTo();
    }

    public function printVigency($dateFormat = "%A, %B %d, %Y", $fromPronoun = "From ", $toPronoun = "to") {
        if ($this->hasVigencyDates()) {
            return $fromPronoun . " " . mb_convert_case(strftime($dateFormat, $this->validFrom()->getTimestamp()), MB_CASE_TITLE, "UTF-8") 
                    . " " . $toPronoun . " " . mb_convert_case(strftime($dateFormat, $this->validTo()->getTimestamp()), MB_CASE_TITLE, "UTF-8");
        }
    }

    public function printFrom($dateFormat = "%A, %B %d, %Y") {
        if ($this->hasVigencyDates()) {
            return mb_convert_case(strftime($dateFormat, $this->validFrom()->getTimestamp()), MB_CASE_TITLE, "UTF-8");
        }
    }

    public function printTo($dateFormat = "%A, %B %d, %Y") {
        if ($this->hasVigencyDates()) {
            return mb_convert_case(strftime($dateFormat, $this->validTo()->getTimestamp()), MB_CASE_TITLE, "UTF-8");
        }
    }

    public function getRFC() {
        return $this->X509CertificateData['subject']['x500UniqueIdentifier'];
    }
    public function getX509XMLData() {
        return
                '<X509Data>'
              .       '<X509IssuerSerial>'
              .           '<X509IssuerName>' . $this->serializeIssuer() . '</X509IssuerName>'
              .           '<X509SerialNumber>' . $this->getSerialNumber() . '</X509SerialNumber>'
              .       '</X509IssuerSerial>'
              .       '<X509Certificate>' . $this->getBase64Certificate() . '</X509Certificate>'
              . '</X509Data>';
    }
}
