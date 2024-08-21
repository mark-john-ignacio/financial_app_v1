<?php

namespace App\Controllers\API\BIRPDF;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

class BIRPDF extends BaseController
{
    public function generatePdf()
    {
        $json = $this->request->getJSON();

        // Load the PDF template
        $template = $this->loadTemplate('0619-E.pdf');

        // Create a new DOMPDF instance with custom options
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);

        // Load the PDF template into DOMPDF
        $dompdf->loadPdf($template);

        // Get the first page of the PDF
        $page = $dompdf->getCanvas()->get_pages()[0];

        // Fill the fields using positioning
        $this->fillFields($page, $json);

        // Render the PDF
        $dompdf->render();

        // Output the PDF in the browser
        return $this->response
            ->setContentType('application/pdf')
            ->setBody($dompdf->output());
    }

    protected function loadTemplate($templateFile)
    {
        $templatePath = WRITEPATH . 'uploads/bir_pdf_files/' . $templateFile;
        return file_get_contents($templatePath);
    }

    protected function fillFields($page, $data)
    {
        $page->text($data->name, 50, 350, null, 0, 'utf-8');
        $page->text($data->email, 50, 400, null, 0, 'utf-8');
        $page->text($data->message, 50, 450, null, 0, 'utf-8');
    }
}