<?php
/*
 * PDFTransformer
 * detifac®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */

require_once ("cfdi33/utils/NumericalCurrencyConverter.php");
require_once ("cfdi33/utils/Currency.php");
require_once ("cfdi33/utils/SpanishNumbers.php");
require_once ("tcpdf/tcpdf.php");
require_once ("PDFTYPE.php");

use com\softcoatl\cfdi\v33\schema\Comprobante as Comprobante;
use com\softcoatl\cfdi\v33\schema\Comprobante\complemento\Pagos as Pagos;
use com\softcoatl\utils as Utils;

class PDFTransformerRP {

    /**
     * 
     * @param \cfdi33\Comprobante $Comprobante
     */
    public static function getPDF($Comprobante, $doc, $type, $logo = 'img/logo.png', $path = './') {

        define ("PDF_PAGE_FORMAT", "A4");
        $pdf = new CUSTOM_PDF_RP(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true); 

        $html  = 
                  "<table cellpadding=\"2px\">"
                    . "<thead>"
                        . "<tr style=\"background-color: #6BA5D9; color: white; font-weight: bold; font-size: 10; text-align: center;\">"
                            . "<th style=\"width: 2cm;\">Clave</th>"
                            . "<th style=\"width: 2cm;\">ID.</th>"
                            . "<th style=\"width: 2cm;\">Cantidad</th>"
                            . "<th style=\"width: 1.5cm;\">Unidad</th>"
                            . "<th style=\"width: 7cm;\">Descripción</th>"
                            . "<th style=\"width: 2cm;\">Precio U.</th>"
                            . "<th style=\"width: 2.5cm;\">Importe</th>"
                        . "</tr>"
                    . "</thead>"
                    . "<tbody>";
 
        /* @var $Concepto \cfdi33\Comprobante\Conceptos\Concepto */
        foreach ($Comprobante->getConceptos()->getConcepto() as $Concepto) {
            $html .=
                    "<tr style=\"font-size: 8; text-align: center;\">"
                      . "<td style=\"width: 2cm;\">" . $Concepto->getClaveProdServ() . "</td>"
                      . "<td style=\"width: 2cm;\">" . $Concepto->getNoIdentificacion() . "</td>"
                      . "<td style=\"width: 2cm;\">" . $Concepto->getCantidad() . "</td>"
                      . "<td style=\"width: 1.5cm;\">" . $Concepto->getClaveUnidad() . "</td>"
                      . "<td style=\"width: 7cm;\">" . $Concepto->getDescripcion() . "</td>"
                      . "<td style=\"width: 2cm;\">" . number_format($Concepto->getValorUnitario(), 2, '.', ',') . "</td>"
                      . "<td style=\"width: 2.5cm;\">" . number_format($Concepto->getImporte(), 2, '.', ',') . "</td>"
                  . "</tr>";
        }
        $html .= "</tbody></table>";

        /***********************************   Recibo Electrónico de Pagos   *****************************************/
        $htmlp  = 
                  "<table cellpadding=\"2px\">"
                    . "<thead>"
                        . "<tr style=\"background-color: #6BA5D9; color: white; font-weight: bold; font-size: 7; text-align: center;\">"
                            . "<th style=\"width: 5cm;\">Documento</th>"
                            . "<th style=\"width: 5cm;\">Método de Pago</th>"
                            . "<th style=\"width: 1.5cm;\">Parcialidad</th>"
                            . "<th style=\"width: 2.5cm;\">Saldo Anterior</th>"
                            . "<th style=\"width: 2.5cm;\">Importe Pagado</th>"
                            . "<th style=\"width: 2.5cm;\">Saldo Insoluto</th>"
                        . "</tr>"
                    . "</thead>"
                    . "<tbody>";

        /* @var $pagos \cfdi33\complemento\Pagos */
        $pagos = NULL;
        foreach ($Comprobante->getComplemento() as $complemento) {
            
            if ($complemento instanceof Pagos) {
                
                $pagos = $complemento; 
            }
        }        

        if ($pagos) {

            /* @var $pago \cfdi33\complemento\Pagos\Pago */
            foreach ($pagos->getPagos() as $pago) {

                /* @var $doctoRelacionado cfdi33\complemento\Pagos\Pago\DoctoRelacionado */
                foreach ($pago->getDoctoRelacionado() as $doctoRelacionado) {

                    $htmlp .=
                            "<tr style=\"font-size: 7; text-align: center;\">"
                              . "<td style=\"width: 5cm; font-size: 6;\">" . $doctoRelacionado->getIdDocumento() . "</td>"
                              . "<td style=\"width: 5cm;\">" . $doctoRelacionado->getMetodoDePagoDR(). " - " . $pdf->decodeMetodoPago($doctoRelacionado->getMetodoDePagoDR()) . "</td>"
                              . "<td style=\"width: 1.5cm;\">" . $doctoRelacionado->getNumParcialidad() . "</td>"
                              . "<td style=\"width: 2.5cm;\">" . number_format($doctoRelacionado->getImpSaldoAnt(), 2, '.', ',') . "</td>"
                              . "<td style=\"width: 2.5cm;\">" . number_format($doctoRelacionado->getImpPagado(), 2, '.', ',') . "</td>"
                              . "<td style=\"width: 2.5cm;\">" . number_format($doctoRelacionado->getImpSaldoInsoluto(), 2, '.', ',') . "</td>"
                          . "</tr>";
                }
            }
        }

        $htmlp .= "</tbody></table>";
        /***********************************   Recibo Electrónico de Pagos   *****************************************/

        $pdf->setComprobante($Comprobante);
        $pdf->setLogo($logo);
        $pdf->setDoc($doc);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle($Comprobante->getTimbreFiscalDigital()->getUUID());
        $pdf->SetSubject('CFDI');
        $pdf->SetKeywords('CFDI');

        $pdf->SetTopMargin(75);
        $pdf->setHeaderMargin(20);
        $pdf->SetAutoPageBreak(TRUE, 90);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

        $pdf->AddPage();
        $pdf->SetFont('Helvetica', '', 8);

        $pdf->writeHTML($html);
        $pdf->writeHTML($htmlp);

        if ($type==  PDFTYPE::Download) {
            $pdf->Output($Comprobante->getTimbreFiscalDigital()->getUUID() . '.pdf', 'D');
        } else if ($type== PDFTYPE::File) {
            $pdf->Output($path . $Comprobante->getTimbreFiscalDigital()->getUUID() . '.pdf', 'F');
        } else {
            return $pdf->Output($Comprobante->getTimbreFiscalDigital()->getUUID() . '.pdf', 'S');
        }

        return TRUE;
    }//getCadenaOriginal
}

