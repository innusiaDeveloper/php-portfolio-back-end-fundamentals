<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'LoginForm.php';

$form = new LoginForm();
$form->handleFormSubmission();
$errors = $form->getErrors();
$successMessage = $form->getSuccessMessage();
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Formular</title>
    <link rel="stylesheet" href="./assets/style/style.css" />
    <link rel="stylesheet" href="./assets/style/login.css" />
    <script src="./assets/js/main.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <header>
        <div class="wrapper-header">
            <div class="header">
                <div class="logo">
                    <a href="#">
                        <img class="desktop-logo" src="./assets/img/logo-homepage-nav.svg" alt="Creative Event Solutions Logo" />
                    </a>
                    <a href="#">
                        <img class="mobile-logo" src="./assets/img/mobile.svg" alt="Creative Event Solutions Mobile Logo" />
                    </a>
                </div>
                <nav class="nav">
                    <a href="index.php" class="nav-button">Registrieren</a>
                    <a href="blog.php" class="nav-button">Blog</a>
                    <a href="login.php" class="nav-button">Einloggen</a>
                    <a href="#"><img src="./assets/img/icon-instagram-black.svg" alt="Instagram" /></a>
                    <a href="#"><img src="./assets/img/icon-facebook-black.svg" alt="Facebook" /></a>
                </nav>
                <div class="burger">
                    <span></span>
                </div>
            </div>
        </div>
    </header>
    <section id="form">
        <div class="wrapper">
            <div class="form-container">
                <form id="login-form" action="login.php" method="POST">
                    <div class="form-title">
                        <h2>Einloggen</h2>
                    </div>
                    <?php if (!empty($successMessage)) : ?>
                        <div class="success-message">
                            <p><?php echo $successMessage; ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="username">Nutzername:</label>
                        <input type="text" id="username" name="username" />
                        <?php if (!empty($errors['username'])) : ?>
                            <span class="error-message"><?php echo $errors['username']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="password">Passwort:</label>
                        <input type="password" id="password" name="password" />
                        <?php if (!empty($errors['password'])) : ?>
                            <span class="error-message"><?php echo $errors['password']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <button type="submit">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="validierung.html">Kontakt</a></li>
                    <li><a href="datenbankabfrage.html">Blog</a></li>
                    <li><a href="#">Impressum</a></li>
                    <li><a href="#">Datenschutz</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Kontakt</h4>
                <ul>
                    <li><a href="#">Kaufingerstr.5</a></li>
                    <li><a href="#">80333 München</a></li>
                    <li><a href="#">+49(0)39-35-665</a></li>
                    <li><a href="#">info@kontakt.com</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>© 2024 Creativ Event Solutions</h4>
                <div class="social-links">
                    <a href="#"><img src="./assets/img/icon-facebook-white.svg" alt="facebook" /></a>
                    <a href="#"><img src="./assets/img/icon-Instagram-white.svg" alt="instagram" /></a>
                    <a href="#"><img src="./assets/img/icon-linkedIn-white.svg" alt="inkedin" /></a>
                    <a href="#"><img src="./assets/img/icon-xing-white.svg" alt="inkedin" /></a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>