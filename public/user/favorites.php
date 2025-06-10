<?php

session_start();
require_once '../config/db.inc.php';

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
        $qry = "SELECT * FROM games WHERE id = :id";
        $stmt = $conn->prepare($qry);
        $stmt->execute(
            [
                'id' => $_POST['id']
            ]
        );
        $game = $stmt->fetch();

        if (isset($_POST['addToCart'])) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            if (in_array($game->id, $_SESSION['cart'])) {
                throw new Exception("Je hebt dit product al in je winkelmandje zitten!");
            } else {
                $_SESSION['cart'][] = $game->id;
            }

            if (isset($_SESSION['favorites'])) {
                $key = array_search($game->id, $_SESSION['favorites']);
                if ($key !== false) {
                    unset($_SESSION['favorites'][$key]);
                    header('Location: ../controllers/cart.php');
                    exit();
                }
            }
        }

        if (isset($_POST['removeId'])) {
            $key = array_search($_POST['removeId'], $_SESSION['favorites']);
            if ($key !== false) {
                unset($_SESSION['favorites'][$key]);
                header('Location: favorites.php');
                exit();
            } else {
                throw new Exception("Er is iets mis gegaan...");
            }
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
    <title>Verlanglijstje</title>
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
        <h1>Verlanglijstje</h1>
        <?php echo isset($error) ? "<p class='error centerText'>" . $error . "</p>" : '' ?>
        <?php if (isset($_SESSION['favorites']) && !empty($_SESSION['favorites'])) { ?>
            <table class="productTable">
                <?php $counter = 0;
                if ($_SESSION['favorites'] && !empty($_SESSION['favorites'])) {
                    $_SESSION['orderPrice'] = 0;
                    foreach ($_SESSION['favorites'] as $cartItem) {
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
                                    <td><br><a href="../detailPage.php?id=<?php echo $game->id ?>" class="saveBtn noUnderline">Bekijk details</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?php echo $game->id ?>">
                                            <button type="submit" name="addToCart" class="saveBtn">Voeg toe aan je mandje</button>

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
                }

                ?>
            </table>
            <?php
        } else { ?>
            <p class="error">Je hebt nog niks aan je favorieten toegevoegd! <a href="../productPage.php" class="hover">Browse hier! »</a></p>
            <?php
        }
        ?>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="../about.php">About</a></p>
    </footer>
</body>

</html>