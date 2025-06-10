<?php

session_start();
require_once '../config/db.inc.php';
if (!isset($_SESSION['loggedInUser'])) {
    header('Location: ../controllers/login.php');
}

function adminPageRow($conn)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(
        [
            'id' => $_SESSION['user_id']
        ]
    );
    $result = $stmt->fetch();
    if ($result->is_admin === 1) {
        echo "<li class='navList'><a href='../admin/adminPage.php'><i class='fa-solid fa-user-tie' style='font-size:26px'></i></a></li>";
    }
}

function notNull($columnName, $label)
{
    if (is_null($columnName) || $columnName == '') {
        throw new Exception("Het veld $label mag niet leeg zijn!");
    }
}

$fetchQry = "SELECT * FROM users WHERE id = :id";
$stmt = $conn->prepare($fetchQry);
$stmt->execute(
    [
        'id' => $_SESSION['user_id']
    ]
);

while ($row = $stmt->fetch()) {
    $oldFirstName = $row->first_name;
    $oldLastName = $row->last_name;
    $oldEmail = $row->email;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];

        if (strlen($password) < 6) {
            throw new Exception("Je wachtwoord moet minstens 6 tekens bevatten!");
        } elseif ($password !== $confirm) {
            throw new Exception("Wachtwoorden komen niet overeen");
        }

        notNull($password, "'Wachtwoord'");

        $updateQry = "UPDATE users 
        SET password = :password 
        WHERE id = :id";
        $stmt = $conn->prepare($updateQry);
        $stmt->execute(
            [
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'id' => $_SESSION['user_id']
            ]
        );

        header('Location: profile.php');
        exit();
    } catch (PDOException $err) {
        $error = $err->getMessage();
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
    <title>Gegevens wijzigen</title>
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
            <li class="navList"><a href="favorites.php"><i class="fa-solid fa-heart" style='font-size:26px'></i></a></li>
            <li class="navList"><a href="../controllers/cart.php"><i class="fa-solid fa-cart-shopping" style='font-size:26px'></i></a></li>
            <?php if (isset($_SESSION['loggedInUser'])) {
                $navLink = 'profile.php';
            } else {
                $navLink = '../controllers/login.php';
            } ?>
            <li class="navList"><a href="<?php echo $navLink ?>"><i class='fa-solid fa-user' style='font-size:26px'></i></a></li>
            <?php if (isset($_SESSION['user_id'])) {
                adminPageRow($conn);
            } ?>
            <?php if (isset($_SESSION['loggedInUser'])) {
                ?><li class="navList"><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket" style='font-size:26px'></i></a></li>
            <?php } ?>
        </ul>
    </nav>

    <div class="container">
        <h1 class="centerText">Wijzig gegevens</h1>

        <?php echo (isset($error) ? "<p class='error centerText'>" . $error . "</p>" : '') ?>
        <p class="centerText"><a href="profile.php">« terug</a></p>

        <form method="post" class="formContainer">
            <fieldset>
                <label for="password" class="formLabel">Wachtwoord</label>
                <input type="password" name="password" id="password" placeholder="Nieuw wachtwoord..." value="<?php echo (isset($password)) ? $password : '' ?>" minlength="6">
                <label for="confirm" class="formLabel">Wachtwoord</label>
                <input type="password" name="confirm" id="confirm" placeholder="Herhaal wachtwoord..." value="<?php echo (isset($confirm)) ? $confirm : '' ?>" minlength="6">
                <input type="submit" class="saveBtn" value="Wijzig">
            </fieldset>
        </form>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="../about.php">About</a></p>
    </footer>
</body>

</html>