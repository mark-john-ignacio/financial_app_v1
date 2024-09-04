<?php

namespace App\Controllers\API\BIRPDF;
use TCPDF_FONTS;

class BIRPDF1601EQ extends BIRPDFBase
{
    public function generatePdf($json = null, $fileName = null)
    {
        $json = $this->request->getJSON();
        $this->loadTemplate('BIRForm1601-EQ.pdf');
        $fileName = 'BIR_Form_' . $json->txt1601eq_tin . '_' . date('Y-m-d') . '.pdf';
        return parent::generatePdf($json, $fileName);
    }

    protected function fillFields($data)
    {
        $this->pdf->SetPage(1);
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $this->pdf->SetFont($fontname, '', 13);
        $this->pdf->SetTextColor(0, 0, 0);
        $letterSpacing = 1;
        $letterSpacing2 = 2.8;
        $letterSpacing3 = -0.3;
        $letterSpacing4 = -0.6;
        $letterSpacing5 = 2.25;
        $letterSpacing6 = 2.3;

        $this->pdf->setFontSpacing($letterSpacing5);
        $this->writeStyledText( 11, 37.5, $data->txt1601eq_yr);

        $this->fillCheckbox( 41.6, 37.3, $data->txt1601eq_qrtr == '1');
        $this->fillCheckbox( 57, 37.3, $data->txt1601eq_qrtr == '2');
        $this->fillCheckbox( 72.4, 37.3, $data->txt1601eq_qrtr == '3');
        $this->fillCheckbox( 87.5, 37.3, $data->txt1601eq_qrtr == '4');

        $this->fillCheckbox( 107.7, 37.4, $data->txt1601eq_amnd == 'Y');
        $this->fillCheckbox( 123.4, 37.4, $data->txt1601eq_amnd != 'Y');

        $this->fillCheckbox( 143.5, 37.3, $data->txt1601eq_anytx == 'Y');
        $this->fillCheckbox( 158.5, 37.3, $data->txt1601eq_anytx != 'Y');

        $this->writeRightAlignedText( 169, 37.3, $data->txt1601eq_nosheets, 25);   

        $tin = explode("-", $data->txt1601eq_tin);
        $this->writeStyledText( 83, 49, $tin[0]);
        $this->writeStyledText( 103, 49, $tin[1]);
        $this->writeStyledText( 123, 49, $tin[2]);
        $this->writeStyledText( 143, 49, $tin[3]);

        $this->writeStyledText( 194, 49, $data->txt1601eq_rdo);  

        
        $this->writeStyledText( 6.8, 59, $data->txt1601eq_nme);  

       ;
        $this->pdf->setFontSpacing($letterSpacing6);
        $this->writeStyledText( 7, 69, $data->txt1601eq_add);
        $this->writeStyledText( 7, 75, substr($data->txt1601eq_add2, 0, 31));
        $this->pdf->setFontSpacing($letterSpacing5);
        $this->writeStyledText( 189, 75.5, $data->txt1601eq_zip);

        $this->writeStyledText( 37, 81, $data->txt1601eq_signum);  

        $this->fillCheckbox( 158, 81, $data->txt1601eq_cat == 'P');
        $this->fillCheckbox( 183.5, 81, $data->txt1601eq_cat != 'P');

        $this->writeStyledText( 37, 87, $data->txt1601eq_email);

        $y = 103;
        for ($i=1; $i <= 6; $i++) { 
            $this->writeStyledText( 12, $y, $data->{'txt1601eq_atc'.$i} ?? '');
            
            $this->processAndWriteAmount( 87.5, $y, $data->{'txt1601eq_gross'.$i} ?? '');

            $this->pdf->setFontSpacing($letterSpacing3);
            $this->processAndWriteAmount( 113, $y,  $data->{'txt1601eq_rate'.$i} ?? '');

            $this->pdf->setFontSpacing($letterSpacing5);
            $this->processAndWriteAmount( 183.8, $y, $data->{'txt1601eq_tax'.$i} ?? '');
            $y += 6;
        }
        
        $this->processAndWriteAmount( 183.8, 140, $data->txt1601eq_totewt);
        
        $this->processAndWriteAmount( 183.8, 146, $data->txt1601eq_less1);

        $this->processAndWriteAmount( 183.8, 152, $data->txt1601eq_less2);
        
        $this->processAndWriteAmount( 183.8, 158, $data->txt1601eq_prev);

        $this->processAndWriteAmount( 183.8, 164, $data->txt1601eq_overr);

        $this->processAndWriteAmount( 183.8, 170, $data->txt1601eq_otrpay);

        $this->processAndWriteAmount( 183.8, 176, $data->txt1601eq_totrem);

        $this->processAndWriteAmount( 183.8, 184, $data->txt1601eq_taxdue);

        $this->processAndWriteAmount( 183.8, 190, $data->txt1601eq_pensur);

        $this->processAndWriteAmount( 183.8, 196, $data->txt1601eq_penint);

        $this->processAndWriteAmount( 183.8, 202, $data->txt1601eq_pencom);

        $this->processAndWriteAmount( 183.8, 208, $data->txt1601eq_pentot);

        $this->processAndWriteAmount( 183.8, 214, $data->txt1601eq_gtot);

        // $this->fillCheckbox( 67.5, 219.5, $data->txt1601eq_ifovr == '1');
        // $this->fillCheckbox(94.5, 219.5, $data->txt1601eq_ifovr == '2');
        // $this->fillCheckbox(146, 219.5, $data->txt1601eq_ifovr == '3');
        



        $this->pdf->SetPage(2);
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $this->pdf->SetFont($fontname, '', 13);
        $this->pdf->SetTextColor(0, 0, 0);

        $tin = explode("-", $data->txt1601eq_tin);
        $this->writeStyledText( 6.5, 37, $tin[0]);
        $this->writeStyledText( 21.5, 37, $tin[1]);
        $this->writeStyledText( 36.5, 37, $tin[2]);
        $this->writeStyledText( 51.7, 37, $tin[3]);

        $this->writeStyledText( 77.7, 37, $data->txt1601eq_nme);  
    }
}