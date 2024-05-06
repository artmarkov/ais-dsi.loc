<?php

use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;

function convertDocToHtml($docFilePath)
{
    $phpWord = IOFactory::load($docFilePath);
    return IOFactory::createWriter($phpWord, 'HTML')->save('php://memory');
}

function convertHtmlToPdf($html, $pdfFilePath)
{
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    file_put_contents($pdfFilePath, $dompdf->output());
}

// Example usage
$docFilePath = 'document/contract_student_free.docx';
$pdfFilePath = 'document/contract_student_free.pdf';

$htmlContent = convertDocToHtml($docFilePath);
convertHtmlToPdf($htmlContent, $pdfFilePath);

echo 'Conversion completed!';