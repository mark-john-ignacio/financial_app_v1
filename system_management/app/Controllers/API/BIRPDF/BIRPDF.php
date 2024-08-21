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

        // Add a page
        $pdf->AddPage();

        // Set the source file
        $pdf->setSourceFile(WRITEPATH . 'uploads/bir_pdf_files/0619-E.pdf');

        // Import the first page of the template
        $tplId = $pdf->importPage(1);

        // Use the imported page as template
        $pdf->useTemplate($tplId);

        // Fill the fields using positioning
        $this->fillFields($pdf, $json);

        // Output the PDF in the browser
        return $this->response
            ->setContentType('application/pdf')
            ->setBody($pdf->Output('', 'S'));
    }

    protected function fillFields($pdf, $data)
    {
        $pdf->SetFont('Helvetica');
        $pdf->SetFontSize(12);

        $pdf->SetXY(50, 50);  // Adjust these coordinates as needed
        $pdf->Write(0, $data->name);

        $pdf->SetXY(50, 60);  // Adjust these coordinates as needed
        $pdf->Write(0, $data->email);

        $pdf->SetXY(50, 70);  // Adjust these coordinates as needed
        $pdf->Write(0, $data->message);
    }
}