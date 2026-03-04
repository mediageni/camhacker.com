<?php
require '../db.php';

// Fetch the ipwithport and image_url_full data from the database
$sql = "SELECT id, ipwithport, image_url_full FROM webcams";
$result = $conn->query($sql);

// Prepare the data as a JSON array to be sent to the frontend
$ips = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ips[] = [
            'id' => $row['id'],
            'ipwithport' => $row['ipwithport'],
            'image_url_full' => $row['image_url_full']
        ];
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Check</title>
    <!-- Add Bootstrap/Bootswatch CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@4.6.0/dist/flatly/bootstrap.min.css" rel="stylesheet">
    <script>
        let ips = <?php echo json_encode($ips); ?>;
        let total = ips.length;
        let stopCheck = false; // Flag to control stopping the process
        let selectedIds = []; // Array to track selected IDs for deletion
        let lastChecked = null; // To track last checked checkbox for Shift+Click

        // Function to update progress
        function updateProgress(current, total) {
            document.getElementById('progress').innerHTML = 'Checked ' + current + ' of ' + total + ' IPs';
        }

        // Function to check the status of each IP
        function checkIp(index) {
            if (stopCheck || index >= total) {
                document.getElementById('statusMessage').innerHTML = '<p>All IPs checked.</p>';
                return;
            }

            let ipInfo = ips[index];
            let ip = ipInfo.ipwithport;
            fetch('check_single_ip.php?ip=' + encodeURIComponent(ip)) // Updated path
                .then(response => response.json())
                .then(data => {
                    // Determine background color based on status
                    let statusText = data.status.includes('online') ? 'online' : 'offline';
                    let bgColor = statusText === 'online' ? 'background-color: lightgreen;' : 'background-color: lightcoral;';

                    // Create a new row with the ID, IP (link to open in new tab), Image URL (using wsrv.nl), status, and a checkbox
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="selectable">${ipInfo.id}</td>
                        <td class="selectable"><a href="http://${ip}" target="_blank">${ip}</a></td>
                        <td class="selectable">
                            ${ipInfo.image_url_full ? `<img src="//wsrv.nl/?url=${encodeURIComponent(ipInfo.image_url_full)}&w=100&h=auto" alt="Thumbnail" style="width: 100px; height: auto;">` : `<img src="placeholder.jpg" alt="No Thumbnail" style="width: 100px; height: auto;">`}
                        </td>
                        <td class="selectable" style="${bgColor}">${statusText}</td>
                        <td class="checkbox-cell"><input type="checkbox" class="delete-checkbox" value="${ipInfo.id}" /></td>
                    `;
                    document.getElementById('statusTableBody').appendChild(row);
                    updateProgress(index + 1, total);
                    checkIp(index + 1); // Move to the next IP
                });
        }

        // Function to start the check process
        function startCheck() {
            stopCheck = false;
            document.getElementById('statusTableBody').innerHTML = ''; // Clear previous statuses
            document.getElementById('status').style.display = 'table'; // Show the status table
            document.getElementById('statusMessage').innerHTML = '<p>Started Checking</p>'; // Display Started Checking message
            checkIp(0); // Start checking from the first IP
        }

        // Function to stop the check process
        function stopCheckFunction() {
            stopCheck = true;
            document.getElementById('statusMessage').innerHTML = '<p>Stopped Checking</p>'; // Display Stopped Checking message
        }

        // Function to collect selected IDs for deletion
        function collectSelectedIds() {
            const checkboxes = document.querySelectorAll('.delete-checkbox:checked');
            selectedIds = Array.from(checkboxes).map(checkbox => checkbox.value);
        }

        // Function to delete selected records via AJAX and track progress
        function deleteSelected() {
            collectSelectedIds();
            if (selectedIds.length === 0) {
                alert("Please select records to delete.");
                return;
            }

            let deletedCount = 0;
            document.getElementById('deleteProgress').innerHTML = `Deleted 0 of ${selectedIds.length} selected records`;

            selectedIds.forEach((id, index) => {
                fetch('delete_record.php?id=' + id, { method: 'POST' }) // Updated path
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`input[value="${id}"]`).closest('tr').remove(); // Remove row
                        }
                        deletedCount++;
                        document.getElementById('deleteProgress').innerHTML = `Deleted ${deletedCount} of ${selectedIds.length} selected records`;

                        // Final message when all records are deleted
                        if (deletedCount === selectedIds.length) {
                            document.getElementById('deleteProgress').innerHTML = `All selected records deleted.`;
                        }
                    });
            });
        }

        // Event listener to select checkbox on clicking any cell in the row
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('checkbox-cell') || e.target.closest('.checkbox-cell')) {
                const checkbox = e.target.querySelector('.delete-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                }
            }
        });

        // Function to handle Shift+Click for selecting multiple checkboxes
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('delete-checkbox')) {
                if (!lastChecked) {
                    lastChecked = e.target;
                    return;
                }

                if (e.shiftKey) {
                    const checkboxes = document.querySelectorAll('.delete-checkbox');
                    let start = Array.prototype.indexOf.call(checkboxes, lastChecked);
                    let end = Array.prototype.indexOf.call(checkboxes, e.target);

                    checkboxes.forEach((checkbox, index) => {
                        if ((index >= Math.min(start, end) && index <= Math.max(start, end))) {
                            checkbox.checked = lastChecked.checked;
                        }
                    });
                }

                lastChecked = e.target;
            }
        });
    </script>
</head>
<body>
    <div class="container mt-4">
        <h1>IP Check Progress</h1>
        
        <!-- Progress info -->
        <p id="progress">Checked 0 of <?php echo count($ips); ?> IPs</p>

        <!-- Start, Stop, and Delete Selected buttons -->
        <div class="mb-3">
            <button class="btn btn-primary" onclick="startCheck()">Start Check</button>
            <button class="btn btn-danger" onclick="stopCheckFunction()">Stop Check</button>
            <button class="btn btn-warning" onclick="deleteSelected()">Delete Selected Records</button>
        </div>

        <!-- Status message for start/stop check -->
        <p id="statusMessage"></p>

        <!-- Deletion progress -->
        <p id="deleteProgress"></p>

        <!-- Status table -->
        <table class="table table-bordered" id="status" style="display: none;">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>IP with Port</th>
                    <th>Thumbnail</th>
                    <th>Status</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody id="statusTableBody">
                <!-- Rows will be appended here dynamically -->
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>