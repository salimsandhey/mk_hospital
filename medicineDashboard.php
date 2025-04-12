<?php
include 'auth.php';
include "dbConnect.php";

// Handle success/error messages from other operations
$message = '';
$messageType = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'success';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Get categories from medicine names (extract first word)
$categoryQuery = "SELECT DISTINCT 
                    CASE 
                        WHEN name LIKE 'Tab %' THEN 'Tablet'
                        WHEN name LIKE 'Cap %' THEN 'Capsule'
                        WHEN name LIKE 'Gel %' THEN 'Gel'
                        WHEN name LIKE 'Oil %' THEN 'Oil'
                        WHEN name LIKE 'Nano %' THEN 'Nano'
                        ELSE 'Other' 
                    END as category 
                  FROM medicines
                  ORDER BY category";
$categoryResult = mysqli_query($conn, $categoryQuery);
$categories = [];
while ($row = mysqli_fetch_assoc($categoryResult)) {
    $categories[] = $row['category'];
}

// Fetch all medicines initially
$query = "SELECT 
            id, 
            name,
            CASE 
                WHEN name LIKE 'Tab %' THEN 'Tablet'
                WHEN name LIKE 'Cap %' THEN 'Capsule'
                WHEN name LIKE 'Gel %' THEN 'Gel'
                WHEN name LIKE 'Oil %' THEN 'Oil'
                WHEN name LIKE 'Nano %' THEN 'Nano'
                ELSE 'Other' 
            END as category
          FROM medicines
          ORDER BY category, name";
$result = mysqli_query($conn, $query);

