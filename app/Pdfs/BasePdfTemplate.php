<?php

declare(strict_types=1);

namespace App\Pdfs;

use TCPDF;

abstract class BasePdfTemplate extends TCPDF
{
    protected string $locale;

    protected string $font;

    protected bool $showPageNumbers;

    protected string $pdfTitle;

    public function __construct($locale = 'en', $title = '', $showPageNumbers = true)
    {
        parent::__construct();

        $this->locale = $locale;
        $this->pdfTitle = $title;
        $this->showPageNumbers = $showPageNumbers;
        $this->font = 'dejavusans'; // 'helvetica'; //dejavusans

        // Set document properties
        $this->SetMargins(23, 30, 15);
        $this->setPrintHeader(true);
        $this->setPrintFooter(true);
        $this->SetAutoPageBreak(true, 30);
    }

    // Standard header
    public function Header(): void
    {
        $this->setY(10);
        $this->SetFont($this->font, '', 16);
        $this->Cell(0, 10, 'Magyar Kolónia Berlin e. V.', 0, 1, 'C');
        $this->SetFont($this->font, '', 10);

        $this->Cell(0, 5, $this->pdfTitle, 0, 1, 'C');

        $this->ln(5);
    }

    // Define the footer
    public function Footer(): void
    {
        $this->SetY(-20);

        if ($this->getPage() > 1 && $this->showPageNumbers) {
            $this->SetFont($this->font, 'I', 8);
            $this->Cell(0, 5, 'Seite '.$this->getAliasNumPage().' - '.$this->getAliasNbPages(), 0, 1, 'C');
        }
        $this->SetFont($this->font, '', 7);
        $this->Cell(0, 4, 'Magyar-Kolónia Berlin (Ungarische-Kolonie-Berlin) e.V. | Registernummer 95 VR 1881 Nz', 0, 1, 'C');
        $this->Cell(0, 4, 'Vertreten durch: József Robotka, Präsident | Mátyás Temesi, Stellvertreter', 0, 1, 'C');

    }

    // Abstract method to define content (child class must implement it)
    abstract protected function generateContent();

    // Generate and return PDF
    public function generatePdf($filename): string
    {
        $this->generateContent();

        return $this->Output($filename);
    }

    public function nf(int $value): string
    {
        return number_format($value / 100, 2, ',', '.');
    }
}
