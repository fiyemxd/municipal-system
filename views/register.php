<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-body">
          <h3 class="card-title mb-4">Register</h3>
          <form method="POST" action="../controllers/AuthController.php">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="new_username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Role</label>
              <select name="role" class="form-select" required>
                <option value="citizen">Citizen</option>
                <option value="employee">Employee</option>
              </select>
            </div>
            <button type="submit" name="register" class="btn btn-success w-100">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
