<h2>Register</h2>

<form method="POST" action="../controllers/AuthController.php">
    <label>Username:</label><br>
    <input type="text" name="new_username" required><br>

    <label>Password:</label><br>
    <input type="password" name="new_password" required><br>

    <label>Role:</label><br>
    <select name="role" required>
        <option value="citizen">Citizen</option>
        <option value="employee">Employee</option>
        <option value="admin">Admin</option>
    </select><br><br>

    <button type="submit" name="register">Register</button>
</form>

<a href="login.php">Back to Login</a>