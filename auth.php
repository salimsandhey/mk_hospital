<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Get the current subdomain
$currentSubdomain = explode('.', $_SERVER['HTTP_HOST'])[0];

// Check if the current subdomain matches the one in the session
// if ($currentSubdomain !== $_SESSION['subdomain']) {
//     // Subdomain doesn't match, log the user out and redirect to login page
//     session_unset();
//     session_destroy();
//     header('Location: login.php');
//     exit;
// }

// Optionally, check username if specific access is needed
if ($_SESSION['username'] === 'yassensandhey') {
    // Grant specific access based on username
    // Your code for specific access here
}

// Proceed with the page content
