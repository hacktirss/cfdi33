<?php

/*
 * PACService
 * cfdi33®
 * ® 2017, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */

namespace com\softcoatl\cfdi\v33;

interface PACService {

    public function timbraComprobante($xmlCFDI);
    public function getTimbre($xmlCFDI);
    public function cancelaComprobantePFXBA($rfc, $uuid, $pass, $pfxBA);
    public function cancelaComprobantePFX($rfc, $uuid, $pass, $pfx);
    public function cancelaComprobanteSignatureBA($rfcEmisor, $rfcReceptor, $total, $uuid, $pass, $cerBA, $keyBA);
    public function cancelaComprobanteSignature($rfcEmisor, $rfcReceptor, $total, $uuid, $pass, $cer, $key);
    public function getCFDIRelacionadosPFXBA($rfc, $uuid, $pass, $pfxBA);
    public function getCFDIRelacionadosPFX($rfc, $uuid, $pass, $pfx);
    public function getCFDIRelacionadosSignatureBA($rfc, $uuid, $pass, $cerBA, $keyBA);
    public function getCFDIRelacionadosSignature($rfc, $uuid, $pass, $cer, $key);
    public function getAcuseCancelacion($uuid);
    public function getError();
}//PACService
