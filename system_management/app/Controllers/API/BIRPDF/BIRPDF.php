<?php

namespace App\Controllers\API\BIRPDF;

use App\Controllers\BaseController;
use setasign\Fpdi\Tcpdf\Fpdi;

class BIRPDF extends BaseController
{
    public function generatePdf()
    {
        $json = $this->request->getJSON();
        
        // Create a new FPDI instance
        $pdf = new Fpdi();
        
        // Set the source file
        $templatePath = WRITEPATH . 'uploads/bir_pdf_files/0619-E.pdf';
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
        
        // Output the PDF in the browser
        return $this->response
            ->setContentType('application/pdf')
            ->setBody($pdf->Output('', 'S'));
    }

    protected function fillFields($pdf, $data)
    {
        $pdf->SetFont('Courier', 'B');  // 'B' for bold
        $pdf->SetFontSize(13);  // Set font size to 20
        $pdf->SetTextColor(0, 0, 0);  // Set text color to black (#000)

        $this->writeStyledText($pdf, 50, 50, $data->name);
        $this->writeStyledText($pdf, 50, 60, $data->email);
        $this->writeStyledText($pdf, 50, 70, $data->message);
    }

    protected function writeStyledText($pdf, $x, $y, $text)
    {
        $pdf->SetXY($x, $y);
        $pdf->Write(0, strtoupper($text));
    }
}