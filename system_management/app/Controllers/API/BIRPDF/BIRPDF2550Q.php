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

            
            // Import the first page of the template
            // $tplId = $pdf->importPage(1);
            
            // Get the size of the imported page
            // $size = $pdf->getTemplateSize($tplId);
            
            // Add a page with the same size as the template
            // $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            
            // Use the imported page as template
            // $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);
            
            // Fill the fields using positioning
            // $this->fillFields($pdf, $json);
            
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
    
        $letterSpacing = 2.1;
        $pdf->setFontSpacing($letterSpacing);
    
        // Fill fields on the first page
        $this->writeStyledText($pdf, 20, 47, $data->txt2550q_add);
        // Add more fields for the first page as needed
    }
    
    protected function fillSecondPageFields($pdf, $data)
    { 
        $fontPath = APPPATH . 'Fonts/SourceCodePro-Bold.ttf';
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
        $pdf->SetFont($fontname, '', 13);
        $pdf->SetTextColor(0, 0, 0);
    
        $letterSpacing = 2.1;
        $pdf->setFontSpacing($letterSpacing);
    
        // Fill fields on the second page
        $this->writeStyledText($pdf, 17, 47, $data->txt2550q_add2); // Example field for second page
        // Add more fields for the second page as needed
    }

    protected function writeStyledText($pdf, $x, $y, $text)
    {
        $pdf->SetXY($x, $y);
        $pdf->Write(0, strtoupper($text));
    }
}