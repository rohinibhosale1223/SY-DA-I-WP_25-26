/* =========================================================
   AURA FRAGRANCES - FULL CLIENT-SIDE SCRIPT (PHP/MySQL VERSION)
   
   Note: All heavy lifting (Cart storage, User login) is now 
   handled securely by PHP and the MySQL database. This script 
   now only handles User Interface (UI) enhancements.
   ========================================================= */

document.addEventListener("DOMContentLoaded", function() {

    // --- 1. REGISTRATION FORM: PASSWORD VALIDATION ---
    // Checks if passwords match before sending the data to the server
    const registerForm = document.querySelector('form[action="register.php"]');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPass = document.getElementById('confirm_pass').value;

            if (password !== confirmPass) {
                // Prevent the form from submitting to PHP if passwords don't match
                event.preventDefault(); 
                alert("Validation Error: Passwords do not match. Please try again.");
            }
        });
    }

    // --- 2. SHOPPING CART: REMOVE CONFIRMATION ---
    // Prevents accidental clicks by asking the user if they are sure
    const removeButtons = document.querySelectorAll('.btn-remove');
    
    removeButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            const isConfirmed = confirm("Are you sure you want to remove this fragrance from your cart?");
            
            if (!isConfirmed) {
                // Prevent the link from triggering the PHP DELETE script
                event.preventDefault(); 
            }
        });
    });

    // --- 3. CHECKOUT BUTTON BEHAVIOR ---
    // No client-side interception here so cart checkout links and order forms work normally.
});