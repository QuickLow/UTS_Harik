<!-- form input data user  -->


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form User Registration</title>
</head>
<body>
    <h2>User Registration</h2>
    <form action="create_account.php" method="POST">
        <!-- username input -->
        <label for="username">Username</label><br>
        <input type="email" id="username" name="username" placeholder="Your Mail" required><br><br>
        
        <label for="password">Password</label><br>
        <input type="password" name="password" required></input><br><br>
        
        <label for="confirmpassword">Password</label><br>
        <input type="password" name="confirmpassword" required></input><br><br>
        
        <label for="fullname">Fullname</label><br>
        <input type="text" id="fullname" name="fullname" placeholder="Your name" required><br><br>

        <!-- role input -->

        <label for="role">Role</label><br>
        <select id="role" name="role" required>
            <option value="">--select your role--</option>
            <option value="admin">Admin</option>
            <option value="operator">Operator</option>  
            <option value="visitor">Visitor</option>  
        </select><br><br>

      <button type="submit">Register</button> 
    </form>
</body>
</html>