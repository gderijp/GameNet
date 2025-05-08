<?php

session_start();
require 'db.inc.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(
        [
            'email' => $email
        ]
    );

    $result = $stmt->fetch();
    try {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Voer een correct emailadres in");
        }
        if ($email == $result->email) {
            throw new Exception("Emailadres bestaat al");
        }
        if (strlen($password) < 6) {
            throw new Exception("Je wachtwoord moet minstens 6 tekens bevatten!");
        }

        $registerQry = "INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)";
        $stmt = $conn->prepare($registerQry);
        $result = $stmt->execute(
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $hashed
            ]
        );
        header('Location: login.php');
        exit();
    } catch (PDOException $err) {
        $error = "SQL Error: " . $err->getMessage();
    } catch (Exception $ex) {
        $error = $ex->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
</head>

<body>
    <nav class="navContainer">
        <a href="index.php"><img src="logo.png" alt="GameNet Logo" class="logoImg"></a>
        <ul class="navRight">
            <li class="navList"><a href="index.php"><i class="fa-solid fa-house" style='font-size:26px'></i></a></li>
            <li class="navList"><a href="productPage.php"><i class="fa-solid fa-shop" style='font-size:26px'></i></a></li>
            <li class="navList"><a href="favorites.php"><i class="fa-solid fa-heart" style='font-size:26px'></i></a></li>
            <li class="navList"><a href="cart.php"><i class="fa-solid fa-cart-shopping" style='font-size:26px'></i></a></li>
        </ul>
    </nav>

    <div class="formContainer">
        <h1>Registreren</h1>
        <form method="POST">
            <div class="formLabel">
                <label for="firstName">Voornaam</label>
                <input type="text" name="firstName" id="firstName" value="<?php echo (isset($_POST['firstName'])) ? $_POST['firstName'] : '' ?>">
            </div>

            <div class="formLabel">
                <label for="lastName">Achternaam</label>
                <input type="text" name="lastName" id="lastName" value="<?php echo (isset($_POST['lastName'])) ? $_POST['lastName'] : '' ?>">
            </div>

            <div class="formLabel">
                <label for="email">Emailadres</label>
                <input type="text" name="email" id="email" value="<?php echo (isset($_POST['email'])) ? $_POST['email'] : '' ?>">
            </div>

            <div class="formLabel">
                <label for="password">Wachtwoord</label>
                <input type="password" name="password" id="password" value="<?php echo (isset($_POST['password'])) ? $_POST['password'] : '' ?>" minlength="6">
            </div>

            <div>
                <input type="submit" class="saveBtn" value="Registreer">
            </div>
        </form>
        <?php echo (isset($error)) ? "<p class='error'>$error</p>" : '' ?>
        <p>Of <a href="login.php">log in</a></p>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="about.php">About</a></p>
    </footer>
</body>

</html>