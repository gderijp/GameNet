<?php

session_start();
require_once '../config/db.inc.php';

if (!isset($_SESSION['loggedInUser'])) {
    header('Location: ../controllers/login.php');
}

if (!isset($_GET['id'])) {
    header('Location: adminPage.php');
    exit();
}

$qry = "SELECT * FROM games WHERE id = :id";
$stmt = $conn->prepare($qry);
$stmt->execute(
    [
        'id' => $_GET['id']
    ]
);
$game = $stmt->fetch();

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

function notNull($columnName, $label)
{
    if (is_null($columnName) || $columnName == '') {
        throw new Exception("Het veld $label mag niet leeg zijn!");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $title = $_POST['title'];
        $genre = $_POST['genre'];
        $rating = floatval($_POST['rating']);
        if ($_POST['rating'] < 0 || $_POST['rating'] > 5) {
            throw new Exception("De rating mag niet lager zijn dan 0, of hoger zijn dan 5.");
        }
        $platform = $_POST['platform'];
        $price = floatval($_POST['price']);
        $releasedate = $_POST['releasedate'];
        $description = $_POST['description'];

        notNull($title, "'Titel'");
        notNull($genre, "'Genre'");
        notNull($platform, "'Platform'");
        notNull($price, "'Prijs'");
        notNull($releasedate, "'Uitgifte datum'");

        $updateQuery = "UPDATE games 
        SET title = :title,
        genre = :genre,
        stars_rating = :stars_rating,
        platform = :platform,
        price = :price,
        release_date = :release_date,
        description = :description
        WHERE id = :id";

        $update = $conn->prepare($updateQuery);
        $update->execute(
            [
                'title' => $title,
                'genre' => $genre,
                'stars_rating' => $rating,
                'platform' => $platform,
                'price' => $price,
                'release_date' => $releasedate,
                'description' => $description,
                'id' => $_GET['id']
            ]
        );

        header('Location: adminPage.php');
        exit();
    } catch (PDOException $err) {
        $error = "<p class='error centerText'>" . $err->getMessage() . "</p>";
    } catch (Exception $ex) {
        $error = "<p class='error centerText'>" . $ex->getMessage() . "</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bewerk product</title>
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
            <?php if (isset($_SESSION['user_id'])) {
                adminPageRow($conn);
            } ?>
            <?php if (isset($_SESSION['loggedInUser'])) {
            ?><li class="navList"><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket" style='font-size:26px'></i></a></li>
            <?php } ?>
        </ul>
    </nav>

    <div class="container">
        <h1 class="centerText"><?php echo $game->title ?></h1>

        <?php echo (isset($error) ? $error : '') ?>
        <p class="centerText"><a href="adminPage.php">« terug</a></p>
        <form method="post" class="formContainer">
            <fieldset>
                <div>
                    <label for="title" class="formLabel">Titel</label>
                    <input type="text" name="title" id="title" value="<?php echo (isset($title)) ? $title : $game->title ?>">
                </div>

                <div>
                    <label for="genre" class="formLabel">Genre</label>
                    <input type="text" name="genre" id="genre" value="<?php echo (isset($genre)) ? $genre : $game->genre ?>">
                </div>

                <div>
                    <label for="rating" class="formLabel">Aantal sterren</label>
                    <input type="number" min="0" max="5" step="0.1" name="rating" id="rating" value="<?php echo (isset($rating)) ? $rating : $game->stars_rating ?>">
                </div>

                <div>
                    <label for="platform" class="formLabel">Platform</label>
                    <input type="text" name="platform" id="platform" value="<?php echo (isset($platform)) ? $platform : $game->platform ?>">
                </div>

                <div>
                    <label for="price" class="formLabel">Prijs</label>
                    <input type="number" name="price" id="price" min="0" step="0.01" value="<?php echo (isset($price)) ? $price : $game->price ?>">
                </div>

                <div>
                    <label for="releasedate" class="formLabel">Uitgifte datum</label>
                    <input type="date" name="releasedate" id="releasedate" value="<?php echo (isset($releasedate)) ? $releasedate : $game->release_date ?>">
                </div>

                <div>
                    <label for="description" class="formLabel">Beschrijving</label>
                    <div>
                        <textarea name="description" id="description" rows="4" cols="35"><?php echo (isset($description)) ? $description : $game->description ?></textarea>
                    </div>
                </div>

                <input type="submit" class="saveBtn" value="Opslaan">
            </fieldset>
        </form>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="../about.php">About</a></p>
    </footer>
</body>

</html>