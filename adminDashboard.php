<?php
// Start session and check for admin privileges
session_start();
// if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
//     header('Location: login.php');
//     exit;
// }

// Include the database connection
include 'dbConnect.php';

// Fetch client data from the clients table
$sql = "SELECT * FROM clients";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Client Management Dashboard</h2>
    
    <table class="table table-striped table-bordered mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Subdomain</th>
                <th>Status</th>
                <th>Restrictions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($client = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $client['id']; ?></td>
                        <td><?php echo htmlspecialchars($client['name']); ?></td>
                        <td><?php echo htmlspecialchars($client['username']); ?></td>
                        <td><?php echo htmlspecialchars($client['subdomain']); ?></td>
                        <td><?php echo htmlspecialchars($client['status']); ?></td>
                        <td><?php echo htmlspecialchars($client['restrictions']); ?></td>
                        <td>
                            <a href="edit_client.php?id=<?php echo $client['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete_client.php?id=<?php echo $client['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No clients found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