class CUSTOM_PDF_RP extends TCPDF {

    /* @var $Comprobante \cfdi33\Comprobante */
    private $Comprobante;
    private $logo;
    private $doc;

    function setDoc($doc) {
        $this->doc = $doc;
    }

    function setComprobante($Comprobante) {
        $this->Comprobante = $Comprobante;
    }

    function setLogo($logo) {
        $this->logo = $logo;
    }

    private function decodeRegimenFiscal($RegimenFiscal) {
        switch ($RegimenFiscal) {
        case "601": return "General de Ley Personas Morales";
        case "603": return "Personas Morales con Fines no Lucrativos";
        case "606": return "Arrendamiento";
        case "607": return "Régimen de Enajenación o Adquisición de Bienes";
        case "608": return "Demás ingresos";
        case "609": return "Consolidación";
        case "610": return "Residentes en el Extranjero sin Establecimiento Permanente en México";
        case "611": return "Ingresos por Dividendos (socios y accionistas)";
        case "612": return "Personas Físicas con Actividades Empresariales y Profesionales";
        case "614": return "Ingresos por intereses";
        case "615": return "Régimen de los ingresos por obtención de premios";
        case "616": return "Sin obligaciones fiscales";
        case "620": return "Sociedades Cooperativas de Producción que optan por diferir sus ingresos";
        case "621": return "Incorporación Fiscal";
        case "622": return "Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras";
        case "623": return "Opcional para Grupos de Sociedades";
        case "624": return "Coordinados";
        case "628": return "Hidrocarburos";
        case "629": return "De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales";
        case "630": return "Enajenación de acciones en bolsa de valores";
        }//case
    }

