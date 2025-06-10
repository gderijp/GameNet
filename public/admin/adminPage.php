<?php

session_start();
require_once '../config/db.inc.php';

if (!isset($_SESSION['loggedInUser'])) {
    header('Location: ../controllers/login.php');
}

try {
    if (isset($_GET['id'])) {
        $removeQuery = "DELETE FROM games WHERE id = :id";
        $stmt = $conn->prepare($removeQuery);
        $stmt->execute(
            [
                'id' => $_GET['id']
            ]
        );
    }
} catch (PDOException $err) {
    $error = $err->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
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
            <li class="navList"><a href="../controllers/cart.php"><i class="fa-solid fa-cart-shopping" style='font-size:26px'></i></a></li>
            <?php if (isset($_SESSION['loggedInUser'])) {
                $navLink = '../user/profile.php';
            } else {
                $navLink = '../controllers/login.php';
            } ?>
            <li class="navList"><a href="<?php echo $navLink ?>"><i class='fa-solid fa-user' style='font-size:26px'></i></a></li>
            <?php if (isset($_SESSION['loggedInUser'])) {
                ?><li class="navList"><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket" style='font-size:26px'></i></a></li>
            <?php } ?>
        </ul>
    </nav>

    <div class="container">
        <h1>GameNet beheerders paneel</h1>

        <?php echo (isset($error)) ? $error : '' ?>
        <a href="insert.php">Voeg een product toe »</a>
        <table class="productTable">
            <?php
            try {
                $qry = "SELECT * FROM games ORDER BY id DESC";
                $stmt = $conn->prepare($qry);
                $stmt->execute();

                $counter = 0;
                foreach ($stmt->fetchAll() as $game) {
                    if ($counter % 4 == 0) {
                        echo "<tr>";
                    } ?>
                    <td class='cell'>
                        <table class='innerTable'>
                            <tr>
                                <th><?php echo $game->title ?></th>
                            </tr>
                            <tr>
                                <td><strong>Genre: </strong><?php echo $game->genre ?></td>
                            </tr>
                            <tr>
                                <td><strong>Platform: </strong><?php echo $game->platform ?></td>
                            </tr>
                            <tr>
                                <td><strong>Prijs: </strong>€<?php echo $game->price ?></td>
                            </tr>
                            <tr>
                                <td><a href='edit.php?id=<?php echo $game->id ?>' class="hover">Wijzig product</a></td>
                            </tr>
                            <tr>
                                <td><a href='?id=<?php echo $game->id ?>' class='error hover'>Verwijder</a></td>
                            </tr>
                        </table>
                    </td>
                    <?php
                    $counter++;

                    if ($counter % 4 == 0) {
                        echo "</tr>";
                    }
                }

                if ($counter % 4 != 0) {
                    $remaining = 4 - ($counter % 4);
                    for ($i = 0; $i < $remaining; $i++) {
                        echo "<td class=''></td>";
                    }
                    echo "</tr>";
                }
            } catch (PDOException $err) {
                $error = $err->getMessage();
            }

            ?>
        </table>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="../about.php">About</a></p>
    </footer>
</body>

</html>