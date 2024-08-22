<?php

namespace App\Controllers\API\BIRPDF;

use App\Controllers\BaseController;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF_FONTS;

abstract class BIRPDFBase extends BaseController
{
    protected $pdf;
    protected $templatePath;
    
    public function __construct()
    {
        $this->pdf = new Fpdi();
        $this->pdf->setPrintHeader(false);
    }
    
    protected function loadTemplate($templateName)
    {
        $this->templatePath = APPPATH . 'Views/PDFTemplates/' . $templateName;
        
        if (!file_exists($this->templatePath)) {
            throw new \Exception("Template file not found: $this->templatePath");
        }
        
        $pageCount = $this->pdf->setSourceFile($this->templatePath);
        $tplId = $this->pdf->importPage(1);
        
        $size = $this->pdf->getTemplateSize($tplId);
        $this->pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $this->pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);
    }
    
    protected abstract function fillFields($data);
    
    protected function generatePdfBase($json, $fileName)
    {
        try {

            $this->fillFields($json);
            $pdfContent = $this->pdf->Output('', 'S');
    
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
    
    protected function writeStyledText($x, $y, $text, $cellWidth = 200)
    {
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell($cellWidth, 10, strtoupper($text), 0, 0, 'L');
    }

    protected function writeRightAlignedText($x, $y, $text, $fieldWidth)
    {
        $this->pdf->setFontSpacing(0); // Reset font spacing for proper width calculation
        $textWidth = $this->pdf->GetStringWidth($text);
        $rightAlignedX = $x + $fieldWidth - $textWidth;
        $this->pdf->SetXY($rightAlignedX, $y);
        $this->pdf->setFontSpacing(2.55); // Restore your desired letter spacing
        $this->pdf->Cell($textWidth, 10, strtoupper($text), 0, 0, 'R');
    }

    protected function fillCheckbox($x, $y, $condition)
    {
        if ($condition) {
            $this->writeStyledText($x, $y, 'X', 10);
        }
    }

    protected function processAndWriteAmount($x, $y, $amountString, $fieldWidth = 25)
    {
        $cleanedAmountString = str_replace(',', '', $amountString);
        $number = (float)$cleanedAmountString;
        $amountFormatted = number_format($number, 2, '.', '');
        $this->writeRightAlignedText($x, $y, $amountFormatted, $fieldWidth);
    }

    protected function processSignatureImage($x, $y, $imageRelativePath)
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
            $this->pdf->Image($tempImagePath, $x, $y, 80, 0, 'PNG'); // (file, x, y, width, height, type)
        
            // Clean up the temporary file
            unlink($tempImagePath);
        } else {
            // Handle the error if the image could not be fetched
            echo 'Image could not be fetched from URL: ' . $signatureImageUrl;
        }
    }
}