    private function decodeTipoComprobante($TipoComprobante) {
        switch ($TipoComprobante) {
        case "I": return "Ingresos";
        case "E": return "Egresos";
        case "T": return "Traslado";
        case "N": return "Nómina";
        case "P": return "Pago";
        }
    }

    private function decodeTipoRelacion($TipoRelacion) {
        switch ($TipoRelacion) {
        case "01": return "Nota de crédito de los documentos relacionados";
        case "02": return "Nota de débito de los documentos relacionados";
        case "03": return "Devolución de mercancía sobre facturas o traslados previos";
        case "04": return "Sustitución de los CFDI previos";
        case "05": return "Traslados de mercancias facturados previamente";
        case "06": return "Factura generada por los traslados previos";
        case "07": return "CFDI por aplicación de anticipo";
        case "08": return "Factura generada por pagos en parcialidades";
        case "09": return "Factura generada por pagos diferidos";
        }
    }

    private function decodeUsoDelCFDI($UsoDelCFDI) {
        switch ($UsoDelCFDI) {
        case "G01": return "Adquisición de mercancias";
        case "G02": return "Devoluciones, descuentos o bonificaciones";
        case "G03": return "Gastos en general";
        case "I01": return "Construcciones";
        case "I02": return "Mobilario y equipo de oficina por inversiones";
        case "I03": return "Equipo de transporte";
        case "I04": return "Equipo de computo y accesorios";
        case "I05": return "Dados, troqueles, moldes, matrices y herramental";
        case "I06": return "Comunicaciones telefónicas";
        case "I07": return "Comunicaciones satelitales";
        case "I08": return "Otra maquinaria y equipo";
        case "D01": return "Honorarios médicos, dentales y gastos hospitalarios.";
        case "D02": return "Gastos médicos por incapacidad o discapacidad";
        case "D03": return "Gastos funerales.";
        case "D04": return "Donativos.";
        case "D05": return "Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación).";
        case "D06": return "Aportaciones voluntarias al SAR.";
        case "D07": return "Primas por seguros de gastos médicos.";
        case "D08": return "Gastos de transportación escolar obligatoria.";
        case "D09": return "Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.";
        case "D10": return "Pagos por servicios educativos (colegiaturas)";
        case "P01": return "Por definir";
        }
    }
    
    private function decodeFormaPago($FormaPago) {
        switch ($FormaPago) {
        case "01": return "Efectivo";
        case "02": return "Cheque nominativo";
        case "03": return "Transferencia electrónica de fondos";
        case "04": return "Tarjeta de crédito";
        case "05": return "Monedero electrónico";
        case "06": return "Dinero electrónico";
        case "08": return "Vales de despensa";
        case "12": return "Dación en pago";
        case "13": return "Pago por subrogación";
        case "14": return "Pago por consignación";
        case "15": return "Condonación";
        case "17": return "Compensación";
        case "23": return "Novación";
        case "24": return "Confusión";
        case "25": return "Remisión de deuda";
        case "26": return "Prescripción o caducidad";
        case "27": return "A satisfacción del acreedor";
        case "28": return "Tarjeta de débito";
        case "29": return "Tarjeta de servicios";
        case "30": return "Aplicación de anticipos";
        case "31": return "Intermediario pagos";
        case "99": return "Por definir";
        }
    }
    
    public static function decodeMetodoPago($MetodoPago) {
        switch ($MetodoPago) {
            case "PUE": return "Pago en una sola exhibición";
            case "PIP": return "Pago inicial y parcialidades";
            case "PPD": return "Pago en parcialidades o diferido";            
        }
    }
    
