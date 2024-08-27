<?php

namespace App\Controllers\API\BIRPDF;
use TCPDF_FONTS;

class BIRPDF2550M extends BIRPDFBase
{
    public function generatePdf($json = null, $fileName = null)
    {
        $json = $this->request->getJSON();
        $this->loadTemplate('BIRForm2550-M-2.pdf');
        $fileName = 'BIR_Form_' . $json->taxpayer_tin . '_' . date('Y-m-d') . '.pdf';
        return parent::generatePdf($json, $fileName);
    }

    protected function fillFields($data)
    {
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $this->pdf->SetFont($fontname, '', 12);
        $this->pdf->SetTextColor(0, 0, 0);
        $letterSpacing = 1;
        $letterSpacing2 = 0.3;
        $letterSpacing3 = -0.3;
        $letterSpacing4 = -0.8;

        $this->pdf->SetPage(1);

        $this->pdf->setFontSpacing($letterSpacing2);
        $this->writeStyledText(64, 29, $data->month);
        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText(72, 29, $data->year);

        $this->fillCheckbox(118, 30, $data->amended_return == 'Y');
        $this->fillCheckbox(130, 30, $data->amended_return != 'Y');

        $this->writeStyledText(193, 29, $data->no_of_sheets);

        $this->pdf->setFontSpacing($letterSpacing2);
        $tin = explode('-', $data->taxpayer_tin);
        $this->writeStyledText(21.5, 40, $tin[0]);
        $this->writeStyledText(35, 40, $tin[1]);
        $this->writeStyledText(48, 40, $tin[2]);

        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText(95, 40, $data->rdo_code);

        $this->pdf->setFontSpacing($letterSpacing3);
        $this->writeStyledText(148, 40, $data->line_of_business);
        $this->writeStyledText(17, 49, $data->withholding_agent_name);

        $this->pdf->setFontSpacing($letterSpacing3);
        $this->writeStyledText(168, 49, $data->telephone_number);

        $this->pdf->setFontSpacing($letterSpacing4);
        $this->writeStyledText(17, 58, $data->registered_address);

        $this->pdf->setFontSpacing($letterSpacing2);
        $this->writeStyledText(184, 58, $data->zip_code);

        $this->fillCheckbox(86, 65.5, $data->tax_relief == 'Y');
        $this->fillCheckbox(104, 65.5, $data->tax_relief != 'Y');

        $this->pdf->setFontSpacing($letterSpacing3);
        $this->writeStyledText(140, 64.5, $data->tax_relief_details);

        $this->pdf->setFontSpacing($letterSpacing3);
        $this->writeRightAlignedText(122, 76.2, $data->vat_sales_12a, 25);
    }

    protected function writeRightAlignedText($x, $y, $text, $fieldWidth)
    {
        $letterSpacing = $this->pdf->getFontSpacing(); 
        $this->pdf->setFontSpacing(0); // Reset font spacing for proper width calculation
        $textWidth = $this->pdf->GetStringWidth($text);
        $rightAlignedX = $x + $fieldWidth - $textWidth;
        $this->pdf->SetXY($rightAlignedX, $y);
        $this->pdf->setFontSpacing($letterSpacing); // Restore your desired letter spacing
        $this->pdf->Cell($fieldWidth, 10, strtoupper($text), 0, 0, 'R'); // Ensure the cell width is the field width
    }
}
