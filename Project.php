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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extract Text from Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <div class="text text-center">
        <h1>Welcome To Text Extraction Page</h1>
    </div>
    <div class="container d-flex flex-column align-items-center vh-100">
        <div class="card shadow p-4 w-50">
            <h2 class="text-center mb-4">Upload a File</h2>
            <form id="uploadForm" action="project.php" method="post" enctype="multipart/form-data">
                <input class="form-control mb-3" type="file" name="file" required>
                <button type="submit" class="btn btn-primary w-100">Extract Text</button>
            </form>
        </div>
        <div class="card shadow p-4 w-50 mt-3">
            <h5 class="text-center">Extracted Text</h5>
            <div class="result-box">
                <pre><?php echo !empty($extractedText) ? htmlspecialchars($extractedText) : "No text extracted yet."; ?></pre>
            </div>

        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
