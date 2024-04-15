$(document).ready(function() {
    $("#submit").click(function() {
        const username = $("#username").val();
        const password = $("#password").val();
        login(username, password); // Pass the username and password to the login function
    });

    function login(username, password) {
        $.ajax({
            url: 'db.php',
            type: 'POST', // Change method to POST since you're sending sensitive data
            dataType: 'json',
            data: {
                username: username,
                password: password,
            },
            success: function(response) {
                console.log("success");
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    $('#like-button').click(function() {
        // Get the current like status
        var currentStatus = $(this).text().trim();

        // Determine the new like status
        var newStatus = (currentStatus === 'Like') ? 'Liked' : 'Like';

        // Send AJAX request to update like status
        $.ajax({
            type: 'POST',
            url: 'db.php',
            data: { likeStatus: newStatus },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update button text and display message
                    $('#like-button').text(newStatus);
                    $('#message').text('Like status updated successfully.');
                } else {
                    // Display error message
                    $('#message').text(response.message);
                }
            },
            error: function(xhr, status, error) {
                // Display error message
                $('#message').text('Error: ' + error);
            }
        });
    });

});
