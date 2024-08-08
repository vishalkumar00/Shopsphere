<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose A User</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .link-box {
            margin: 10px;
            padding: 20px;
            border: 2px solid #333;
            border-radius: 10px;
            text-align: center;
        }
        .link-box a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="link-box">
        <a href="admin/index.php">Admin Portal</a>
    </div>
    <div class="link-box">
        <a href="seller/index.php">Seller Portal</a>
    </div>
    <div class="link-box">
        <a href="user/index.php">User (Customer) Portal</a>
    </div>
</body>
</html>