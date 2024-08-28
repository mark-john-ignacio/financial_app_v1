<?php

namespace App\Controllers\API\BIRPDF;

use App\Controllers\BaseController;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF_FONTS;

class BIRPDF2550Q extends BaseController
{
    public function generatePdf()
    {
        try {
            $json = $this->request->getJSON();
            
            // Debugging: Log received data
            log_message('debug', 'Received JSON data: ' . json_encode($json));
    
            // Create a new FPDI instance
            $pdf = new Fpdi();
    
            // Removes the header and footer of the template
            $pdf->setPrintHeader(false);
            
            // Set the source file
            $templatePath = APPPATH . 'Views/PDFTemplates/BIRForm2550-Q.pdf';
            
            // Check if template file exists
            if (!file_exists($templatePath)) {
                throw new \Exception("Template file not found: $templatePath");
            }

            $pageCount = $pdf->setSourceFile($templatePath);

             // Loop through each page of the template
             for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Import the current page
                $tplId = $pdf->importPage($pageNo);
                
                // Get the size of the imported page
                $size = $pdf->getTemplateSize($tplId);
                
                // Add a page with the same size as the template
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                
                // Use the imported page as template
                $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);
                
                 // Fill fields based on the page number
                if ($pageNo == 1) {
                    $this->fillFirstPageFields($pdf, $json);
                } elseif ($pageNo == 2) {
                    $this->fillSecondPageFields($pdf, $json);
                }
            }
            
            // Generate PDF content
            $pdfContent = $pdf->Output('', 'S');
    
            // Debugging: Log PDF size
            log_message('debug', 'Generated PDF size: ' . strlen($pdfContent) . ' bytes');
    
