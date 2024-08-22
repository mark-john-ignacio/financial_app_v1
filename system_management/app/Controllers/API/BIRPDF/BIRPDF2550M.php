<?php

namespace App\Controllers\API\BIRPDF;
use TCPDF_FONTS;

class BIRPDF2550M extends BIRPDFBase
{
    public function generatePdf()
    {
        $json = $this->request->getJSON();
        $this->loadTemplate('BIRForm2550-M.pdf');
        $fileName = 'BIR_Form_' . $json->taxpayer_tin . '_' . date('Y-m-d') . '.pdf';
        return $this->generatePdfBase($json, $fileName);
    }

    protected function fillFields($data)
    {
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $this->pdf->SetFont($fontname, '', 12);
        $this->pdf->SetTextColor(0, 0, 0);
        $letterSpacing = 2.55;
        $this->pdf->setFontSpacing($letterSpacing);
        $this->pdf->SetPage(1);
        $this->writeStyledText(16.5, 45, "HEllo can you see me");

        $this->pdf->SetPage(2);

        $this->writeStyledText(16.5, 45, "How about here?");
    }
}
