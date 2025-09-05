<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smarty API Address Lookup</title>
</head>
<body>

<!-- Address input field -->
<input type="text" id="addressInput" placeholder="Enter an address" />

<!-- Hidden input field to store the JSON response -->
<input type="hidden" id="addressJson" />

<script>
    // Function to call the Smarty API and process the response
    function callSmartyAPI(address) {
        const apiKey = "579d0101-328c-a556-765a-64542a9b5fa8";  // Replace with your API key
        const authToken = "SQWG4dRJcgmyu3VK5ZpC";               // Replace with your Auth token
        const url = `https://us-street.api.smarty.com/street-address?auth-id=${apiKey}&auth-token=${authToken}&street=${encodeURIComponent(address)}&license=us-core-cloud`;

        // Make an AJAX request to the Smarty API
        fetch(url)
            .then(response => {
                // Check for a successful response
                if (!response.ok) {
                    // If not successful, try to parse the error message
                    return response.json().then(err => {
                        throw new Error(`API Error: ${err.message || 'Unknown error'}`);
                    });
                }
                // Otherwise, return the response JSON
                return response.json();
            })
            .then(data => {
                console.log("API Response:", data);  // Log the full API response for debugging
                
                if (data.length > 0) {
                    // Assuming the first result is the most relevant
                    const addressData = data[0];
                    const formattedAddress = `${addressData.delivery_line_1}, ${addressData.locality}, ${addressData.admin_area_1} ${addressData.zip_code}`;

                    // Prepare the JSON data to be saved
                    const jsonResponse = {
                        place_id: addressData.input_id,
                        address_components: {
                            street: addressData.delivery_line_1,
                            city: addressData.locality,
                            state: addressData.admin_area_1,
                            postal_code: addressData.zip_code
                        },
                        geometry: {
                            location: {
                                lat: addressData.latitude,
                                lng: addressData.longitude
                            }
                        }
                    };

                    // Set the hidden field with JSON data
                    document.getElementById('addressJson').value = JSON.stringify(jsonResponse);

                    // Optional: Update a visible field with the formatted address
                    document.getElementById('addressInput').value = formattedAddress;
                } else {
                    console.error('No address found');
                }
            })
            .catch(error => {
                // Catch and log any errors
                console.error('Error fetching address data from Smarty:', error);
                alert(`Error: ${error.message}`);  // Display the error in a popup for better visibility
            });
    }

    // Function to handle address input changes
    function handleAddressInput() {
        const addressInput = document.getElementById('addressInput');
        const address = addressInput.value;

        // Call Smarty API when user finishes typing (or after a delay)
        if (address.length > 5) {  // Only call API if the address is long enough
            callSmartyAPI(address);
        }
    }

    // Event listener to trigger address lookup when user types
    window.addEventListener('DOMContentLoaded', function () {
        const addressInput = document.getElementById('addressInput');
        
        if (addressInput) {
            addressInput.addEventListener('input', handleAddressInput);
        } else {
            console.error("Address input element not found!");
        }
    });
</script>

</body>
</html>
