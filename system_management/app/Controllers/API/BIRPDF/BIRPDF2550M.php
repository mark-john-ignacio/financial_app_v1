<?php

namespace App\Controllers\API\BIRPDF;
use TCPDF_FONTS;

class BIRPDF2550M extends BIRPDFBase
{
    public function generatePdf($json = null, $fileName = null)
    {
        $json = $this->request->getJSON();
        $this->loadTemplate('BIRForm2550-M.pdf');
        $fileName = 'BIR_Form_' . $json->taxpayer_tin . '_' . date('Y-m-d') . '.pdf';
        return parent::generatePdf($json, $fileName);
    }

    protected function fillFields($data)
    {
        $this->pdf->SetPage(1);
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $this->pdf->SetFont($fontname, '', 12);
        $this->pdf->SetTextColor(0, 0, 0);
        $letterSpacing = 1;
        $letterSpacing2 = 2.8;
        $letterSpacing3 = -0.3;
        $letterSpacing4 = -0.6;



        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText(65.5, 29, $data->month);
        $this->pdf->setFontSpacing($letterSpacing2);
        $this->writeStyledText(74, 29, $data->year);

        $this->fillCheckbox(120, 30, $data->amended_return == 'Y');
        $this->fillCheckbox(132, 30, $data->amended_return != 'Y');

        $this->writeStyledText(195, 29, $data->no_of_sheets);

        $this->pdf->setFontSpacing($letterSpacing);
        $tin = explode('-', $data->taxpayer_tin);
        $this->writeStyledText(23.5, 40, $tin[0]);
        $this->writeStyledText(37, 40, $tin[1]);
        $this->writeStyledText(50, 40, $tin[2]);

        $this->pdf->setFontSpacing($letterSpacing2);
        $this->writeStyledText(97, 40, $data->rdo_code);

        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText(150, 40, $data->line_of_business);
        $this->writeStyledText(19, 49, $data->withholding_agent_name);

        $this->pdf->setFontSpacing($letterSpacing3);
        $this->writeStyledText(170, 49, $data->telephone_number);

        $this->pdf->setFontSpacing($letterSpacing4);
        $this->writeStyledText(17, 58, $data->registered_address);

        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText(186, 58, $data->zip_code);

        $this->fillCheckbox(88, 65.5, $data->tax_relief == 'Y');
        $this->fillCheckbox(106, 65.5, $data->tax_relief != 'Y');

        $this->pdf->setFontSpacing($letterSpacing3);
        $this->writeStyledText(142, 64.5, $data->tax_relief_details);

        $this->pdf->setFontSpacing($letterSpacing3);
        // $this->writeRightAlignedText(122, 76.2, $data->vat_sales_12a, 25);
        $this->processAndWriteAmount(128, 76.2, $data->part2_12a);
        $this->processAndWriteAmount(178, 76.2, $data->part2_12b);
        $this->processAndWriteAmount(128, 79.5, $data->part2_13a);
        $this->processAndWriteAmount(178, 79.5, $data->part2_13b);
        $this->processAndWriteAmount(128, 83.2, $data->part2_14);
        $this->processAndWriteAmount(128, 86.5, $data->part2_15);
        $this->processAndWriteAmount(128, 91, $data->part2_16a);
        $this->processAndWriteAmount(178, 91, $data->part2_16b);
        $this->processAndWriteAmount(178, 96, $data->part2_17a);
        $this->processAndWriteAmount(178, 100.5, $data->part2_17b);
        $this->processAndWriteAmount(178, 104.5, $data->part2_17c);
        $this->processAndWriteAmount(178, 108.5, $data->part2_17d);
        $this->processAndWriteAmount(178, 112, $data->part2_17e);
        $this->processAndWriteAmount(178, 115.5, $data->part2_17f);
        $this->processAndWriteAmount(128, 122.5, $data->part2_18a);
        $this->processAndWriteAmount(178, 122.5, $data->part2_18b);
        $this->processAndWriteAmount(128, 126, $data->part2_18c);
        $this->processAndWriteAmount(178, 126, $data->part2_18d);
        $this->processAndWriteAmount(128, 130, $data->part2_18e);
        $this->processAndWriteAmount(178, 130, $data->part2_18f);
        $this->processAndWriteAmount(128, 133.5, $data->part2_18g);
        $this->processAndWriteAmount(178, 133.5, $data->part2_18h);
        $this->processAndWriteAmount(128, 137, $data->part2_18i);
        $this->processAndWriteAmount(178, 137, $data->part2_18j);
        $this->processAndWriteAmount(128, 140.5, $data->part2_18k);
        $this->processAndWriteAmount(178, 140.5, $data->part2_18l);
        $this->processAndWriteAmount(128, 144, $data->part2_18m);
        $this->processAndWriteAmount(128, 147.5, $data->part2_18n);
        $this->processAndWriteAmount(178, 147.5, $data->part2_18o);
        $this->processAndWriteAmount(128, 151, $data->part2_18p);
        $this->processAndWriteAmount(178, 155, $data->part2_19);
        $this->processAndWriteAmount(178, 165, $data->part2_20a);
        $this->processAndWriteAmount(178, 169, $data->part2_20b);
        $this->processAndWriteAmount(178, 172, $data->part2_20c);
        $this->processAndWriteAmount(178, 176, $data->part2_20d);
        $this->processAndWriteAmount(178, 179.5, $data->part2_20e);
        $this->processAndWriteAmount(178, 183, $data->part2_20f);
        $this->processAndWriteAmount(178, 186.5, $data->part2_21);
        $this->processAndWriteAmount(178, 190, $data->part2_22);
        $this->processAndWriteAmount(178, 197, $data->part2_23a);
        $this->processAndWriteAmount(178, 201, $data->part2_23b);
        $this->processAndWriteAmount(178, 204.5, $data->part2_23c);
        $this->processAndWriteAmount(178, 208, $data->part2_23d);
        $this->processAndWriteAmount(178, 211.5, $data->part2_23e);
        $this->processAndWriteAmount(178, 215, $data->part2_23f);
        $this->processAndWriteAmount(178, 218.5, $data->part2_23g);
        $this->processAndWriteAmount(178, 222, $data->part2_24);
        $this->processAndWriteAmount(50, 229, $data->part2_25a);
        $this->processAndWriteAmount(94, 230, $data->part2_25b);
        $this->processAndWriteAmount(128, 230, $data->part2_25c);
        $this->processAndWriteAmount(178, 229, $data->part2_25d);
        $this->processAndWriteAmount(178, 233, $data->part2_26);
        $this->processSignatureImage(50, 235, $data->signature_image);

    }
}
