<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Medicine</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
</head>

<body>
    <?php include 'header.php'; ?>
    
    <div class="container mt-5">
        <h2>Add New Medicine</h2>

        <!-- Form to add new medicine -->
        <form action="addMedicine.php" method="POST">
            <div class="mb-3">
                <label for="medicine_name" class="form-label">Medicine Name</label>
                <input type="text" class="form-control" name="medicine_name" id="medicine_name" placeholder="Enter the medicine name" required>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Add Medicine</button>
        </form>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
