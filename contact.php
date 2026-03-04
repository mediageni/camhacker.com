<?php
require 'db.php';
require 'includes/pagination.php'; // Include pagination logic
require 'queries.php'; // Include the query logic
require 'functions.php'; // Include the functions file
?>
<?php include 'includes/head.php'; 
$dynamicTitle = 'Contact CamHacker'; // Page-specific title
?>
<title><?php echo htmlspecialchars($dynamicTitle); ?></title>
</head>
<body>

<main>
<?php include 'includes/nav.php'; ?>
    <div class="container mt-4">
        <h1 class="mb-4">Contact CamHacker</h1>

        <p>If you have any questions, concerns, or would like to report a camera, feel free to contact us. We will get back to you as soon as possible.</p>

        <p>You can write to us directly at: <strong>solidbunker@protonmail.com</strong>.</p>

        <p class="mt-4">We appreciate your feedback and inquiries. Thank you for visiting CamHacker!</p>

    </div>
</main>

<?php include 'includes/modal.php'; ?>
<?php include 'includes/footer.php'; ?>
</body>
</html>
