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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About page</title>
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
            <?php if (isset($_SESSION['loggedInUser'])) { ?>
                <li class="navList"><a href="controllers/logout.php"><i class="fa-solid fa-right-from-bracket" style='font-size:26px'></i></a></li>
            <?php } ?>
        </ul>
    </nav>

    <div class="container">
        <h1>Over mij</h1>
        <div id="aboutWrapper">
            <article class="article">
                <h3>Wie ben ik?</h3>
                <p>
                    Mijn naam is Giorgio de Rijp. Op het moment nog 19 jaartjes jong en ik heb een passie voor leren. In 2023 ben ik begonnen met een HBO Software Engineering opleiding met alleen een havo diploma op zak.
                    Met moeite heb ik mijn propedeuse weten te bemachtigen, maar ik wist al dat ik met deze kennis het niet ver zou schoppen. Na een poos out of the running te zijn geweest, heb ik het toch weer opgepakt en ben ik begonnen bij de BitAcademy!
                    Ik ben nog geen anderhalve maand geleden begonnen, en toch zijn we nu al hier!
                </p><br>

                <h3>Achter deze website</h3>
                <p>
                    17 april 2025, de start van dit project. Zo begon deze opdracht: Bouw iets vanuit het niets.
                    Ik heb ervoor gekozen om een website te bouwen die een beetje wat weg heeft van de bekende webwinkels als Bol, maar met een vleugje GameMania en andere gamewebshops.
                    Ik ben nog een beginner in dit wereldje, dus als er bugs optreden op mijn site mag je dat gerust aangeven!
                </p><br>

                <h3>Contact</h3>
                <p>Wil je contact met mij opnemen? <strong>Dat kan!</strong></p>
                Email: <a href="mailto:gderijp@gmail.com">gderijp@gmail.com</a><br>
                Telefoonnummer: <a href="tel:0648656328">06 48656328</a><br>
                <a href="https://www.linkedin.com/in/gderijp" target="_blank">LinkedIn</a> - <a href="https://github.com/gderijp" target="_blank">GitHub</a>
            </article>
            <img src="images/logo.png" alt="Large website logo" id="aboutLogo">
        </div>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="about.php">About</a></p>
    </footer>
</body>

</html>