    public static function decodeImpuesto($impuesto) {
        switch ($impuesto) {
        case "001": return "ISR";
        case "002": return "IVA";
        case "003": return "IEPS";
        }
    }
    public function Header() {

        $html = 
                "<table style=\"width: 100%; border-collapse: separate; border-spacing: 10px;\">"
                    . "<tr>"
                        . "<td>"
                            . "<table style=\"width: 100%;\">"
                                . "<tr>"
                                    . "<td style=\"width: 4cm;\"><img style=\"min-width: 3.5cm; max-width: 3.5cm; height: 3cm;\" src=\"data:image/png;base64, " . base64_encode($this->logo) . "\"/></td>"
                                    . "<td style=\"text-align: center; width: 9.5cm;\"><b>"
                                        . $this->Comprobante->getEmisor()->getNombre() . "<br/>"
                                        . "RFC:" . $this->Comprobante->getEmisor()->getRfc() . "</b><br/>" 
                                        . "<font size=\"8\"><b>Régimen Fiscal " . $this->Comprobante->getEmisor()->getRegimenFiscal() . "</b> - " . $this->decodeRegimenFiscal($this->Comprobante->getEmisor()->getRegimenFiscal()) . "</font>"
                                    . "</td>"
                                    . "<td style=\"width: 5cm; border: 0.1px solid #6BA5D9;\"><table><tr><td>"
                                        . "<table style=\"text-align: center; font-size: 8;\">"
                                            . "<tr style=\"background-color: #6BA5D9; color: white; font-weight: bold; font-size: 10;\"><td>" . $this->doc . " : " . $this->Comprobante->getFolio() . "</td></tr>"
                                            . "<tr><td>Lugar de Expedición</td></tr>"
                                            . "<tr><td>C.P. " . $this->Comprobante->getLugarExpedicion(). "</td></tr>"
                                            . "<tr><td>Fecha de Expedición</td></tr>"
                                            . "<tr><td>" . $this->Comprobante->getFecha(). "</td></tr>"
                                            . "<tr><td>Tipo de Comprobante</td></tr>"
                                            . "<tr><td>" . $this->Comprobante->getTipoDeComprobante() . " - " . $this->decodeTipoComprobante($this->Comprobante->getTipoDeComprobante()) . "</td></tr>"
                                        . "</table>"
                                    . "</td></tr></table></td>"
                                . "</tr>"
                            . "</table>"
                        . "</td>"
                    . "</tr>"
                    . "<tr>"
                        . "<td>"
                            . "<table style=\"font-size: 8;\">"
                                . "<tr>"
                                    . "<td style=\"width: 10cm;\">"
                                        . "<table>"
                                            . "<tr><td style=\"color: #729B9C; font-weight: bold;\">Receptor del Comprobante Fiscal</td></tr>"
                                            . "<tr><td>" . $this->Comprobante->getReceptor()->getNombre(). "</td></tr>"
                                            . "<tr><td><b>R.F.C. " . $this->Comprobante->getReceptor()->getRfc(). "</b></td></tr>"
                                            . "<tr><td><b>Uso del CFDI: " . $this->Comprobante->getReceptor()->getUsoCFDI() . "</b> - " . $this->decodeUsoDelCFDI($this->Comprobante->getReceptor()->getUsoCFDI()) ."</td></tr>"
                                        . "</table>"
                                    . "</td>"
                                    . "<td style=\"width: 10cm;\">"
                                        . "<table>"
                                            . "<tr><td colspan=\"2\" style=\"color: #729B9C; font-weight: bold;\">Datos Generales del Comprobante</td></tr>"
                                            . "<tr><td><b>Moneda</b></td><td>" . $this->Comprobante->getMoneda(). "</td></tr>"
                                            . "<tr><td><b>Versión CFDI</b></td><td>" . $this->Comprobante->getVersion() . "</td></tr>"
                                        . "</table>"
                                    . "</td>"
                                . "</tr>"
                            . "</table>"
                        . "</td>"
                    . "</tr>"
                . "</table>";

        $this->SetFont('helvetica', '', 10);
        //$this->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0x6B, 0xA5, 0xD9)));
        $this->writeHTML($html);
        $this->SetTextColor();
    }

