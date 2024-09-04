<?php

namespace App\Controllers\API\BIRPDF;
use TCPDF_FONTS;

class BIRPDF2550Q extends BIRPDFBase
{

    public function generatePdf($json = null, $fileName = null)
    {
        $json = $this->request->getJSON();
        $this->loadTemplate('BIRForm2550-Q.pdf');
        $fileName = 'BIR_Form_' . $json->txt2550q_tin . '_' . date('Y-m-d') . '.pdf';
        return parent::generatePdf($json, $fileName);
    }
   
    public function fillFields($data)
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
        $letterSpacing6 = 2.5;

        $this->pdf->setFontSpacing($letterSpacing5);
        $this->fillCheckbox( 26, 36, $data->txt2550q_accountingperiods == 'C');
        $this->fillCheckbox( 50, 36, $data->txt2550q_accountingperiods != 'C');

        $this->writeStyledText( 108, 36, $data->txt2550q_year_end_M);
        $this->writeStyledText( 118, 36, $data->txt2550q_year_end_Y);

        $this->fillCheckbox( 153, 36, $data->txt2550q_qrtr == '1');
        $this->fillCheckbox( 166, 36, $data->txt2550q_qrtr == '2');
        $this->fillCheckbox( 181, 36, $data->txt2550q_qrtr == '3');
        $this->fillCheckbox( 196, 36, $data->txt2550q_qrtr == '4');
        
        $return_preiod_from = explode("/", $data->return_preiod_from);
        $this->writeStyledText( 23, 46.5, $return_preiod_from[0]);
        $this->writeStyledText( 33, 46.5, $return_preiod_from[1]);
        $this->writeStyledText( 43, 46.5, $return_preiod_from[2]);   
        
        $return_preiod_to = explode("/", $data->return_preiod_to);
        $this->writeStyledText( 72.5, 46.5, $return_preiod_to[0]);
        $this->writeStyledText( 82.5, 46.5, $return_preiod_to[1]);
        $this->writeStyledText( 92.5, 46.5, $return_preiod_to[2]);       

        $this->fillCheckbox( 122.5, 46.5, $data->txt2550q_amnd == 'Y');
        $this->fillCheckbox( 137.6, 46.5, $data->txt2550q_amnd != 'Y');

        $this->fillCheckbox( 168, 46.5, $data->txt2550q_spr == 'Y');
        $this->fillCheckbox( 188, 46.5, $data->txt2550q_spr != 'Y');

        $tin = explode("-", $data->txt2550q_tin);
        $this->writeStyledText( 83, 58, $tin[0]);
        $this->writeStyledText( 103, 58, $tin[1]);
        $this->writeStyledText( 123, 58, $tin[2]);
        // $this->writeStyledText( 143, 58, $tin[3]);

        $this->writeStyledText( 194, 58, $data->txt2550q_rdo);   
        
        $this->writeStyledText( 8, 68, $data->txt2550q_taxpayer_name);  

        $this->writeStyledText( 8, 78.5, $data->txt2550q_add);
        $this->writeStyledText( 8, 84.5, substr($data->txt2550q_add2, 0, 31));

        $this->writeStyledText( 188, 84.5, $data->txt2550q_zip);

        $this->writeStyledText( 8.6, 95, $data->txt2550q_signum);

        $this->writeStyledText( 73, 95, $data->txt2550q_email);

        $this->fillCheckbox( 57, 100.5, $data->txt2550q_tax_payer_classification == 'micro');
        $this->fillCheckbox( 81.7, 100.5, $data->txt2550q_tax_payer_classification == 'small');
        $this->fillCheckbox( 106.5, 100.5, $data->txt2550q_tax_payer_classification == 'medium');
        $this->fillCheckbox( 137.5, 101, $data->txt2550q_tax_payer_classification == 'Large');

        $this->fillCheckbox( 67.5, 107.5, $data->txt2550q_14 == 'Y');
        $this->fillCheckbox( 83, 107.5, $data->txt2550q_14 != 'Y');

        $this->writeStyledText( 127.5, 108.5, substr($data->txt2550q_14A, 0, 31));

        // PART II
        $this->processAndWriteAmount( 183, 120.5, $data->net_vat_payable, );

        $this->processAndWriteAmount( 183, 130.5, $data->creditable_vat_withhelding, );
        
        $this->processAndWriteAmount( 183, 137.5, $data->advance_vat_payments, );

        $this->processAndWriteAmount(  183, 144, $data->txt2550q_18, );

        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText( 60.5, 149, $data->specify);

        $this->pdf->setFontSpacing($letterSpacing5);
        $this->processAndWriteAmount(  183, 150.5, $data->other_credits_payment, );

        $this->processAndWriteAmount(  183, 156.5, $data->total_tax_credits_payments, );

        $this->processAndWriteAmount(  183, 162.5, $data->tax_still_payable, );

        $this->processAndWriteAmount(  183, 168.5, $data->surcharge, );

