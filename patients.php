<?php
include 'auth.php';

// Initialize or reset pagination parameters in session when directly accessing this page
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    // This is a direct page load, not an AJAX request
    $_SESSION['patient_page'] = 1;
    $_SESSION['patient_search'] = '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Patients</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .search-bar {
            max-width: 300px;
        }

        .search-bar input {
            padding-left: 40px;
        }

        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        #loadMoreContainer {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .no-more-patients {
            display: none;
            text-align: center;
            margin: 20px 0;
            color: #6c757d;
        }

        @media (max-width: 576px) {
            .search-bar {
                width: 100%;
                /* Ensure the search bar is 100% width */
            }
        }
    </style>
</head>

<body>
    <?php
    include 'header.php';
    ?>
    <div class="container-fix patients-container">
        <div class="mb-3 patients-search justify-content-between">
            <h5 class="bold">Patient List</h5>
            <div class=" justify-content-between align-items-center mb-2">
                <div class="d-flex">
                    <div class="search-bar position-relative ">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchPatient" class="form-control search-input"
                            placeholder="Search by ID, Name, or Phone">
                    </div>
                    <a class="add-btn ms-2" href="newRecord.php">
                        <i class="fas fa-plus"></i>
                    </a>
                    <a class="add-btn ms-2" id="refresh-page" >
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </a>
                </div>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-hover all-patients-table">
                <thead>
                    <tr class="table-head">
                        <th scope="col">Id</th>
                        <th scope="col">Name</th>
                        <th scope="col">Mobile</th>
                        <th scope="col" class="hide">Age</th>
                        <th scope="col">Address</th>
                        <th scope="col" class="hide">Last Visit</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="patientTableBody">
                    <!-- Patient data will be loaded here via AJAX -->
                </tbody>
            </table>
            
            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading patients...</p>
            </div>
            
            <div class="no-more-patients" id="noMorePatientsMessage">
                <p>No more patients to load</p>
            </div>
            
            <div id="loadMoreContainer">
                <button id="loadMoreButton" class="btn custom-btn">
                    <i class="fas fa-sync-alt me-2"></i>Load More
                </button>
            </div>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
        // Variables to manage pagination and search
        let currentPage = 1;
        let isLoading = false;
        let hasMorePages = true;
        let searchQuery = '';
        let debounceTimer;
        
        // Function to load patients via AJAX
        function loadPatients(page = 1, search = '') {
            if (isLoading || (!hasMorePages && page > 1)) return;
            
            isLoading = true;
            
            // Show loading spinner if loading more pages
            if (page > 1) {
                document.getElementById('loadingSpinner').style.display = 'block';
                document.getElementById('loadMoreButton').disabled = true;
            }
            
            // Prepare form data for the request
            const formData = new FormData();
            formData.append('page', page);
            formData.append('search', search);
            
            // Make AJAX request
            fetch('fetchPatients.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('loadMoreButton').disabled = false;
                
                // Update the table with new data
                if (page === 1) {
                    document.getElementById('patientTableBody').innerHTML = data.html;
                } else {
                    document.getElementById('patientTableBody').innerHTML += data.html;
                }
                
                // Update pagination state
                currentPage = data.currentPage;
                hasMorePages = data.hasMore;
                
                // Show or hide "No more patients" message
                if (!hasMorePages) {
                    document.getElementById('noMorePatientsMessage').style.display = 'block';
                    document.getElementById('loadMoreContainer').style.display = 'none';
                } else {
                    document.getElementById('noMorePatientsMessage').style.display = 'none';
                    document.getElementById('loadMoreContainer').style.display = 'flex';
                }
                
                isLoading = false;
            })
            .catch(error => {
                console.error('Error loading patients:', error);
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('loadMoreButton').disabled = false;
                isLoading = false;
                alert('Error loading patients. Please try again.');
            });
        }
        
        // Function to handle search with debounce
        function searchPatients() {
            clearTimeout(debounceTimer);
            
            debounceTimer = setTimeout(() => {
                searchQuery = document.getElementById('searchPatient').value.trim();
                currentPage = 1;
                hasMorePages = true;
                loadPatients(currentPage, searchQuery);
            }, 500); // Debounce delay of 500ms
        }
        
        // Load patients when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadPatients(currentPage, searchQuery);
            
            // Event listeners
            document.getElementById('searchPatient').addEventListener('input', searchPatients);
            
            document.getElementById('loadMoreButton').addEventListener('click', function() {
                if (!isLoading && hasMorePages) {
                    loadPatients(currentPage + 1, searchQuery);
                }
            });
            
            document.querySelector("#refresh-page").addEventListener("click", function(e) {
                currentPage = 1;
                searchQuery = '';
                document.getElementById('searchPatient').value = '';
                hasMorePages = true;
                loadPatients(currentPage, searchQuery);
            });
        });
    </script>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>