            // Output the PDF
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="generated.pdf"')
                ->setBody($pdfContent);
        } catch (\Exception $e) {
            log_message('error', 'PDF generation failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'PDF generation failed']);
        }
    }

    protected function fillFirstPageFields($pdf, $data)
    {
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $pdf->SetFont($fontname, '', 13);
        $pdf->SetTextColor(0, 0, 0);
    
        $letterSpacing = 2.25;
        $pdf->setFontSpacing($letterSpacing);

        $this->fillCheckbox($pdf, 26, 36, $data->txt2550q_accountingperiods == 'C');
        $this->fillCheckbox($pdf, 50, 36, $data->txt2550q_accountingperiods != 'C');

        // $year_end = explode("/", $data->txt2550q_year_end);
        $this->writeStyledText($pdf, 108, 36, $data->txt2550q_year_end_M);
        $this->writeStyledText($pdf, 118, 36, $data->txt2550q_year_end_Y);

        $this->fillCheckbox($pdf, 153, 36, $data->txt2550q_qrtr == '1');
        $this->fillCheckbox($pdf, 166, 36, $data->txt2550q_qrtr == '2');
        $this->fillCheckbox($pdf, 181, 36, $data->txt2550q_qrtr == '3');
        $this->fillCheckbox($pdf, 196, 36, $data->txt2550q_qrtr == '4');

        $return_preiod_from = explode("/", $data->return_preiod_from);
        $this->writeStyledText($pdf, 23, 46.5, $return_preiod_from[0]);
        $this->writeStyledText($pdf, 33, 46.5, $return_preiod_from[1]);
        $this->writeStyledText($pdf, 43, 46.5, $return_preiod_from[2]);

        $return_preiod_to = explode("/", $data->return_preiod_to);
        $this->writeStyledText($pdf, 72.5, 46.5, $return_preiod_to[0]);
        $this->writeStyledText($pdf, 82.5, 46.5, $return_preiod_to[1]);
        $this->writeStyledText($pdf, 92.5, 46.5, $return_preiod_to[2]);

        $this->fillCheckbox($pdf, 122.5, 46.5, $data->txt2550q_amnd == 'Y');
        $this->fillCheckbox($pdf, 137.6, 46.5, $data->txt2550q_amnd != 'Y');

        $this->fillCheckbox($pdf, 168, 46.5, $data->txt2550q_spr == 'Y');
        $this->fillCheckbox($pdf, 188, 46.5, $data->txt2550q_spr != 'Y');

        $tin = explode("-", $data->txt2550q_tin);
        $this->writeStyledText($pdf, 83, 58, $tin[0]);
        $this->writeStyledText($pdf, 103, 58, $tin[1]);
        $this->writeStyledText($pdf, 123, 58, $tin[2]);
        // $this->writeStyledText($pdf, 143, 58, $tin[3]);

        $this->writeStyledText($pdf, 194, 58, $data->txt2550q_rdo);   
        
        $this->writeStyledText($pdf, 8, 68, $data->txt2550q_taxpayer_name);  

        $this->writeStyledText($pdf, 8, 78.5, $data->txt2550q_add);
        $this->writeStyledText($pdf, 8, 84.5, substr($data->txt2550q_add2, 0, 31));

        $this->writeStyledText($pdf, 8.6, 95, $data->txt2550q_signum);

        $this->writeStyledText($pdf, 73, 95, $data->txt2550q_email);

        $this->fillCheckbox($pdf, 57, 100.5, $data->txt2550q_tax_payer_classification == 'micro');
        $this->fillCheckbox($pdf, 81.7, 100.5, $data->txt2550q_tax_payer_classification == 'small');
        $this->fillCheckbox($pdf, 106.5, 100.5, $data->txt2550q_tax_payer_classification == 'medium');
        $this->fillCheckbox($pdf, 137.5, 101, $data->txt2550q_tax_payer_classification == 'Large');

        $this->fillCheckbox($pdf, 67.5, 107.5, $data->txt2550q_14 == 'Y');
        $this->fillCheckbox($pdf, 83, 107.5, $data->txt2550q_14 != 'Y');

        $this->writeStyledText($pdf, 127.5, 108.5, $data->txt2550q_14A);

        // PART II
        $this->writeFormattedAmount($pdf, 168, 120.5, 184, 120.5, $data->net_vat_payable, 25);

        $this->writeFormattedAmount($pdf, 168, 130.5, 184, 130.5, $data->creditable_vat_withhelding, 25);
        
        $this->writeFormattedAmount($pdf, 168, 137.5, 184, 137.5, $data->advance_vat_payments, 25);

        $this->writeFormattedAmount($pdf, 168, 144, 184, 144, $data->txt2550q_18, 25);

        $this->writeStyledText($pdf, 60.5, 149, $data->specify);

        $this->writeFormattedAmount($pdf, 168, 150.5, 184, 150.5, $data->other_credits_payment, 25);

        $this->writeFormattedAmount($pdf, 168, 156.5, 184, 156.5, $data->total_tax_credits_payments, 25);

        $this->writeFormattedAmount($pdf, 168, 162.5, 184, 162.5, $data->tax_still_payable, 25);

        $this->writeFormattedAmount($pdf, 168, 168.5, 184, 168.5, $data->surcharge, 25);

        $this->writeFormattedAmount($pdf, 168, 175.5, 184, 175.5, $data->interest, 25);

        $this->writeFormattedAmount($pdf, 168, 182.5, 184, 182.5, $data->compromise, 25);

        $this->writeFormattedAmount($pdf, 168, 189, 184, 189, $data->total_penalties, 25);

        $this->writeFormattedAmount($pdf, 168, 195, 184, 195, $data->total_amount_payable, 25);
    }
    
    protected function fillSecondPageFields($pdf, $data)
    { 
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $pdf->SetFont($fontname, '', 12);
        $pdf->SetTextColor(0, 0, 0);
    
        $letterSpacing = 2.3;
        $pdf->setFontSpacing($letterSpacing);

        $tin = explode("-", $data->txt2550q_tin);
        $this->writeStyledText($pdf, 8.5, 30, $tin[0]);
        $this->writeStyledText($pdf, 23, 30, $tin[1]);
        $this->writeStyledText($pdf, 38, 30, $tin[2]);
        // $this->writeStyledText($pdf, 143, 58, $tin[3]);

        $this->writeStyledText($pdf, 77.7, 30, $data->txt2550q_taxpayer_name);  
    
        $this->writeFormattedAmount($pdf, 92, 45, 108, 45, $data->vatable_sales_A, 25);
        $this->writeFormattedAmount($pdf, 168, 45, 183, 45, $data->vatable_sales_B, 25);

        $this->writeFormattedAmount($pdf, 92, 50, 108, 50, $data->zero_rated_sales, 25);

        $this->writeFormattedAmount($pdf, 92, 56, 108, 56, $data->exempt_sales, 25);

        $this->writeFormattedAmount($pdf, 92, 62, 108, 62, $data->total_sales_output_tax_due_A, 25);
        $this->writeFormattedAmount($pdf, 168, 62, 183, 62, $data->total_sales_output_tax_due_A, 25);

        $this->writeFormattedAmount($pdf, 168, 68, 183, 68, $data->output_vat_on_uncollected_recievable, 25);

        $this->writeFormattedAmount($pdf, 168, 74, 183, 74, $data->output_vat_on_recovered_uncollected_recievable, 25);

        $this->writeFormattedAmount($pdf, 168, 79, 183, 79, $data->total_adjusted_output_tax_due, 25);

        $this->writeFormattedAmount($pdf, 168, 89, 183, 89, $data->input_tax_carreid_over_from_previous_quarter, 25);

        $this->writeFormattedAmount($pdf, 168, 94, 183, 94, $data->input_tax_deferred_on_capital_goods, 25);

        $this->writeFormattedAmount($pdf, 168, 100, 183, 100, $data->transitional_input_tax, 25);

        $this->writeFormattedAmount($pdf, 168, 106, 183, 106, $data->presumptive_input_tax, 25);
        
        $this->writeFormattedAmount($pdf, 168, 111, 183, 111, $data->others_42_num, 25);
        $this->writeStyledText($pdf, 40, 110, $data->others_42_txt);

        $this->writeFormattedAmount($pdf, 168, 117, 183, 117, $data->total_43, 25);

        $this->writeFormattedAmount($pdf, 92, 128, 108, 128, $data->domestic_purchases_A, 25);
        $this->writeFormattedAmount($pdf, 168, 128, 183, 128, $data->domestic_purchases_B, 25);

        $this->writeFormattedAmount($pdf, 92, 133, 108, 133, $data->services_rendered_by_non_resident_A, 25);
        $this->writeFormattedAmount($pdf, 168, 133, 183, 133, $data->services_rendered_by_non_resident_B, 25);
       
        $this->writeFormattedAmount($pdf, 92, 139, 108, 139, $data->importations_A, 25);
        $this->writeFormattedAmount($pdf, 168, 139, 183, 139, $data->importations_B, 25);

        $this->writeStyledText($pdf, 35, 143, $data->others_47_A_txt);
        $this->writeFormattedAmount($pdf, 92, 144, 108, 144, $data->others_47_A_num, 25);
        $this->writeFormattedAmount($pdf, 168, 144, 183, 144, $data->others_47_B_num, 25);

        $this->writeFormattedAmount($pdf, 92, 150, 108, 150, $data->domestic_purchases_with_no_input_tax, 25);

        $this->writeFormattedAmount($pdf, 92, 156, 108, 156, $data->vat_exempt_importations, 25);

        $this->writeFormattedAmount($pdf, 92, 161, 108, 161, $data->total_current_purchases_input_tax_A, 25);
        $this->writeFormattedAmount($pdf, 168, 161, 183, 161, $data->total_current_purchases_input_tax_B, 25);

        $this->writeFormattedAmount($pdf, 168, 167, 183, 167, $data->total_available_input_tax, 25);

        $this->writeFormattedAmount($pdf, 168, 177, 183, 177, $data->input_tax_on_purchases, 25);

        $this->writeFormattedAmount($pdf, 168, 183, 183, 183, $data->input_tax_attributable_to_vat_exempt_sales, 25);

        $this->writeFormattedAmount($pdf, 168, 189, 183, 189, $data->vat_refund_tcc_claimed, 25);

        $this->writeFormattedAmount($pdf, 168, 195, 183, 195, $data->input_vat_on_unpaid_payable, 25);

        $this->writeStyledText($pdf, 38, 198.8, $data->others_56_txt);
        $this->writeFormattedAmount($pdf, 168, 200, 183, 200, $data->others_56_num, 25);

        $this->writeFormattedAmount($pdf, 168, 206, 183, 206, $data->total_deductions_from_input_tax, 25);

        $this->writeFormattedAmount($pdf, 168, 211, 183, 211, $data->input_vat_on_settled_unpaid_payables_previously_deducted, 25);

        $this->writeFormattedAmount($pdf, 168, 217, 183, 217, $data->adjusted_deductions_from_input_tax, 25);

        $this->writeFormattedAmount($pdf, 168, 222, 183, 222, $data->total_allowable_input_tax, 25);

        $this->writeFormattedAmount($pdf, 168, 228, 183, 228, $data->net_vat_payable_excess_input_tax, 25);
    }

    protected function writeFormattedAmount($pdf, $xWhole, $yWhole, $xDecimal, $yDecimal, $amount, $fieldWidth)
    {
        // Remove commas and split the amount into whole and decimal parts
        $formattedAmount = explode(".", str_replace(",", "", $amount));
        
        // Write the whole number part
        $this->writeRightAlignedText($pdf, $xWhole, $yWhole, $formattedAmount[0], $fieldWidth);
        
        // Write the decimal part, fallback to '00' if not available
        $this->writeRightAlignedText($pdf, $xDecimal, $yDecimal, $formattedAmount[1] ?? '00', $fieldWidth);
    }

    protected function writeStyledText($pdf, $x, $y, $text, $cellWidth = 200)
    {
        $pdf->SetXY($x, $y);
        $pdf->Cell($cellWidth, 10, strtoupper($text), 0, 0, 'L');
    }

    protected function writeRightAlignedText($pdf, $x, $y, $text, $fieldWidth)
    {
        $pdf->setFontSpacing(0); // Reset font spacing for proper width calculation
        $textWidth = $pdf->GetStringWidth($text);
        $rightAlignedX = $x + $fieldWidth - $textWidth;
        $pdf->SetXY($rightAlignedX, $y);
        $pdf->setFontSpacing(2.55); // Restore your desired letter spacing
        $pdf->Cell($textWidth, 10, strtoupper($text), 0, 0, 'R');
    }

    protected function fillCheckbox($pdf, $x, $y, $condition)
    {
        if ($condition) {
            $this->writeStyledText($pdf, $x, $y, 'X', 10);
        }
    }
}