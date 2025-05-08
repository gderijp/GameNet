<?php

session_start();
require 'db.inc.php';
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
        echo "<li class='navList'><a href='adminPage.php'><i class='fa-solid fa-user-tie' style='font-size:26px'></i></a></li>";
    }
}

try {
    $userQry = "SELECT * FROM users WHERE id = :id";
    $stmt = $conn->prepare($userQry);
    $stmt->execute(
        [
            'id' => $_SESSION['user_id']
        ]
    );

    while ($row = $stmt->fetch()) {
        $firstName = $row->first_name;
        $lastName = $row->last_name;
        $email = $row->email;
    }

    $ordersQry = "SELECT * FROM orders WHERE userId = :userId";
    $stmt = $conn->prepare($ordersQry);
    $stmt->execute(
        [
            'userId' => $_SESSION['user_id']
        ]
    );
    $orders = $stmt->fetchAll();
    if (empty($orders)) {
        throw new Exception("Je hebt nog geen producten besteld <a href='productPage.php' class='hover'>Shop hier! »</a>");
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
    <title>Profiel</title>
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
            <?php } ?>
        </ul>
    </nav>

    <div class="container">
        <h1>Profiel</h1>
        <p><strong>Naam:</strong> <?php echo "$firstName $lastName" ?></p>
        <p><strong>Emailadres:</strong> <?php echo $email ?></p>

        <p><a href="editUser.php" class="hover">Wijzig gegevens</a> - <a href="editPassword.php" class="hover">Wijzig wachtwoord</a></p>
        <p><a href="deleteUser.php" class="hover error">Verwijder account</a></p>

        <h2 style="margin-top: 2em;">Bestellingen</h2>
        <?php echo isset($error) ? "<p class=''>" . $error . "</p>" : '' ?>
        <ol>
            <?php foreach ($orders as $order) { ?>
                <li>
                    <table class="productTable">
                        <tr>
                            <th>Producten</th>
                            <td>
                                <?php $games = array_filter(array_map('trim', explode(',', $order->games_list)));
                                echo implode(', ', $games); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Totale prijs</th>
                            <td>€<?php echo $order->total_price ?></td>
                        </tr>
                        <tr>
                            <th>Besteld op</th>
                            <td><?php echo $order->order_date ?></td>
                        </tr>
                    </table>
                </li>
            <?php } ?>
        </ol>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="about.php">About</a></p>
    </footer>
</body>

</html>