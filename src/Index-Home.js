$(document).ready(function() {
    $.ajax({
        url: 'db.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log("success");
            console.log(response); // This will contain the latest post contents
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });


    $('#Logout-button').click(function() {
        event.preventDefault(); // Prevent the default form submission
        console.log("yeah")

        $.ajax({
            url: 'logout.php', // Assuming the PHP script with logout logic is in logout.php
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(), // Serialize the form data
            success: function(response) {
                console.log("Logout success");
                // Redirect or perform any other action after successful logout
            },
            error: function(xhr, status, error) {
                console.error("Logout error: " + xhr.responseText);
                window.location.reload("login.php")
            }
        });


    });
});
