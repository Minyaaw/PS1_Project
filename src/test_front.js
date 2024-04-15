document.getElementById("login-form").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent form submission
    
    // Get input values
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;
  
    // Simple validation
    if (username === "admin" && password === "password") {
      // Successful login
      window.location.href = "welcome.html"; // Redirect to welcome page
    } else {
      // Display error message
      document.getElementById("error-message").innerText = "Invalid username or password.";
    }
  });
  