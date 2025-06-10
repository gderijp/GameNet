<?php

session_start();
require_once '../config/db.inc.php';
if (!isset($_SESSION['loggedInUser'])) {
    header('Location: login.php');
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

$orderPrice = '';
try {
    $orderQry = "SELECT * FROM orders WHERE userId = :userId ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($orderQry);
    $stmt->execute(
        [
            'userId' => $_SESSION['user_id']
        ]
    );

    while ($game = $stmt->fetch()) {
        $orderPrice = $game->total_price;

        $gamesList = rtrim($game->games_list, ',');
        $games = explode(',', $gamesList);
    }

    if (!isset($_SESSION['orderPrice'])) {
        throw new Exception("Geen bestelling gevonden...");
    }
} catch (PDOException $err) {
    $error = $err->getMessage();
} catch (Exception $ex) {
    $error = $ex->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Besteld!</title>
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
            <?php if (isset($_SESSION['loggedInUser'])) {
                $navLink = '../user/profile.php';
            } else {
                $navLink = 'login.php';
            } ?>
            <li class="navList"><a href="<?php echo $navLink ?>"><i class='fa-solid fa-user' style='font-size:26px'></i></a></li>
            <?php if (isset($_SESSION['user_id'])) {
                adminPageRow($conn);
            } ?>
            <?php if (isset($_SESSION['loggedInUser'])) {
                ?><li class="navList"><a href="logout.php"><i class="fa-solid fa-right-from-bracket" style='font-size:26px'></i></a></li>
            <?php } ?>
        </ul>
    </nav>

    <div class="container">
        <?php if (isset($error)) {
            echo "<h3 class='error centerText'>" . $error . "</h3>";
        } else { ?>
            <h1>Gefeliciteerd! Je product is besteld voor <?php echo $orderPrice ?> euro!!!!</h1>
            <?php if (!$_SESSION['loggedInUser']) { ?>
                <small>(niet ingelogd)</small>
            <?php } ?>

            <h3>Overzicht van je bestelde producten:</h3>

            <ol>
                <?php foreach ($games as $game) {
                    $stmt = $conn->prepare("SELECT price FROM games WHERE title = :title");
                    $stmt->execute(
                        [
                            'title' => $game
                        ]
                    );
                    while ($gamePrice = $stmt->fetch()) { ?>
                        <li>
                            <p><strong>Titel: </strong><?php echo $game . "<br><strong>Prijs: </strong>€$gamePrice->price"; ?></p>
                        </li>
                    <?php }
                } ?>
            </ol>
        <?php }  ?>
        <p style="margin-top: 4em;"><a href="../user/profile.php">Bekijk al je bestellingen »</a></p>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="../about.php">About</a></p>
    </footer>
</body>

</html>