<?php

namespace App\Controllers\API\BIRPDF;

use App\Controllers\BaseController;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF_FONTS;

class BIRPDF0619E extends BaseController
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
            $templatePath = APPPATH . 'Views/PDFTemplates/BIRForm0619-E.pdf';
            
            // Check if template file exists
            if (!file_exists($templatePath)) {
                throw new \Exception("Template file not found: $templatePath");
            }
    
            $pageCount = $pdf->setSourceFile($templatePath);
            
            // Import the first page of the template
            $tplId = $pdf->importPage(1);
            
            // Get the size of the imported page
            $size = $pdf->getTemplateSize($tplId);
            
            // Add a page with the same size as the template
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            
            // Use the imported page as template
            $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);
            
            // Fill the fields using positioning
            $this->fillFields($pdf, $json);
            
            // Generate PDF content
            $pdfContent = $pdf->Output('', 'S');

            $fileName = 'BIR_Form_' . $json->taxpayer_tin . '_' . date('Y-m-d') . '.pdf';
    
            // Debugging: Log PDF size
            log_message('debug', 'Generated PDF size: ' . strlen($pdfContent) . ' bytes');
    
            // Output the PDF
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
                ->setHeader('X-Filename', $fileName)
                ->setBody($pdfContent);
        } catch (\Exception $e) {
            log_message('error', 'PDF generation failed: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setHeader('Content-Type', 'application/json')
                ->setBody(json_encode([
                    'error' => 'PDF generation failed',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]));
        }
    }

    protected function fillFields($pdf, $data)
    {
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $pdf->SetFont($fontname, '', 12);
        $pdf->SetTextColor(0, 0, 0);

        $letterSpacing = 2.55;
        $pdf->setFontSpacing($letterSpacing);
    
        $this->writeStyledText($pdf, 16.5, 45, $data->month);
        $this->writeStyledText($pdf, 26.5,45, $data->year);
        $this->writeStyledText($pdf, 52, 45, $data->due_month);
        $this->writeStyledText($pdf, 62, 45, $data->due_day);
        $this->writeStyledText($pdf, 72, 45, $data->due_year);

        $this->fillCheckbox($pdf, 97, 44, $data->amended_form == 'Y');
        $this->fillCheckbox($pdf, 112.5, 44, $data->amended_form != 'Y');
        $this->fillCheckbox($pdf, 133, 44, $data->taxes_withheld == 'Y');
        $this->fillCheckbox($pdf, 148, 44, $data->taxes_withheld != 'Y');

        $this->writeStyledText($pdf, 77, 57, $data->taxpayer_tin);
        $this->writeStyledText($pdf, 189, 57, $data->rdo_code);
        $this->writeStyledText($pdf, 6.5, 67, $data->withholding_agent_name);

        $this->writeStyledText($pdf, 6.5, 77, $data->registered_address);
        $this->writeStyledText($pdf, 6.5, 83.5, substr($data->registered_address_continued, 0, 31));
        $this->writeStyledText($pdf, 189, 83.5, $data->zip_code);
        $this->writeStyledText($pdf, 36.5, 90, $data->contact_number);
        $this->fillCheckbox($pdf, 157, 90, $data->withholding_agent_category == 'P');
        $this->fillCheckbox($pdf, 182, 90, $data->withholding_agent_category != 'P');
        $this->writeStyledText($pdf, 6.5, 100, $data->email_address);

        $this->processAndWriteAmount($pdf, 184.3, 112.25, $data->amount_of_remittance);
        $this->processAndWriteAmount($pdf, 184.3, 118.9, $data->amount_remitted_previous);
        $this->processAndWriteAmount($pdf, 184.3, 125.25, $data->net_amount_of_remittance);
        $this->processAndWriteAmount($pdf, 184.3, 135.5, $data->penalty_surcharge);
        $this->processAndWriteAmount($pdf, 184.3, 142, $data->penalty_interest);
        $this->processAndWriteAmount($pdf, 184.3, 148.5, $data->penalty_compromise);
        $this->processAndWriteAmount($pdf, 184.3, 155, $data->total_penalties);
        $this->processAndWriteAmount($pdf, 184.3, 161.5, $data->total_amount_of_remittance);
        $this->processSignatureImage($pdf, 120, 176, $data->signature_image);
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

    protected function processAndWriteAmount($pdf, $x, $y, $amountString, $fieldWidth = 25)
    {
        $cleanedAmountString = str_replace(',', '', $amountString);
        $number = (float)$cleanedAmountString;
        $amountFormatted = number_format($number, 2, '.', '');
        $this->writeRightAlignedText($pdf, $x, $y, $amountFormatted, $fieldWidth);
    }

    protected function processSignatureImage($pdf, $x, $y, $imageRelativePath)
    {
        $imageRelativePath = preg_replace('/^\.\.\//', '', $imageRelativePath);
        $baseURL = base_url();
        $trimmedBaseURL = str_replace('/system_management/public', '', $baseURL);
        $signatureImageUrl = $trimmedBaseURL . $imageRelativePath;
        
        // Create a stream context to bypass SSL verification
        $contextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $context = stream_context_create($contextOptions);
        
        // Fetch the image from the URL with the context
        $imageContent = file_get_contents($signatureImageUrl, false, $context);
        
        if ($imageContent !== false) {
            // Save the image to a temporary file
            $tempImagePath = tempnam(sys_get_temp_dir(), 'signature_') . '.png';
            file_put_contents($tempImagePath, $imageContent);
        
            // Add the image to the PDF
            $pdf->Image($tempImagePath, $x, $y, 80, 0, 'PNG'); // (file, x, y, width, height, type)
        
            // Clean up the temporary file
            unlink($tempImagePath);
        } else {
            // Handle the error if the image could not be fetched
            echo 'Image could not be fetched from URL: ' . $signatureImageUrl;
        }
    }
}