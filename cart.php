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

$orderPrice = 0;
$gamesList = '';
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cartItem) {
        try {
            $stmt = $conn->prepare("SELECT * FROM games WHERE id = :id");
            $stmt->execute(
                [
                    'id' => $cartItem
                ]
            );
            $game = $stmt->fetch();

            $orderPrice += $game->price;
            $gamesList .= $game->title . ",";
        } catch (PDOException $err) {
            $error = $err->getMessage();
        }
        $_SESSION['orderPrice'] = $orderPrice;
        $_SESSION['gamesList'] = $gamesList;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['removeId'])) {
            $key = array_search($_POST['removeId'], $_SESSION['cart']);
            if ($key !== false) {
                unset($_SESSION['cart'][$key]);
                header('Location: cart.php');
                exit();
            } else {
                throw new Exception("Er is iets mis gegaan...");
            }
        }

        if (isset($_SESSION['loggedInUser'])) {
            $userId = null;
            if (isset($_POST['placeOrder'])) {
                if (isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                }

                $gamesList = $_SESSION['gamesList'];
                unset($_SESSION['gamesList']);

                $insertQry = "INSERT INTO orders (userId, total_price, games_list) VALUES (:userId, :totalPrice, :gamesList)";
                $stmt = $conn->prepare($insertQry);
                $stmt->execute(
                    [
                        'userId' => $userId,
                        'totalPrice' => $orderPrice,
                        'gamesList' => $gamesList
                    ]
                );
                header('Location: order.php');
                unset($_SESSION['cart']);
                exit();
            }
        } else {
            $_SESSION['triedToOrder'] = true;
            header('Location: login.php');
            exit();
        }
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
    <title>Winkelmandje</title>
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
            } ?>
        </ul>
    </nav>

    <div class="container">
        <h1>Winkelmandje</h1>
        <?php echo (isset($error)) ? $error : '' ?>

        <?php
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            $counter = 0; ?>
            <table class="productTable">
                <?php foreach ($_SESSION['cart'] as $cartItem) {
                    try {
                        $stmt = $conn->prepare("SELECT * FROM games WHERE id = :id");
                        $stmt->execute(
                            [
                                'id' => $cartItem
                            ]
                        );
                        $game = $stmt->fetch();
                    } catch (PDOException $err) {
                        $error = $err->getMessage();
                    }

                    if ($counter % 4 == 0) {
                        echo "<tr>";
                    } ?>
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
                                <td><br><a href="detailPage.php?id=<?php echo $game->id ?>" class="saveBtn noUnderline">Bekijk details</a></td>
                            </tr>
                            <tr>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="removeId" value="<?php echo $game->id ?>">
                                        <button class="saveBtn" style="color: red;">Verwijder</button>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <?php
                    $counter++;
                    if ($counter % 4 == 0) {
                        echo "</tr>";
                    }
                }

                ?>
            </table>

            <p><strong>Totale prijs:</strong> €<?php echo $_SESSION['orderPrice'] ?></p>
            <form method="POST">
                <button type="submit" name="placeOrder" class="saveBtn">Bestel!</button><?php echo !isset($_SESSION['loggedInUser']) ? "<small> (Log eerst in)</small>" : '' ?>
            </form>
            <?php
        } else { ?>
            <p class="error">Je hebt nog niks aan je wagentje toegevoegd! <a href="productPage.php" class="hover">Verder winkelen »</a></p>
        <?php }
        ?>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="about.php">About</a></p>
    </footer>
</body>

</html>