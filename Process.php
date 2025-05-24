<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$extractedText = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $allowedExtensions = ['txt', 'csv', 'pdf'];

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = basename($_FILES['file']['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        $extractedText = "Error: Unsupported file type. Only TXT, CSV, and PDF are allowed.";
    } else {
        $safeFileName = preg_replace("/[^a-zA-Z0-9\.-]/", "", $fileName);
        $filePath = $uploadDir . $safeFileName;

        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $extractedText = extractTextFromFile($filePath);
        } else {
            $extractedText = "Error uploading the file. Check folder permissions.";
        }
    }
}

function extractTextFromFile($filePath)
{
    require 'vendor/autoload.php';  

    $extension = pathinfo($filePath, PATHINFO_EXTENSION);

    switch (strtolower($extension)) {
        case 'txt':
            return file_get_contents($filePath);
        case 'csv':
            return extractTextFromCSV($filePath);
        case 'pdf':
            return extractTextFromPDF($filePath);
        default:
            return "Unsupported file type: $extension";
    }
}

function extractTextFromCSV($filePath)
{
    $text = '';
    if (($handle = fopen($filePath, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $text .= implode(" ", $data) . "\n";
        }
        fclose($handle);
    }
    return $text;
}

function extractTextFromPDF($filePath)
{
    if (!file_exists('vendor/autoload.php')) {
        return "Error: PDF library is missing. Run 'composer require smalot/pdfparser' and retry.";
    }

    $parser = new \Smalot\PdfParser\Parser();
    $pdf = $parser->parseFile($filePath);
    return $pdf->getText();
}
?>
