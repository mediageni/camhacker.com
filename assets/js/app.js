$(document).ready(function() {
    // Function to load all cities
    function loadAllCities() {
        $.ajax({
            url: '/search.php',  // Use absolute path from root
            type: 'GET',
            data: { ajax: 'get_all_cities' },  // Request all cities
            success: function(response) {
                try {
                    var cities = JSON.parse(response);  // Parse the JSON response

                    // Clear the city dropdown and populate with all cities
                    $('#city').empty().append('<option value="">City</option>');
                    $.each(cities, function(index, city) {
                        $('#city').append('<option value="' + city + '">' + city + '</option>');
                    });
                } catch (e) {
                    console.error("Invalid JSON response:", response);
                }
            },
            error: function() {
                alert('Error fetching all cities. Please try again.');
            }
        });
    }

    // Load all cities when the page loads
    if ($('#country').val() === "") {
        loadAllCities();
    }

    // When the country dropdown changes
    $('#country').change(function() {
        var country = $(this).val(); // Get the selected country value

        // If a country is selected, make the AJAX request
        if (country) {
            $.ajax({
                url: '/search.php',  // Use absolute path from root
                type: 'GET',
                data: { ajax: 'get_cities', country: country },  // Send the selected country
                success: function(response) {
                    try {
                        var cities = JSON.parse(response);  // Parse the JSON response

                        // Clear the city dropdown
                        $('#city').empty().append('<option value="">City</option>');

                        // Populate the city dropdown with the fetched cities
                        $.each(cities, function(index, city) {
                            $('#city').append('<option value="' + city + '">' + city + '</option>');
                        });
                    } catch (e) {
                        console.error("Invalid JSON response:", response);
                    }
                },
                error: function() {
                    alert('Error fetching cities. Please try again.');
                }
            });
        } else {
            // If no country is selected, reload all cities
            loadAllCities();
        }
    });

    // ==============================
    // Add Keyboard Shortcut Binding
    // ==============================

    // Bind 'mod+k' to show the modal
    Mousetrap.bind('mod+k', function(event) {
        event.preventDefault(); // Prevent default browser behavior
        showModal();
    });

    // Function to show the modal
    function showModal() {
        // Assuming you're using Bootstrap 5
        var myModal = new bootstrap.Modal(document.getElementById('myModal'), {
            keyboard: true
        });
        myModal.show();
    }
});
