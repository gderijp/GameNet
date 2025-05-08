<?php

session_start();
require 'db.inc.php';

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
        echo "<li class='navList'><a href='adminPage.php'><i class='fa-solid fa-user-tie' style='font-size:26px'></i></a></li>";
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $gameId = '';
        if (isset($_POST['gameId'])) {
            $gameId = $_POST['gameId'];
            if (!$gameId) {
                throw new Exception("Geen ID gevonden");
            }
        }

        if (isset($_POST['addToCart'])) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (in_array($gameId, $_SESSION['cart'])) {
                throw new Exception("Je hebt dit product al in je winkelmandje zitten!");
            } else {
                $_SESSION['cart'][] = $gameId;
            }

            header('Location: index.php');
            exit();
        }

        if (isset($_POST['removeCartId'])) {
            $key = array_search($gameId, $_SESSION['cart']);
            if ($key !== false) {
                unset($_SESSION['cart'][$key]);

                header('Location: index.php');
                exit();
            } else {
                throw new Exception("Er is iets mis gegaan...");
            }
        }

        if (isset($_POST['addToFavorites'])) {
            if (!isset($_SESSION['favorites'])) {
                $_SESSION['favorites'] = [];
            }

            if (!in_array($gameId, $_SESSION['favorites'])) {
                $_SESSION['favorites'][] = $gameId;
            } else {
                throw new Exception("Je hebt dit product al gefavorite!");
            }

            header('Location: index.php');
            exit();
        }

        if (isset($_POST['removeFavorite'])) {
            if (isset($_SESSION['favorites'])) {
                $key = array_search($gameId, $_SESSION['favorites']);
                if ($key !== false) {
                    unset($_SESSION['favorites'][$key]);
                    header('Location: index.php');
                    exit();
                }
            }
        }
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
    <title>Backend Eindproject</title>
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
            <?php if (isset($_SESSION['loggedInUser'])) {
                $navLink = 'profile.php';
            } else {
                $navLink = 'login.php';
            } ?>
            <li class="navList"><a href="<?php echo $navLink ?>"><i class='fa-solid fa-user' style='font-size:26px'></i></a></li>
            <?php if (isset($_SESSION['user_id'])) {
                adminPageRow($conn);
            } ?>
            <?php if (isset($_SESSION['loggedInUser'])) {
                ?><li class="navList"><a href="logout.php"><i class="fa-solid fa-right-from-bracket" style='font-size:26px'></i></a></li>
                <?php
            }
            ?>
        </ul>
    </nav>

    <div class="container">
        <div class="wrapper">
            <span class="centerText">
                <?php if (isset($_SESSION['loggedInUser'])) { ?>
                    Welkom bij </span><img src='noBgImg.png' alt='GameNet Logo' id='indexImg'> <span>
                    <?php echo $_SESSION['username'] . "!</span>";
                } else { ?>
                Welkom bij </span>
            <img src='noBgImg.png' alt='GameNet Logo' id='indexImg'>!
                <?php } ?>
        </div>
        <?php echo isset($error) ? "<p class='error'" . $error . "</p>" : '' ?>

        <h2>Nieuwste games:</h2>
        <p class="centerText">
        </p>
        <table class="productTable">
            <tr>
                <?php
                try {
                    $qry = "SELECT * FROM games ORDER BY id DESC LIMIT 3";
                    $stmt = $conn->prepare($qry);
                    $stmt->execute();

                    foreach ($stmt->fetchAll() as $game) { ?>
                        <td class="cell">
                            <table class="innerTable">
                                <tr>
                                    <th><?php echo $game->title ?></th>
                                </tr>
                                <tr>
                                    <td><strong>Genre:</strong> <?php echo $game->genre ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Platform:</strong> <?php echo $game->platform ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Prijs:</strong> €<?php echo $game->price ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Rating:</strong> <?php echo $game->stars_rating ?> sterren</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="buttonRow">
                                            <a href="detailPage.php?id=<?php echo $game->id ?>" class="saveBtn noUnderline noMargin"><i class="fa-solid fa-circle-info"></i> Details</a>

                                            <form method="POST">
                                                <input type="hidden" name="gameId" value="<?php echo $game->id ?>">
                                                <?php if (isset($_SESSION['favorites']) && (in_array($game->id, $_SESSION['favorites']))) { ?>
                                                    <button class="saveBtn noMargin" name="removeFavorite" style="color: red;"><i class="fa-solid fa-heart-circle-minus" style='font-size:18px'></i></button>
                                                <?php } else { ?>
                                                    <button type="submit" name="addToFavorites" class="saveBtn noMargin"><i class="fa-solid fa-heart" style='font-size:18px'></i></button>
                                                <?php } ?>
                                            </form>

                                            <form method="post">
                                                <input type="hidden" name="gameId" value="<?php echo $game->id ?>">
                                                <?php if (isset($_SESSION['cart']) && (in_array($game->id, $_SESSION['cart']))) { ?>
                                                    <button class="saveBtn noMargin" name="removeCartId" style="color: red;"><i class="fa-solid fa-cart-shopping" style='font-size:18px'></i></button>
                                                <?php } else { ?>
                                                    <button type="submit" name="addToCart" class="saveBtn noMargin"><i class="fa-solid fa-cart-plus" style='font-size:18px'></i></button>
                                                <?php } ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <?php
                    }
                } catch (PDOException $err) {
                    $error = $err->getMessage();
                }

                ?>
            </tr>
        </table>
        <a href="productPage.php" class="hover" style="float: right;">Bekijk alle producten »</a>

        <h2>Hoogst beoordeelde games:</h2>

        <table class="productTable">
            <tr>
                <?php
                try {
                    $qry = "SELECT * FROM games ORDER BY stars_rating DESC LIMIT 3";
                    $stmt = $conn->prepare($qry);
                    $stmt->execute();

                    foreach ($stmt->fetchAll() as $game) { ?>
                        <td class="cell">
                            <table class="innerTable">
                                <tr>
                                    <th><?php echo $game->title ?></th>
                                </tr>
                                <tr>
                                    <td><strong>Genre:</strong> <?php echo $game->genre ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Platform:</strong> <?php echo $game->platform ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Prijs:</strong> €<?php echo $game->price ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Rating:</strong> <?php echo $game->stars_rating ?> sterren</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="buttonRow">
                                            <a href="detailPage.php?id=<?php echo $game->id ?>" class="saveBtn noUnderline noMargin"><i class="fa-solid fa-circle-info"></i> Details</a>

                                            <form method="POST">
                                                <input type="hidden" name="gameId" value="<?php echo $game->id ?>">
                                                <?php if (isset($_SESSION['favorites']) && (in_array($game->id, $_SESSION['favorites']))) { ?>
                                                    <button class="saveBtn noMargin" name="removeFavorite" style="color: red;"><i class="fa-solid fa-heart-circle-minus" style='font-size:18px'></i></button>
                                                <?php } else { ?>
                                                    <button type="submit" name="addToFavorites" class="saveBtn noMargin"><i class="fa-solid fa-heart" style='font-size:18px'></i></button>
                                                <?php } ?>
                                            </form>

                                            <form method="post">
                                                <input type="hidden" name="gameId" value="<?php echo $game->id ?>">
                                                <?php if (isset($_SESSION['cart']) && (in_array($game->id, $_SESSION['cart']))) { ?>
                                                    <button class="saveBtn noMargin" name="removeCartId" style="color: red;"><i class="fa-solid fa-cart-shopping" style='font-size:18px'></i></button>
                                                <?php } else { ?>
                                                    <button type="submit" name="addToCart" class="saveBtn noMargin"><i class="fa-solid fa-cart-plus" style='font-size:18px'></i></button>
                                                <?php } ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <?php
                    }
                } catch (PDOException $err) {
                    $error = $err->getMessage();
                }

                ?>
            </tr>
        </table>
        <a href="productPage.php" class="hover" style="float: right;">Bekijk alle producten »</a>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="about.php">About</a></p>
    </footer>
</body>

</html>