        $this->processAndWriteAmount(  183, 175.5, $data->interest, );

        $this->processAndWriteAmount(  183, 182.5, $data->compromise, );

        $this->processAndWriteAmount(  183, 189, $data->total_penalties, );

        $this->processAndWriteAmount(  183, 195, $data->total_amount_payable, );


        $this->pdf->SetPage(2);
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $this->pdf->SetFont($fontname, '', 13);
        $this->pdf->SetTextColor(0, 0, 0);

        $this->pdf->setFontSpacing($letterSpacing5);
        $tin = explode("-", $data->txt2550q_tin);
        $this->writeStyledText( 8.5, 30, $tin[0]);
        $this->writeStyledText( 23, 30, $tin[1]);
        $this->writeStyledText( 38, 30, $tin[2]);
        // $this->writeStyledText( 143, 58, $tin[3]);

        $this->writeStyledText( 77.7, 30, $data->txt2550q_taxpayer_name);  
    
        $this->processAndWriteAmount( 108, 45, $data->vatable_sales_A, );
        $this->processAndWriteAmount( 183, 45, $data->vatable_sales_B, );

        $this->processAndWriteAmount(  108, 50, $data->zero_rated_sales, );

        $this->processAndWriteAmount( 108, 56, $data->exempt_sales, );

        $this->processAndWriteAmount( 108, 62, $data->total_sales_output_tax_due_A, );
        $this->processAndWriteAmount( 183, 62, $data->total_sales_output_tax_due_A, );

        $this->processAndWriteAmount( 183, 68, $data->output_vat_on_uncollected_recievable, );

        $this->processAndWriteAmount( 183, 74, $data->output_vat_on_recovered_uncollected_recievable, );

        $this->processAndWriteAmount( 183, 79, $data->total_adjusted_output_tax_due, );

        $this->processAndWriteAmount( 183, 89, $data->input_tax_carreid_over_from_previous_quarter, );

        $this->processAndWriteAmount( 183, 94, $data->input_tax_deferred_on_capital_goods, );

        $this->processAndWriteAmount( 183, 100, $data->transitional_input_tax, );

        $this->processAndWriteAmount( 183, 106, $data->presumptive_input_tax, );
        
        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText( 40, 110, $data->others_42_txt);

        $this->pdf->setFontSpacing($letterSpacing5);
        $this->processAndWriteAmount( 183, 111, $data->others_42_num, );

        $this->processAndWriteAmount(183, 117, $data->total_43, );

        $this->processAndWriteAmount( 108, 128, $data->domestic_purchases_A, );
        $this->processAndWriteAmount( 183, 128, $data->domestic_purchases_B, );

        $this->processAndWriteAmount( 108, 133, $data->services_rendered_by_non_resident_A, );
        $this->processAndWriteAmount( 183, 133, $data->services_rendered_by_non_resident_B, );
       
        $this->processAndWriteAmount( 108, 139, $data->importations_A, );
        $this->processAndWriteAmount( 183, 139, $data->importations_B, );

        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText( 35, 143, $data->others_47_A_txt);

        $this->pdf->setFontSpacing($letterSpacing5);
        $this->processAndWriteAmount( 108, 144, $data->others_47_A_num, );
        $this->processAndWriteAmount( 183, 144, $data->others_47_B_num, );

        $this->processAndWriteAmount( 108, 150, $data->domestic_purchases_with_no_input_tax, );

        $this->processAndWriteAmount( 108, 156, $data->vat_exempt_importations, );

        $this->processAndWriteAmount( 108, 161, $data->total_current_purchases_input_tax_A, );
        $this->processAndWriteAmount( 183, 161, $data->total_current_purchases_input_tax_B, );

        $this->processAndWriteAmount( 183, 167, $data->total_available_input_tax, );

        $this->processAndWriteAmount( 183, 177, $data->input_tax_on_purchases, );

        $this->processAndWriteAmount( 183, 183, $data->input_tax_attributable_to_vat_exempt_sales, );

        $this->processAndWriteAmount( 183, 189, $data->vat_refund_tcc_claimed, );

        $this->processAndWriteAmount( 183, 195, $data->input_vat_on_unpaid_payable, );

        $this->pdf->setFontSpacing($letterSpacing);
        $this->writeStyledText( 38, 198.8, $data->others_56_txt);
        
        $this->pdf->setFontSpacing($letterSpacing5);
        $this->processAndWriteAmount( 183, 200, $data->others_56_num, );

        $this->processAndWriteAmount( 183, 206, $data->total_deductions_from_input_tax, );

        $this->processAndWriteAmount( 183, 211, $data->input_vat_on_settled_unpaid_payables_previously_deducted, );

        $this->processAndWriteAmount( 183, 217, $data->adjusted_deductions_from_input_tax, );

        $this->processAndWriteAmount( 183, 222, $data->total_allowable_input_tax, );

        $this->processAndWriteAmount( 183, 228, $data->net_vat_payable_excess_input_tax, );
    }   
}