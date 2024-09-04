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
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplId = $this->pdf->importPage($pageNo);
            $size = $this->pdf->getTemplateSize($tplId);
            $this->pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $this->pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);
        }
    }
    
    protected abstract function fillFields($data);
    
    protected function generatePdf($json, $fileName)
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
        $letterSpacing = $this->pdf->getFontSpacing(); 
        $this->pdf->setFontSpacing(0); // Reset font spacing for proper width calculation
        $textWidth = $this->pdf->GetStringWidth($text);
        $rightAlignedX = $x + $fieldWidth - $textWidth;
        $this->pdf->SetXY($rightAlignedX, $y);
        $this->pdf->setFontSpacing($letterSpacing); // Restore your desired letter spacing
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
        if ($amountString) {
            $cleanedAmountString = str_replace(',', '', $amountString);
            $number = (float)$cleanedAmountString;
            $amountFormatted = number_format($number, 2, '.', '');
            $this->writeRightAlignedText($x, $y, $amountFormatted, $fieldWidth);
        }
    }

    // TODO: Move image inside the ci4 project to make it secure
    // TODO: Create an API for fetching the image
    // TODO: Create a module for setting signature image
    protected function processSignatureImage($x, $y, $imageFileName)
    {
        $imagePath = WRITEPATH . 'uploads/bir_signature/' . $imageFileName;
        $placeholderPath = WRITEPATH . 'uploads/bir_signature/placeholder.webp';

        if (!file_exists($imagePath)) {
            $imagePath = $placeholderPath;
            $imageType = 'WEBP';
        } else {
            $imageType = strtoupper(pathinfo($imagePath, PATHINFO_EXTENSION));
        }

        if (!file_exists($imagePath)) {
            throw new \Exception("Placeholder image file not found: $imagePath");
        }

        $this->pdf->Image($imagePath, $x, $y, 75, 20, $imageType);
    }
}
