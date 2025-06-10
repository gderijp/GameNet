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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $deleteQry = "DELETE FROM orders WHERE userId = :userId;
        DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($deleteQry);
        $stmt->execute(
            [
                'id' => $_SESSION['user_id'],
                'userId' => $_SESSION['user_id']
            ]
        );

        session_unset();
        session_destroy();
        header('Location: ../controllers/login.php');
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
    <title>Verwijder account</title>
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
        <h1 class="centerText">Weet u zeker dat u uw account wilt verwijderen?</h1>
        <?php echo (isset($error)) ? $error : '' ?>
        <form method="POST" class="centerText">
            <input type="submit" name="permDelete" value="Ja, zeker!" class="saveBtn">
            <a href="profile.php" class="hover">Nee, nog niet...</a>
        </form>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="../about.php">About</a></p>
    </footer>
</body>

</html>