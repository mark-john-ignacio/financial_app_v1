<?php

namespace App\Controllers\API\BIRPDF;
use TCPDF_FONTS;
class BIRPDF0619E extends BIRPDFBase
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
        $this->writeStyledText(16.5, 45, $data->month);
        $this->writeStyledText(26.5, 45, $data->year);
        $this->writeStyledText(52, 45, $data->due_month);
        $this->writeStyledText(62, 45, $data->due_day);
        $this->writeStyledText(72, 45, $data->due_year);
        
        $this->fillCheckbox(97, 44, $data->amended_form == 'Y');
        $this->fillCheckbox(112.5, 44, $data->amended_form != 'Y');
        $this->fillCheckbox(133, 44, $data->taxes_withheld == 'Y');
        $this->fillCheckbox(148, 44, $data->taxes_withheld != 'Y');
        
        $this->writeStyledText(77, 57, $data->taxpayer_tin);
        $this->writeStyledText(189, 57, $data->rdo_code);
        $this->writeStyledText(6.5, 67, $data->withholding_agent_name);
        
        $this->writeStyledText(6.5, 77, $data->registered_address);
        $this->writeStyledText(6.5, 83.5, substr($data->registered_address_continued, 0, 31));
        $this->writeStyledText(189, 83.5, $data->zip_code);
        $this->writeStyledText(36.5, 90, $data->contact_number);
        $this->fillCheckbox(157, 90, $data->withholding_agent_category == 'P');
        $this->fillCheckbox(182, 90, $data->withholding_agent_category != 'P');
        $this->writeStyledText(6.5, 100, $data->email_address);
        
        $this->processAndWriteAmount(184.3, 112.25, $data->amount_of_remittance);
        $this->processAndWriteAmount(184.3, 118.9, $data->amount_remitted_previous);
        $this->processAndWriteAmount(184.3, 125.25, $data->net_amount_of_remittance);
        $this->processAndWriteAmount(184.3, 135.5, $data->penalty_surcharge);
        $this->processAndWriteAmount(184.3, 142, $data->penalty_interest);
        $this->processAndWriteAmount(184.3, 148.5, $data->penalty_compromise);
        $this->processAndWriteAmount(184.3, 155, $data->total_penalties);
        $this->processAndWriteAmount(184.3, 161.5, $data->total_amount_of_remittance);
        $this->processSignatureImage(120, 176, $data->signature_image);
    }
}
