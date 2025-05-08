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

function notNull($columnName, $label)
{
    if (is_null($columnName) || $columnName == '') {
        throw new Exception("<p class='error centerText'>Het veld $label mag niet leeg zijn!</p>");
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

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $savePath = 'uploads/';
            $targetFile = 'uploads/' . $_FILES['image']['name'];

            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imagePath = $targetFile;
                } else {
                    throw new Exception("Fout bij het uploaden van het bestand.");
                }
            } else {
                throw new Exception("Alleen JPG, JPEG en PNG bestanden zijn toegestaan.");
            }
        } else {
            $imagePath = '';
        }

        notNull($title, "'Titel'");
        notNull($genre, "'Genre'");
        notNull($platform, "'Platform'");
        notNull($price, "'Prijs'");
        notNull($releasedate, "'Uitgifte datum'");

        $insertQry = "INSERT INTO games (title, genre, stars_rating, platform, price, release_date, description, image_path)
        VALUES (:title, :genre, :stars_rating, :platform, :price, :release_date, :description, :image_path)";
        $stmt = $conn->prepare($insertQry);
        $stmt->execute(
            [
                'title' => $title,
                'genre' => $genre,
                'stars_rating' => $rating,
                'platform' => $platform,
                'price' => $price,
                'release_date' => $releasedate,
                'description' => $description,
                'image_path' => $imagePath
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
    <title>Product toevoegen</title>
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
        <h1 class="centerText">Voeg een product toe</h1>

        <?php echo (isset($error) ? $error : '') ?>
        <p class="centerText"><a href="adminPage.php">« terug</a></p>
        <form method="post" enctype="multipart/form-data" class="formContainer">
            <fieldset>
                <div>
                    <label for="title" class="formLabel">Titel</label>
                    <input type="text" name="title" id="title" value="<?php echo (isset($title)) ? $title : '' ?>">
                </div>

                <div>
                    <label for="genre" class="formLabel">Genre</label>
                    <input type="text" name="genre" id="genre" value="<?php echo (isset($genre)) ? $genre : '' ?>">
                </div>

                <div>
                    <label for="rating" class="formLabel">Aantal sterren</label>
                    <input type="number" min="0" max="5" step="0.1" name="rating" id="rating" value="<?php echo (isset($rating)) ? $rating : '0.0' ?>">
                </div>

                <div>
                    <label for="platform" class="formLabel">Platform</label>
                    <input type="text" name="platform" id="platform" value="<?php echo (isset($platform)) ? $platform : '' ?>">
                </div>

                <div>
                    <label for="price" class="formLabel">Prijs</label>
                    <input type="number" name="price" id="price" min="0" step="0.01" value="<?php echo (isset($price)) ? $price : '' ?>">
                </div>

                <div>
                    <label for="releasedate" class="formLabel">Uitgifte datum</label>
                    <input type="date" name="releasedate" id="releasedate" value="<?php echo (isset($releasedate)) ? $releasedate : '' ?>">
                </div>

                <div>
                    <label for="description" class="formLabel">Beschrijving</label>
                    <div>
                        <textarea name="description" id="description" rows="4" cols="25"><?php echo (isset($description)) ? $description : '' ?></textarea>
                    </div>
                </div>

                <div>
                    <label for="image" class="formLabel">Foto toevoegen</label>
                    <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png">
                </div>

                <input type="submit" class="saveBtn" value="Voeg toe">
            </fieldset>
        </form>
    </div>

    <footer class="footer-container">
        <p>Copyright &copy; GameNet 2025</p>
        <p><a href="about.php">About</a></p>
    </footer>
</body>

</html>