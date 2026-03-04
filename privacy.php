<?php
require 'db.php';
require 'includes/pagination.php'; // Include pagination logic
require 'queries.php'; // Include the query logic
require 'functions.php'; // Include the functions file
?>
<?php include 'includes/head.php'; ?>
</head>
<body>

<main>
<?php include 'includes/nav.php'; ?>
    <div class="container mt-4">
        <h1 class="mb-4">Privacy Policy</h1>
        
        <p>This Privacy Policy outlines the types of personal information that is received and collected by our website and how it is used.</p>
        
        <h3>Information We Collect</h3>
        <p>We do not collect or store personal information about our users directly. However, we use Google Analytics and Google AdSense, which may collect certain information about your device, browsing habits, and interactions with our website.</p>

        <h3>Google Analytics</h3>
        <p>We use Google Analytics to collect information about how visitors interact with our website. This helps us understand website traffic, user behavior, and improve user experience. The information collected may include:</p>
        <ul>
            <li>IP address (anonymized)</li>
            <li>Browser type and version</li>
            <li>Pages visited and time spent on each page</li>
            <li>Operating system and device information</li>
        </ul>
        <p>This data is collected anonymously and is used for analytical purposes only. No personally identifiable information is stored or shared.</p>

        <h3>Google AdSense</h3>
        <p>We use Google AdSense to display advertisements on our website. Google AdSense may use cookies to serve ads based on your previous visits to our website or other websites. Google's use of advertising cookies enables it and its partners to serve ads to users based on their visit to our site and/or other sites on the internet.</p>
        <p>Users may opt out of personalized advertising by visiting the <a href="https://www.google.com/settings/ads">Google Ads Settings</a>.</p>

        <h3>Cookies</h3>
        <p>Cookies are small files stored on your device to help analyze web traffic or keep track of preferences. Google AdSense uses cookies to serve ads on our site. These cookies do not contain personal information. You can choose to disable cookies through your browser settings.</p>

        <h3>Third-Party Links</h3>
        <p>Our website may contain links to other websites. We are not responsible for the privacy practices or content of these third-party sites. We recommend you review the privacy policies of any website you visit.</p>

        <h3>Changes to This Privacy Policy</h3>
        <p>We may update this Privacy Policy from time to time. Any changes will be reflected on this page, and your continued use of our website will signify your acceptance of the updated terms.</p>

        <h3>Contact Us</h3>
        <p>If you have any questions about this Privacy Policy, feel free to <a href="/contact">contact us</a>.</p>
    </div>
</main>

<?php include 'includes/modal.php'; ?>
<?php include 'includes/footer.php'; ?>
</body>
</html>
