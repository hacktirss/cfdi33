<?php

/*
 * PDFTransformerAC
 * cfdi®
 * © 2018, Detisa 
 * http://www.detisa.com.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since sep 2018
 */

require_once('tcpdf/tcpdf.php');
require_once('PDFTYPE.php');


class AcuseVO {

    private $Folio;
    private $UUID;
    private $Estatus;
    private $Fecha;
    private $RfcEmisor;
    private $NombreEmisor;
    private $RfcReceptor;
    private $NombreReceptor;
    private $Sello;

    function getFolio() {
        return $this->Folio;
    }

    function getUUID() {
        return $this->UUID;
    }

    function getEstatus() {
        return $this->Estatus;
    }

    function getFecha() {
        return $this->Fecha;
    }

    function getRfcEmisor() {
        return $this->RfcEmisor;
    }

    function getNombreEmisor() {
        return $this->NombreEmisor;
    }

    function getRfcReceptor() {
        return $this->RfcReceptor;
    }

    function getNombreReceptor() {
        return $this->NombreReceptor;
    }

    function getSello() {
        return $this->Sello;
    }

    function setFolio($Folio) {
        $this->Folio = $Folio;
    }

    function setUUID($UUID) {
        $this->UUID = $UUID;
    }

    function setEstatus($Estatus) {
        $this->Estatus = $Estatus;
    }

    function setFecha($Fecha) {
        $this->Fecha = $Fecha;
    }

    function setRfcEmisor($RfcEmisor) {
        $this->RfcEmisor = $RfcEmisor;
    }

    function setNombreEmisor($NombreEmisor) {
        $this->NombreEmisor = $NombreEmisor;
    }

    function setRfcReceptor($RfcReceptor) {
        $this->RfcReceptor = $RfcReceptor;
    }

    function setNombreReceptor($NombreReceptor) {
        $this->NombreReceptor = $NombreReceptor;
    }

    function setSello($Sello) {
        $this->Sello = $Sello;
    }

}
/**
 * Description of PDFTransformerAC
 *
 * @author Rolando Esquivel
 */
class PDFTransformerAC {

    /**
     * 
     * @param AcuseVO $acuse
     * @param type $direccion
     * @param type $type
     * @param type $logo
     * @param type $path
     * @return boolean
     */
    public static function getPDF($acuse, $direccion, $type, $logo = 'img/logo.png', $path = './') {

        $doc_title = "Acuse de Cancelación de CFDI";

        define ("PDF_PAGE_FORMAT", "A4");
        define ("PDF_MARGIN_TOP", 30);
        define ("PDF_MARGIN_BOTTOM", 10);
        define ("PDF_FONT_SIZE_MAIN", 12);
        define ("PDF_FONT_SIZE_DATA", 8);
        define ("PDF_HEADER_TITLE", $doc_title);

        $pdf = new CUSTOM_PDFAC(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true); 

        $pdf->setDireccion($direccion);
        $pdf->setLogo($logo);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle($doc_title);

        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetMargins(10, 45, 10);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->AddPage();
        $pdf->SetTextColor();
        $pdf->writeHTML('<div align="center"><h3>'.$doc_title.'</h3></div>', false, 0, false, 0);
        $pdf->SetTextColor();

        $cTabla = "<br/><br/><br/><table border=\"0\" cellspacing=\"1\" cellpadding=\"10\">"
                    . "<tr>"
                    .   "<td style=\"color:#749B9C\" align=\"right\" nowrap><b>Folio Omicrom</b></td>"
                    .   "<td align=\"left\" nowrap>" . $acuse->getFolio() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    .   "<td style=\"color:#749B9C\" align=\"right\" nowrap><b>Folio Fiscal</b></td>"
                    .   "<td align=\"left\" nowrap=\"nowrap\">" . $acuse->getUUID() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    .   "<td style=\"color:#749B9C\" align=\"right\" nowrap><b>Estatus CFDI</b></td>"
                    .   "<td align=\"left\" nowrap>Cancelado</td>"
                    . "</tr>"
                    . "<tr >"
                    .   "<td style=\"color:#749B9C\" align=\"right\" nowrap><b>Fecha de Cancelacion</b></td>"
                    .   "<td align=\"left\" nowrap>" . $acuse->getFecha() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    .   "<td style=\"color:#749B9C\" align=\"right\" nowrap><b>RFC Emisor</b></td>"
                    .   "<td align=\"left\" nowrap>" . $acuse->getRfcEmisor() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    .   "<td style=\"color:#749B9C\" align=\"right\" nowrap><b>RFC Receptor</b></td>"
                    .   "<td align=\"left\" nowrap>" . $acuse->getRfcReceptor() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    .   "<td style=\"color:#749B9C\" align=\"right\" nowrap><b>Receptor</b></td>"
                    .   "<td align=\"left\" nowrap>" . $acuse->getNombreReceptor() . "</td>"
                    . "</tr>"
                    . "<tr><td>&nbsp;</td></tr>"
                    . "<tr>"
                    .   "<td style=\"background-color:#749B9C; color:#FFFFFF\" colspan=\"2\" align=\"left\"><b>Sello Digital del SAT</b></td>"
                    . "</tr>"
                    . "<tr>"
                    .   "<td style=\"font-size=10\" colspan=\"2\" align=\"left\" nowrap>" . $acuse->getSello() . "</td>"
                    . "</tr>"
            . "</table>";
        $pdf->writeHTML($cTabla, true, 0, true, 1);			

        if ($type==PDFTYPE::Download) {
            $pdf->Output($acuse->getUUID() . '.pdf', 'D');
        } else if ($type==PDFTYPE::File) {
            $pdf->Output($path . $acuse->getUUID() . '.pdf', 'F');
        } else {
            return $pdf->Output($acuse->getUUID() . '.pdf', 'S');
        }

        return TRUE;
    }
    
}

class CUSTOM_PDFAC extends TCPDF {

    private $direccion;
    private $logo;

    function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    function setLogo($logo) {
        $this->logo = $logo;
    }

    public function Header() {

        $html = 
                "<table style=\"width: 100%; border-collapse: separate; border-spacing: 10px;\">"
                    . "<tr>"
                        . "<td style=\"width: 4cm;\"><img style=\"min-width: 3.5cm; max-width: 3.5cm; height: 3cm;\" src=\"data:image/png;base64, " . base64_encode($this->logo) . "\"/></td>"
                        . "<td style=\"width: 10cm;\">" . $this->direccion . "</td>"
                    . "</tr>"
                . "</table>";

        $this->SetFont('helvetica', '', 10);
        $this->writeHTML($html);
        $this->SetTextColor();
    }

    // Page footer
    public function Footer() {
            $cur_y = $this->GetY();
            $ormargins = $this->getOriginalMargins();
            $this->SetTextColor(0x74, 0x9B, 0x9C);			
            //set style for cell border
            $line_width = 0.85 / $this->getScaleFactor();
            $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0x74, 0x9B, 0x9C)));
            $this->SetY(-15);
            $this->SetFont('helvetica', 'B', 10);
            $this->Cell(0, 10, "DETISA S.A. DE C.V. Texcoco Edo. de Méx. Tel. 01 595 9250401 http://detisa.com.mx", 0, false, 'C', 0, '', 0, false, 'T', 'M');
            $this->SetTextColor();
    }
}
