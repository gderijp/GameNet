<?php

session_start();
require_once 'config/db.inc.php';

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
        echo "<li class='navList'><a href='admin/adminPage.php'><i class='fa-solid fa-user-tie' style='font-size:26px'></i></a></li>";
    }
}

try {
    $qry = "SELECT * FROM games WHERE id = :id";
    $stmt = $conn->prepare($qry);
    $stmt->execute(
        [
            'id' => $_GET['id']
        ]
    );
    $game = $stmt->fetch();
    if (!$game) {
        header('Location: productPage.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['addToCart'])) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            if (in_array($game->id, $_SESSION['cart'])) {
                throw new Exception("Je hebt dit product al in je winkelmandje zitten!");
            } else {
                $_SESSION['cart'][] = $game->id;
            }

            header('Location: controllers/cart.php');
            exit();
        }

        if (isset($_POST['addToFavorites'])) {
            if (!isset($_SESSION['favorites'])) {
                $_SESSION['favorites'] = [];
            }
            if (in_array($game->id, $_SESSION['favorites'])) {
                throw new Exception("Je hebt dit product al gefavorite!");
            } else {
                $_SESSION['favorites'][] = $game->id;
            }

            header('Location: user/favorites.php');
            exit();
        }

        if (isset($_POST['removeCartId'])) {
            $key = array_search($_POST['removeCartId'], $_SESSION['cart']);
            if ($key !== false) {
                unset($_SESSION['cart'][$key]);
                header('Location: controllers/cart.php');
                exit();
            } else {
                throw new Exception("Er is iets mis gegaan...");
            }
        }

        if (isset($_POST['removeFavoriteId'])) {
            $key = array_search($_POST['removeFavoriteId'], $_SESSION['favorites']);
            if ($key !== false) {
                unset($_SESSION['favorites'][$key]);
                header('Location: user/favorites.php');
                exit();
            } else {
                throw new Exception("Er is iets mis gegaan...");
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
    <title><?php echo isset($game) ? $game->title . ' - ' : '' ?>Detailpagina</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="images/noBgImg.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
</head>

<body>
    <nav class="navContainer">
        <a href="index.php"><img src="images/logo.png" alt="GameNet Logo" class="logoImg"></a>
        <ul class="navRight">
            <li class="navList"><a href="index.php"><i class="fa-solid fa-house" style='font-size:26px'></i></a></li>
            <li class="navList"><a href="productPage.php"><i class="fa-solid fa-shop" style='font-size:26px'></i></a></li>
            <li class="navList"><a href="user/favorites.php"><i class="fa-solid fa-heart" style='font-size:26px'></i></a></li>
            <li class="navList"><a href="controllers/cart.php"><i class="fa-solid fa-cart-shopping" style='font-size:26px'></i></a></li>
            <?php if (isset($_SESSION['loggedInUser'])) {
                $navLink = 'user/profile.php';
            } else {
                $navLink = 'controllers/login.php';
            } ?>
            <li class="navList"><a href="<?php echo $navLink ?>"><i class='fa-solid fa-user' style='font-size:26px'></i></a></li>
            <?php if (isset($_SESSION['user_id'])) {
                adminPageRow($conn);
            } ?>
            <?php if (isset($_SESSION['loggedInUser'])) {
                ?><li class="navList"><a href="controllers/logout.php"><i class="fa-solid fa-right-from-bracket" style='font-size:26px'></i></a></li>
            <?php } ?>
        </ul>
    </nav>

    <div class="container">
        <h1 class="centerText"><?php echo $game->title ?></h1>

        <?php
        $returnPage = "productPage.php";
        if (isset($_SESSION['cart'])) {
            if (in_array($game->id, $_SESSION['cart'])) {
                $returnPage = "controllers/cart.php";
            }
        }
        ?>
        <p class="centerText">
            <a href="<?php echo $returnPage ?>">« Terug</a>
        </p>

        <div class="detailWrapper">
            <div id="detailView">
                <table id="detailTable">
                    <tr>
                        <td><strong>Genre </strong></td>
                        <td><?php echo $game->genre ?></td>
                    </tr>
                    <tr>
                        <td><strong>Prijs </strong></td>
                        <td>€<?php echo $game->price ?></td>
                    </tr>
                    <tr>
                        <td><strong>Rating </strong></td>
                        <td><?php echo $game->stars_rating ?> sterren van de 5</td>
                    </tr>
                    <tr>
                        <td><strong>Releasedate </strong></td>
                        <td><?php echo date("d-m-Y", strtotime($game->release_date)) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Platform </strong></td>
                        <td><?php echo $game->platform ?></td>
                    </tr>
                </table>
            </div>

            <div id="imageView">
                <?php if (!empty($game->image_path)) { ?>
                    <div class="crop">
                        <img src="<?php echo $game->image_path ?>" alt="Logo" id="productImg">
                    </div>
                <?php } ?>
                <form method="POST" style="margin-left: 2.6em">
                    <?php if (isset($_SESSION['cart'])) {
                        if (!in_array($game->id, $_SESSION['cart'])) { ?>
                            <button type="submit" name="addToCart" class="saveBtn">Voeg toe aan je mandje</button>
                        <?php } else { ?>
                            <input type="hidden" name="removeCartId" value="<?php echo $game->id ?>">
                            <button class="saveBtn" style="color: red;">Verwijder uit mandje</button>
                        <?php }
                    } else { ?>
                        <button type="submit" name="addToCart" class="saveBtn">Voeg toe aan je mandje</button>
                    <?php }
                    if (isset($_SESSION['favorites'])) {
                        if (!in_array($game->id, $_SESSION['favorites'])) { ?>
                            <button type="submit" name="addToFavorites" class="saveBtn">Voeg toe aan je favorieten</button>
                        <?php } else { ?>
                            <input type="hidden" name="removeFavoriteId" value="<?php echo $game->id ?>">
                            <button class="saveBtn" style="color: red;">Verwijder uit je favorieten</button>
                        <?php }
                    } else { ?>
                        <button type="submit" name="addToFavorites" class="saveBtn">Voeg toe aan je favorieten</button>
                    <?php } ?>
                </form>
            </div>
        </div>

        <?php echo isset($error) ? "<p class='error centerText'>" . $error . "</p>" : '' ?>

        <div>
            <h2 class="centerText">Beschrijving:</h2>
            <fieldset class="article">
                <p class="centerText">
                    <?php echo $game->description ?>
                </p>
            </fieldset>
        </div>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="about.php">About</a></p>
    </footer>
</body>

</html>