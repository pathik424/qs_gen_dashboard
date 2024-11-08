<?php 

/* Template Name: Register */ 

?>


<form id="registrationForm" method="post" enctype="multipart/form-data" >
    <h2>Register</h2>
    
    <input type="text" name="first_name" placeholder="First name*" required>
    <input type="text" name="last_name" placeholder="Last name*" required>
    
    <input type="email" name="email" placeholder="Email address*" required>
    
    <label>
        <input type="checkbox" id="representInstitute" name="represent_institution">
        I represent an institution
    </label>
    
    <div id="institutionDetails" style="display: none;">
        <input type="text" name="institution_name" placeholder="Institution Name">
    </div>

    <input type="file" accept="image/*" name="profile_image">
    
    <input type="text" name="designation" placeholder="Designation">
    <input type="text" name="country" placeholder="Country/Region">
    
    <input type="text" name="username" placeholder="Username*" required>
    <input type="password" name="password" placeholder="Password*" required>
    
    <button type="submit" name="register">Register</button>
</form>

<script>
    document.getElementById('representInstitute').addEventListener('change', function() {
        document.getElementById('institutionDetails').style.display = this.checked ? 'block' : 'none';
    });
</script>


<style>
    /* Basic styling */
form {
    width: 50%; /* Adjust width as needed */
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

h2 {
    text-align: center;
    margin-bottom:   
 20px;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius:   
 5px;
}

label {
    display: block;
    margin-bottom: 5px;
}

button[type="submit"] {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;   

}

/* Specific styling for the institution details section */
#institutionDetails {
    display: none;
}

#representInstitute:checked ~ #institutionDetails {
    display: block;
}
</style>