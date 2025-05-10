<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/registerAdmin.css"> 
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="col-md-6 offset-md-3">
        <h3 class="mb-4">Register New User</h3>

        <?php
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success">'.htmlspecialchars($_GET['success']).'</div>';
        }
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger">'.htmlspecialchars($_GET['error']).'</div>';
        }
        ?>

        <form method="POST" action="../PHP/registerUserFunction.php">
         
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Register User</button>
        </form>
    </div>
</div>
</body>
</html>