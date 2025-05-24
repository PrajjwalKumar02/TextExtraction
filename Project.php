<?php include 'process.php'; ?>
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