    // Page footer
    public function Footer() {

        $converter = new Utils\NumericalCurrencyConverter(new Utils\SpanishNumbers(), new Utils\Currency('PESOS', 'PESO'));

        $hasObservaciones = false;
        $html = "<table style=\"font-size: 7; font-style: italic; color: GRAY;\">";

        foreach ($this->Comprobante->getAddenda() as $addenda) {

            /* @var $addenda \cfdi33\detisa\Observaciones */
            if ($addenda instanceof Comprobante\addenda\Observaciones) {

                /* @var $observacion Observacion */
                foreach ($addenda->getObservaciones() as $observacion) {

                    $hasObservaciones = true;
                    $html .= "<tr><td style=\"width: 19cm; text-align: left;\">" . $observacion->getDescripcion() . "</td></tr>";
                }
            }
        }

        $html .=  "</table>";

        if ($hasObservaciones) {

            //echo $html;
            $this->SetY(-95);
            $this->SetFont('helvetica', '', 7);
            $this->writeHTML($html);
        }

        /***********************************   Recibo Electrónico de Pagos   *****************************************/
        $htmlp  = 
                  "<table cellpadding=\"2px\">"
                    . "<thead>"
                        . "<tr style=\"background-color: #6BA5D9; color: white; font-weight: bold; font-size: 8; text-align: center;\">"
                            . "<th style=\"width: 5cm;\">Fecha del Pago</th>"
                            . "<th style=\"width: 9cm;\">Forma de Pago</th>"
                            . "<th style=\"width: 5cm;\">Importe del Pago</th>"
                        . "</tr>"
                    . "</thead>"
                    . "<tbody>";

        /* @var $pagos \cfdi33\complemento\Pagos */
        $pagos = NULL;
        foreach ($this->Comprobante->getComplemento() as $complemento) {

            if ($complemento instanceof Pagos) {

                $pagos = $complemento; 
            }
        }        

        $pagoTotal = 0.00;
        if ($pagos) {

            /* @var $pago \cfdi33\complemento\Pagos\Pago */
            foreach ($pagos->getPagos() as $pago) {

                $pagoTotal += $pago->getMonto();
                $moneda = $pago->getMonedaP() == 'MXN' ? "M.N." : "USD";
                $htmlp .=
                        "<tr style=\"font-size: 7; text-align: center;\">"
                          . "<td style=\"width: 5cm;\">" . $pago->getFechaPago() . "</td>"
                          . "<td style=\"width: 9cm;\">" . $pago->getFormaDePagoP(). " - " . $this->decodeFormaPago($pago->getFormaDePagoP()) . "</td>"
                          . "<td style=\"width: 5cm; text-align: right\">" . number_format($pago->getMonto(), 2, '.', ',') . "</td>"
                      . "</tr>";
            }

            $htmlp .=
                    "<tr style=\"font-size: 7; text-align: center;\">"
                      . "<td style=\"text-align: left;\" colspan=\"2\">Importe con Letra: " . $converter->convert($pagoTotal) . " " . $moneda . "</td>"
                      . "<td style=\"text-align: right;\">Pago Total: " . number_format($pagoTotal, 2, '.', ',') . "</td>"
                  . "</tr>";
        }

        $htmlp .= "</tbody></table>";


        $this->SetY(-90);
        $this->SetFont('helvetica', '', 7);
        $this->writeHTML($htmlp);
        /***********************************   Recibo Electrónico de Pagos   *****************************************/

        /* @var $TimbreFiscalDigital \cfdi33\complemento\TimbreFiscalDigital */
        $TimbreFiscalDigital = $this->Comprobante->getTimbreFiscalDigital();
        $html = 
                "<table style=\"border: 0.1px solid #D2D1D2; font-size: 7; font-weight: bold;\">"
                    . "<tr>"
                        . "<td style=\"width: 3.3cm; background-color: #D2D1D2;\">No. Certificado Digital</td>"
                        . "<td style=\"text-align: center; width: 6.2cm; border-bottom: 0.1px solid #D2D1D2;\">" . $this->Comprobante->getNoCertificado() . "</td>"
                        . "<td style=\"width: 3.3cm; background-color: #D2D1D2;\">Certificado Digital SAT</td>"
                        . "<td style=\"text-align: center; width: 6.2cm; border-bottom: 0.1px solid #D2D1D2;\">". ($TimbreFiscalDigital==NULL ? "&nbsp;" : $TimbreFiscalDigital->getNoCertificadoSAT()) . "</td>"
                    . "</tr>"
                    . "<tr>"
                        . "<td style=\"width: 3.3cm; background-color: #D2D1D2;\">Folio Fiscal</td>"
                        . "<td style=\"text-align: center; width: 6.2cm;\">" . ($TimbreFiscalDigital==NULL ? "&nbsp;" : $TimbreFiscalDigital->getUUID()) . "</td>"
                        . "<td style=\"width: 3.3cm; background-color: #D2D1D2;\">Fecha de Certificación</td>"
                        . "<td style=\"text-align: center; width: 6.2cm;\"    >". ($TimbreFiscalDigital==NULL ? "&nbsp;" : $TimbreFiscalDigital->getFechaTimbrado()) . "</td>"
                    . "</tr>"
                . "</table>";

        $this->writeHTML($html);

        if ($this->Comprobante->getCfdiRelacionados() != NULL) {

            $html = "<table style=\"border: 0.1px solid #D2D1D2; font-size: 7; font-weight: bold;\">";
            $tipoRelacion = $this->Comprobante->getCfdiRelacionados()->getTipoRelacion();
            /* @var $CfdiRelacionado cfdi33\Comprobante\CfdiRelacionados\CfdiRelacionado */
            foreach ($this->Comprobante->getCfdiRelacionados()->getCfdiRelacionado() as $CfdiRelacionado) {
                $html .=
                    "<tr>"
                        . "<td style=\"width: 3.3cm; background-color: #D2D1D2;\">CFDI Relacionado</td>"
                        . "<td style=\"text-align: center; width: 6.2cm; border-bottom: 0.1px solid #D2D1D2;\">" . $CfdiRelacionado->getUUID() . "</td>"
                        . "<td style=\"width: 3.3cm; background-color: #D2D1D2;\">Tipo de Relacion</td>"
                        . "<td style=\"text-align: center; width: 6.2cm; border-bottom: 0.1px solid #D2D1D2;\">". $tipoRelacion . " - " . $this->decodeTipoRelacion($tipoRelacion) . "</td>"
                    . "</tr>";
            }
            
            $html .= "</table>";
            $this->writeHTML($html);
        }


        // set style for barcode
        $style = array(
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0x5B, 0xA5, 0xD9),
            'bgcolor' => array(255, 255, 255)
        );