// Count total medicines
$countQuery = "SELECT COUNT(*) as total FROM medicines";
$countResult = mysqli_query($conn, $countQuery);
$totalMedicines = mysqli_fetch_assoc($countResult)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2E37A4;
            --secondary-color: #F0F4FF;
            --accent-color: #4f57d1;
            --text-color: #2B2D42;
            --light-text: #A0A3B1;
            --danger-color: #FF5E57;
            --success-color: #00C896;
            --border-radius: 10px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .dashboard-header {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--box-shadow);
        }

        .search-container {
            position: relative;
            max-width: 400px;
        }

        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
        }

        .search-input {
            padding-left: 40px;
            border-radius: 30px;
            border: 1px solid #e2e8f0;
            background-color: var(--secondary-color);
        }

        .category-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 1.5rem;
        }

        .category-badge {
            padding: 8px 16px;
            border-radius: 30px;
            background-color: #f8f9fa;
            color: var(--text-color);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
        }

        .category-badge:hover, .category-badge.active {
            background-color: var(--primary-color);
            color: white;
        }

        .category-badge .count {
            background-color: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 30px;
            margin-left: 8px;
            font-size: 0.8rem;
        }

        .medicine-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .medicine-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: all 0.3s;
            overflow: hidden;
            position: relative;
        }

        .medicine-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .medicine-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            position: relative;
        }

        .medicine-actions {
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
        }

        .medicine-id {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--secondary-color);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        .medicine-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            padding-right: 50px;
        }

        .medicine-category {
            display: inline-block;
            padding: 5px 10px;
            background-color: var(--secondary-color);
            border-radius: 20px;
            font-size: 0.8rem;
            color: var(--primary-color);
        }

        .btn-action {
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
            border-radius: var(--border-radius);
            transition: all 0.2s;
        }

        .btn-edit {
            color: var(--primary-color);
            background-color: var(--secondary-color);
            border: none;
        }

        .btn-edit:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-delete {
            color: var(--danger-color);
            background-color: rgba(255, 94, 87, 0.1);
            border: none;
        }

        .btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
        }

        .statistics-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .statistic-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            text-align: center;
        }

        .statistic-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .statistic-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .statistic-label {
            color: var(--light-text);
            font-size: 0.9rem;
        }

        .category-icon {
            margin-right: 5px;
        }

        .medicine-letter {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            grid-column: 1 / -1;
        }

        .modal-header, .modal-footer {
            border: none;
        }

        .modal-body {
            padding: 1.5rem;
        }

        #addForm .form-control {
            padding: 0.8rem 1rem;
            border-radius: var(--border-radius);
        }

        @media (max-width: 576px) {
            .statistics-container {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header {
                flex-direction: column !important;
                align-items: flex-start !important;
            }
            
            .search-container {
                width: 100%;
                margin-bottom: 1rem;
            }
            
            .add-button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container-fix mt-4">
        <!-- Alert message for success/error feedback -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Dashboard Header -->
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Medicine Dashboard</h2>
                <p class="text-muted mb-0">Manage your medicine inventory</p>
            </div>
            <div class="d-flex align-items-center">
                <div class="search-container me-3">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search" class="form-control search-input" placeholder="Search medicines...">
                </div>
                <button class="btn custom-btn add-button" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                    <i class="fas fa-plus me-2"></i>Add Medicine
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="statistics-container">
            <div class="statistic-card">
                <div class="statistic-icon">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="statistic-value"><?php echo $totalMedicines; ?></div>
                <div class="statistic-label">Total Medicines</div>
            </div>
            <?php
            // Get counts for each category
            foreach ($categories as $category) {
                $categoryIcon = '';
                $bgColor = '';
                
                switch ($category) {
                    case 'Tablet':
                        $categoryIcon = 'pills';
                        break;
                    case 'Capsule':
                        $categoryIcon = 'capsules';
                        break;
                    case 'Gel':
                        $categoryIcon = 'prescription-bottle';
                        break;
                    case 'Oil':
                        $categoryIcon = 'vial';
                        break;
                    case 'Nano':
                        $categoryIcon = 'syringe';
                        break;
                    default:
                        $categoryIcon = 'prescription-bottle-medical';
                }
                
                // Get count for this category
                $countCatQuery = "SELECT COUNT(*) as count FROM medicines WHERE 
                                  CASE 
                                    WHEN name LIKE 'Tab %' THEN 'Tablet'
                                    WHEN name LIKE 'Cap %' THEN 'Capsule'
                                    WHEN name LIKE 'Gel %' THEN 'Gel'
                                    WHEN name LIKE 'Oil %' THEN 'Oil'
                                    WHEN name LIKE 'Nano %' THEN 'Nano'
                                    ELSE 'Other' 
                                  END = '$category'";
                $countCatResult = mysqli_query($conn, $countCatQuery);
                $catCount = mysqli_fetch_assoc($countCatResult)['count'];
                
                echo '<div class="statistic-card">
                        <div class="statistic-icon">
                            <i class="fas fa-'.$categoryIcon.'"></i>
                        </div>
                        <div class="statistic-value">'.$catCount.'</div>
                        <div class="statistic-label">'.$category.'</div>
                    </div>';
            }
            ?>
        </div>

        <!-- Category filters -->
        <div class="category-filters">
            <div class="category-badge active" data-category="all">
                <i class="fas fa-layer-group category-icon"></i>
                All
                <span class="count"><?php echo $totalMedicines; ?></span>
            </div>
            
            <?php foreach ($categories as $category): 
                $categoryIcon = '';
                
                switch ($category) {
                    case 'Tablet':
                        $categoryIcon = 'pills';
                        break;
                    case 'Capsule':
                        $categoryIcon = 'capsules';
                        break;
                    case 'Gel':
                        $categoryIcon = 'prescription-bottle';
                        break;
                    case 'Oil':
                        $categoryIcon = 'vial';
                        break;
                    case 'Nano':
                        $categoryIcon = 'syringe';
                        break;
                    default:
                        $categoryIcon = 'prescription-bottle-medical';
                }
                
                // Get count for this category
                $countCatQuery = "SELECT COUNT(*) as count FROM medicines WHERE 
                                  CASE 
                                    WHEN name LIKE 'Tab %' THEN 'Tablet'
                                    WHEN name LIKE 'Cap %' THEN 'Capsule'
                                    WHEN name LIKE 'Gel %' THEN 'Gel'
                                    WHEN name LIKE 'Oil %' THEN 'Oil'
                                    WHEN name LIKE 'Nano %' THEN 'Nano'
                                    ELSE 'Other' 
                                  END = '$category'";
                $countCatResult = mysqli_query($conn, $countCatQuery);
                $catCount = mysqli_fetch_assoc($countCatResult)['count'];
            ?>
                <div class="category-badge" data-category="<?php echo $category; ?>">
                    <i class="fas fa-<?php echo $categoryIcon; ?> category-icon"></i>
                    <?php echo $category; ?>
                    <span class="count"><?php echo $catCount; ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Medicines Container -->
        <div id="medicinesContainer" class="medicine-container">
            <?php 
            if (mysqli_num_rows($result) > 0) {
                $currentCategory = '';
                while ($row = mysqli_fetch_assoc($result)) {
                    $firstLetter = strtoupper(substr(ltrim($row['name'], 'Tab Cap Gel Oil Nano '), 0, 1));
                    
                    echo '<div class="medicine-card" data-category="' . htmlspecialchars($row['category']) . '">
                            <div class="medicine-header">
                                <div class="medicine-id">#' . htmlspecialchars($row['id']) . '</div>
                                <div class="medicine-letter">' . $firstLetter . '</div>
                                <div class="medicine-name">' . htmlspecialchars($row['name']) . '</div>
                                <div class="medicine-category">
                                    <i class="fas fa-tag me-1"></i>' . htmlspecialchars($row['category']) . '
                                </div>
                            </div>
                            <div class="medicine-actions">
                                <button class="btn btn-edit btn-action" onclick="editMedicine(' . $row['id'] . ', \'' . htmlspecialchars($row['name']) . '\')">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                                <button class="btn btn-delete btn-action" onclick="confirmDelete(' . $row['id'] . ', \'' . htmlspecialchars($row['name']) . '\')">
                                    <i class="fas fa-trash-alt me-1"></i>Delete
                                </button>
                            </div>
                        </div>';
                }
            } else {
                echo '<div class="no-results">
                        <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                        <h4>No medicines found</h4>
                        <p class="text-muted">Try adding some medicines or refining your search.</p>
                      </div>';
            }
            ?>
        </div>
    </div>

    <!-- Add Medicine Modal -->
    <div class="modal fade" id="addMedicineModal" tabindex="-1" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMedicineModalLabel">Add New Medicine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="addMedicine.php" method="POST">
                        <div class="mb-3">
                            <label for="medicine_name" class="form-label">Medicine Name</label>
                            <input type="text" class="form-control" name="medicine_name" id="medicine_name" placeholder="Enter the medicine name" required>
                            <div class="form-text">Include prefix like 'Tab', 'Cap', etc. if applicable</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addForm" class="btn custom-btn">Add Medicine</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Medicine Modal -->
    <div class="modal fade" id="editMedicineModal" tabindex="-1" aria-labelledby="editMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMedicineModalLabel">Edit Medicine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="editMedicine.php" method="POST">
                        <input type="hidden" id="edit_medicine_id" name="medicine_id">
                        <div class="mb-3">
                            <label for="edit_medicine_name" class="form-label">Medicine Name</label>
                            <input type="text" class="form-control" name="medicine_name" id="edit_medicine_name" placeholder="Enter the medicine name" required>
                            <div class="form-text">Include prefix like 'Tab', 'Cap', etc. if applicable</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="editForm" class="btn custom-btn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Initialize variables
        let medicines = <?php echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC)); ?>;
        let activeCategory = 'all';
        let searchTerm = '';
        
        // Search functionality with debounce
        let debounceTimer;
        $('#search').on('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchTerm = $(this).val().toLowerCase();
                filterMedicines();
            }, 300);
        });
        
        // Category filter
        $('.category-badge').on('click', function() {
            $('.category-badge').removeClass('active');
            $(this).addClass('active');
            activeCategory = $(this).data('category');
            filterMedicines();
        });
        
        // Filter medicines based on search term and category
        function filterMedicines() {
            const cards = document.querySelectorAll('.medicine-card');
            let hasVisibleCards = false;
            
            cards.forEach(card => {
                const medicineName = card.querySelector('.medicine-name').textContent.toLowerCase();
                const medicineCategory = card.dataset.category;
                
                // Check if it matches both search term and category filter
                const matchesSearch = medicineName.includes(searchTerm);
                const matchesCategory = activeCategory === 'all' || medicineCategory === activeCategory;
                
                if (matchesSearch && matchesCategory) {
                    card.style.display = 'block';
                    hasVisibleCards = true;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show no results message if needed
            const noResultsElem = document.querySelector('.no-results');
            if (noResultsElem) {
                if (!hasVisibleCards) {
                    // Create a no results message if it doesn't exist
                    if (document.querySelectorAll('.no-results').length === 0) {
                        const noResults = document.createElement('div');
                        noResults.className = 'no-results';
                        noResults.innerHTML = `
                            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                            <h4>No medicines found</h4>
                            <p class="text-muted">Try adjusting your search or category filter.</p>
                        `;
                        document.getElementById('medicinesContainer').appendChild(noResults);
                    } else {
                        document.querySelector('.no-results').style.display = 'block';
                    }
                } else {
                    document.querySelectorAll('.no-results').forEach(elem => {
                        elem.style.display = 'none';
                    });
                }
            }
        }
        
        // Edit medicine
        function editMedicine(id, name) {
            document.getElementById('edit_medicine_id').value = id;
            document.getElementById('edit_medicine_name').value = name;
            
            const editModal = new bootstrap.Modal(document.getElementById('editMedicineModal'));
            editModal.show();
        }
        
        // Delete confirmation
        function confirmDelete(id, name) {
            if (confirm(`Are you sure you want to delete "${name}"?`)) {
                window.location.href = 'deleteMedicine.php?id=' + id;
            }
        }
    </script>
</body>
</html>
