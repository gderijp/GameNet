<?php

session_start();
require_once '../config/db.inc.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $fetchEmailQuery = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($fetchEmailQuery);
        $stmt->execute(
            [
                'email' => $email
            ]
        );
        $users = $stmt->fetch();

        $foundPass = $users->password;

        if (($users->email !== $email) || ($users->password !== $password && !password_verify($password, $foundPass))) {
            throw new Exception("<p class='error'>Incorrecte email/wachtwoord ingevoerd</p>");
        } else {
            $_SESSION['loggedInUser'] = $email;
            $_SESSION['username'] = $users->first_name;
            $_SESSION['user_id'] = $users->id;

            if ($_SESSION['triedToOrder'] == true) {
                header('Location: cart.php');
                exit();
            }
            header('Location: ../index.php');
            exit();
        }
    } catch (PDOException $err) {
        echo "SQL Error: " . $err->getMessage();
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
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../images/noBgImg.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
</head>

<body>
    <nav class="navContainer">
        <a href="../index.php"><img src="../images/logo.png" alt="GameNet Logo" class="logoImg"></a>
        <ul class="navRight">
            <li class="navList"><a href="../index.php"><i class="fa-solid fa-house" style='font-size:26px'></i></a></li>
            <li class="navList"><a href="../productPage.php"><i class="fa-solid fa-shop" style='font-size:26px'></i></a></li>
            <li class="navList"><a href="../user/favorites.php"><i class="fa-solid fa-heart" style='font-size:26px'></i></a></li>
            <li class="navList"><a href="cart.php"><i class="fa-solid fa-cart-shopping" style='font-size:26px'></i></a></li>
        </ul>
    </nav>

    <div class="formContainer">
        <h1>Log in</h1>
        <form method="POST">
            <div class="formLabel">
                <label for="email">Emailadres</label>
                <input type="text" name="email" id="email" value="<?php echo (isset($_POST['email'])) ? $_POST['email'] : '' ?>">
            </div>

            <div class="formLabel">
                <label for="password">Wachtwoord</label>
                <input type="password" name="password" id="password" value="<?php echo (isset($_POST['password'])) ? $_POST['password'] : '' ?>" minlength="4">
            </div>

            <div>
                <input type="submit" class="saveBtn" value="Log in">
            </div>
        </form>
        <?php echo (isset($error)) ? $error : '' ?>
        <p>Of registreer <a href="register.php">hier</a></p>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="../about.php">About</a></p>
    </footer>
</body>

</html>