        $code = $this->Comprobante->getValidationURL();
        $this->write2DBarcode($code, 'QRCODE,Q', 10, 240, 30, 30, $style, 'T');

        $this->SetAbsXY(45, 240);
        
        $DOM= new \DOMDocument("1.0","UTF-8");
        $root = $DOM->createElement('root');
        $tfd = $TimbreFiscalDigital->asXML($root);
        $tfd->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance" );
        $tfd->setAttribute("xmlns:tfd", "http://www.sat.gob.mx/TimbreFiscalDigital");
        $DOM->appendChild($tfd);
        $html = 
                  "<table padding-left: 5px;>"
                    . "<tr><td style=\"background-color: #5BA5D9; color: white; font-weight: bold;\">Cadena original del complemento de certificación digital del SAT</td></tr>"
                    . "<tr><td style=\"font-size: 6;\">" . Comprobante::getTFDOriginalBytes($DOM->saveXML()) . "</td></tr>"
                    . "<tr><td style=\"background-color: #5BA5D9;  color: white; font-weight: bold;\">Sello Digital del Emisor</td></tr>"
                    . "<tr><td style=\"font-size: 6;\">" . $this->Comprobante->getSello() . "</td></tr>"
                    . "<tr><td style=\"background-color: #5BA5D9;  color: white; font-weight: bold;\">Sello Digital del SAT</td></tr>"
                    . "<tr><td style=\"font-size: 6;\">" . $TimbreFiscalDigital->getSelloSAT() . "</td></tr>"
                . "</table>";

        $this->writeHTML($html);

        $this->SetFont('helvetica', 'B', 7);
        $this->Cell(0, 0, '*Este Este documento es una representación impresa de un Comprobante Fiscal Digital a través de Internet', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $this->SetY(-10);
        $this->SetFont('helvetica', 'B', 6);
        $this->SetTextColor(0x72, 0x9B, 0x9C);
        $this->Cell(0, 0, 'Facturado por: DETI DESARROLLO Y TRANSFERENCIA DE INFORMATICA S.A. DE C.V. Texcoco Edo. de Méx. Tel. 01 595 9250401 http://detisa.com.mx', 0, false, 'C', 0, '', 0, false, 'T', 'M');

        $this->SetY(-10);
        $this->SetFont('helvetica', '', 6);
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');     

    }
}
