<?php

namespace App\Controllers\API\BIRPDF;
use TCPDF_FONTS;
class BIRPDF2307 extends BIRPDFBase
{
    public function generatePdf($json = null, $fileName = null)
    {
        $json = $this->request->getJSON();
        $this->loadTemplate('BIRForm2307.pdf');
        // $fileName = 'BIR_Form_' . $json->taxpayer_tin . '_' . date('Y-m-d') . '.pdf';
        return parent::generatePdf($json, $fileName);
    }

    protected function fillFields($data)
    {
        $this->pdf->SetPage(1);
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $this->pdf->SetFont($fontname, '', 11);
        $this->pdf->SetTextColor(0, 0, 0);
        $letterSpacing = 0;
        $letterSpacing2 = 2.7;
        $letterSpacing3 = 2.1;
        $letterSpacing4 = -0.3;


        // NEED TO ADD THE PERIOD DATE BASED ON QUARTE IF FISCAL IS THE TYPE OF YEAR
        $this->pdf->setFontSpacing($letterSpacing2); 
        $date1 = explode("-", $data->date1);
        $this->writeStyledText(53.5, 35.5, $date1[0]);
        $this->writeStyledText(62.5, 35.5, $date1[1]);
        $this->writeStyledText(71.5, 35.5, $date1[2]);

        $date2 = explode("-", $data->date2);
        $this->writeStyledText(140.5, 35.5, $date2[0]);
        $this->writeStyledText(150.5, 35.5, $date2[1]);
        $this->writeStyledText(159, 35.5, $date2[2]);
        
        
        $payeetin = isset($data->payeetin) ? explode(".", $data->payeetin) : [];

        // Set default values if any part of $payeetin is missing
        $payeetin[0] = isset($payeetin[0]) ? $payeetin[0] : '';
        $payeetin[1] = isset($payeetin[1]) ? $payeetin[1] : '';
        $payeetin[2] = isset($payeetin[2]) ? $payeetin[2] : '';
        $payeetin[3] = isset($payeetin[3]) ? $payeetin[3] : '';
        
        // Write text with default or actual values
        $this->writeStyledText(72.8, 46, $payeetin[0]);
        $this->writeStyledText(91, 46, $payeetin[1]);
        $this->writeStyledText(109, 46, $payeetin[2]);
        $this->writeStyledText(128.4, 46, $payeetin[3]);
        

        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText(12, 55.5, $data->payeename);
        $this->writeStyledText(12, 65.7, substr($data->payeeadd, 0 , 72));
        // ADD THIS NEXT LINE IF THERE FOREIGN ADDRESS JUST CHANGE THE payeeadd
        // $this->writeStyledText(12, 65.7, substr($data->payeeadd, 0 , 77));

        $this->pdf->setFontSpacing($letterSpacing3);
        $this->writeStyledText(191, 66, $data->payeezip);
        
        $this->pdf->setFontSpacing($letterSpacing2); 
        $comptin = explode(".", $data->comptin);
        $this->writeStyledText(73.5, 87, $comptin[0]);
        $this->writeStyledText(91.5, 87, $comptin[1]);
        $this->writeStyledText(109.5, 87, $comptin[2]);
        $this->writeStyledText(128.5, 87, $comptin[3]);

        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText(12, 96.5, $data->compname);
        $this->writeStyledText(12, 106.5, substr($data->compadd, 0 , 72));

        $this->pdf->setFontSpacing($letterSpacing3);
        $this->writeStyledText(191, 106.5, $data->compzip);

        //Income Payments Subject to Expanded Withholding Tax
        $xValTxt = 125;
        $xValNum = 126;
        $count = 0; // if need to limit the number of lopp that will be display based on the number of field on the form

        foreach ($data->details as $detail ) {
            //increment the xValTxt by 2.4
            //increment the xValTxt by 4.9
            
            // if need to limit the number of lopp that will be display based on the number of field in the form
            if ($count >= 10) {
                break;
            }
            $count++;

            // Calculate the number of chunks needed
            $textLength = strlen($detail->ewtdesc);
            $chunks = ceil($textLength / 58);

            for ($i = 0; $i < $chunks; $i++) {

                $start = $i * 58;


                $chunk = substr($detail->ewtdesc, $start, 58); 
                $this->pdf->SetFont($fontname, '', 4.5);
                $this->pdf->setFontSpacing($letterSpacing);
                $this->writeStyledText(6, $xValTxt, $chunk);

                $xValTxt += 2.4;

                if ($i == 0) {
                    $this->pdf->SetFont($fontname, '', 9);
                    $this->writeStyledText(64.5, $xValNum, $detail->cewtcode);
        
                    $this->writeRightAlignedText( 78.5, $xValNum, $detail->amount, 25);
                    //$this->writeRightAlignedText( 104.7, $xValNum, $detail->amount, 25);//change the amount depend on the name of the data
                    //$this->writeRightAlignedText( 130, $xValNum, $detail->amount, 25);//change the amount depend on the name of the data
                    $this->writeRightAlignedText( 155.5, $xValNum, $detail->amount, 25);//change the amount depend on the name of the data
        
                    $this->writeRightAlignedText( 185.5, $xValNum, $detail->newtamt, 25);
                }
                $xValNum += 4.9;
            }

        }
        
        $this->pdf->SetFont($fontname, '', 9);
        // $this->writeRightAlignedText( 78.5, 174.4, $data->totdues, 25);
        //$this->writeRightAlignedText( 104.7, 174.4, $data->amount, 25);//change the amount depend on the name of the data
        //$this->writeRightAlignedText( 130, 174.4, $data->amount, 25);//change the amount depend on the name of the data
        $this->writeRightAlignedText( 155.5, 174.4, $data->totdues, 25);//change the amount depend on the name of the data
        $this->writeRightAlignedText( 185.5, 174.4, $data->totewts, 25);

        $this->pdf->SetFont($fontname, '', 9);
      
        $this->processSignatureImage(77, 247, $data->bir_sig_sign);


        // Change the value here 
        // Money Payments Subject to Withholding of Business Tax (Government & Private)
        // $xValTxt = 185;
        // $xValNum = 185.2;
        // $count = 0; // if need to limit the number of lopp that will be display based on the number of field on the form
        // // $count = count($data->details);
        // foreach ($data->details as $detail ) {
        //     //increment the xValTxt by 2.4
        //     //increment the xValTxt by 5
            
        //     // if need to limit the number of lopp that will be display based on the number of field in the form
        //     if ($count >= 10) {
        //         break;
        //     }
        //     $count++;

        //     $this->pdf->SetFont($fontname, '', 4.5);
        //     $this->pdf->setFontSpacing($letterSpacing);
        //     $this->writeStyledText(6, $xValTxt ,substr( $detail->ewtdesc, 0, 58 ));

        //     $xValTxt += 2.4;

        //     $this->writeStyledText(6, $xValTxt,substr( $detail->ewtdesc, 58, 80 ));

        //     $this->pdf->SetFont($fontname, '', 9);
        //     $this->writeStyledText(64.5, 126, $detail->cewtcode);

        //     $this->writeRightAlignedText( 78.5, $xValNum, $detail->amount, 25);
        //     $this->writeRightAlignedText( 104.7, $xValNum, $detail->amount, 25);//change the amount depend on the name of the data
        //     $this->writeRightAlignedText( 130, $xValNum, $detail->amount, 25);//change the amount depend on the name of the data
        //     $this->writeRightAlignedText( 155.5, $xValNum, $detail->amount, 25);//change the amount depend on the name of the data

        //     $this->writeRightAlignedText( 185.5, $xValNum, $detail->newtamt, 25);

        //     $xValNum += 5;
        //     $xValTxt += 2.4;
        // }
        
        // $this->pdf->SetFont($fontname, '', 9);

        // $this->writeRightAlignedText( 78.5, 235, $data->amount, 25);
        // $this->writeRightAlignedText( 104.7, 235, $data->amount, 25);//change the amount depend on the name of the data
        // $this->writeRightAlignedText( 130, 235, $data->amount, 25);//change the amount depend on the name of the data
        // $this->writeRightAlignedText( 155.5, 235, $data->amount, 25);//change the amount depend on the name of the data
        // $this->writeRightAlignedText( 185.5, 235, $data->newtamt, 25);

    }
}
