<?php
require_once 'RegistrationForm.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$form = new RegistrationForm();
$form->handleFormSubmission();
$errors = $form->getErrors();
$successMessage = $form->getSuccessMessage();
$username = htmlspecialchars($form->getUsername(), ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($form->getEmail(), ENT_QUOTES, 'UTF-8');
$gender = htmlspecialchars($form->getGender(), ENT_QUOTES, 'UTF-8');
$country = htmlspecialchars($form->getCountry(), ENT_QUOTES, 'UTF-8');
$agbs = htmlspecialchars($form->getAgbs(), ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Formular Validierung</title>
    <link rel="stylesheet" href="./assets/style/style.css" />
    <link rel="stylesheet" href="./assets/style/validierung.css" />
    <script src="./assets/js/main.js" defer></script>
    <script src="./assets/js/validierung.js" defer></script>
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
                    <a href="#"><img src="./assets/img/icon-instagram-black.svg" alt="Facebook" /></a>
                    <a href="#"><img src="./assets/img/icon-facebook-black.svg" alt="Instagram" /></a>
                </nav>
                <div class="burger">
                    <span></span>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="column">
            <div class="top-column">
                <h1>Jetzt Registrieren</h1>
                <h7>
                    Unsere Agentur scheut keinen Tauchgang, denn wir suchen nur die
                    schönsten Perlen Münchens für Sie aus. Sie wollen up to date in Sachen
                    Münchner Events sein? Unser Newsletter stellt exklusiv für Sie alle
                    wichtigsten News zusammen.
                </h7>
                <h3>-Neue Locations und limitierte Angebote</h3>
                <h3>-Exklusive Eventeinladungen in den verschiedensten Branchen</h3>
                <h3>-Emotionsgeladene und innovative Eventformate</h3>
                <h7>
                    Wir versprechen Ihnen keine nervige E-Mail Flut, sondern dedizierter
                    Inhalt, der Ihnen ein Lächeln in das Gesicht zaubert. Für uns stehen
                    die Bedürfnisse unserer Kunden stets im Vordergrund. Dementsprechend
                    schützen wir gemäß der DSGVO Ihre persönlichen Daten vor Dritten und
                    verarbeiten sie nur streng vertraulich unter hohen
                    Sicherheitsauflagen.
                </h7>
            </div>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="form-group">
                    <label for="username">Nutzername:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" />
                    <?php if (isset($errors['username'])) : ?>
                        <div class="error">
                            <?php foreach ($errors['username'] as $error) : ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="email">E-Mail Adresse:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" />
                    <?php if (isset($errors['email'])) : ?>
                        <div class="error">
                            <?php foreach ($errors['email'] as $error) : ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="password">Passwort:</label>
                    <input type="password" id="password" name="password" />
                    <?php if (isset($errors['password'])) : ?>
                        <div class="error">
                            <?php foreach ($errors['password'] as $error) : ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="password_repeat">Passwort wiederholen:</label>
                    <input type="password" id="password_repeat" name="password_repeat" />
                    <?php if (isset($errors['password_repeat'])) : ?>
                        <div class="error">
                            <?php foreach ($errors['password_repeat'] as $error) : ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="gender">Geschlecht:</label>
                    <select id="gender" name="gender">
                        <option value="" disabled <?php if (empty($gender)) echo 'selected'; ?>>Bitte auswählen</option>
                        <option value="männlich" <?php if ($gender === 'männlich') echo 'selected'; ?>>Männlich</option>
                        <option value="weiblich" <?php if ($gender === 'weiblich') echo 'selected'; ?>>Weiblich</option>
                        <option value="divers" <?php if ($gender === 'divers') echo 'selected'; ?>>Divers</option>
                    </select>
                    <?php if (isset($errors['gender'])) : ?>
                        <div class="error">
                            <?php foreach ($errors['gender'] as $error) : ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="country">Land:</label>
                    <select id="country" name="country">
                        <option value="" disabled <?php if (empty($country)) echo 'selected'; ?>>Bitte auswählen</option>
                        <option value="Deutschland" <?php if ($country === 'Deutschland') echo 'selected'; ?>>Deutschland</option>
                        <option value="Österreich" <?php if ($country === 'Österreich') echo 'selected'; ?>>Österreich</option>
                        <option value="Schweiz" <?php if ($country === 'Schweiz') echo 'selected'; ?>>Schweiz</option>
                        <option value="Italien" <?php if ($country === 'Italien') echo 'selected'; ?>>Italien</option>
                        <option value="Frankreich" <?php if ($country === 'Frankreich') echo 'selected'; ?>>Frankreich</option>
                    </select>
                    <?php if (isset($errors['country'])) : ?>
                        <div class="error">
                            <?php foreach ($errors['country'] as $error) : ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <input type="checkbox" id="agbs" name="agbs" <?php if ($agbs === 'on') echo 'checked'; ?> />
                    <label for="agbs">Ich habe die AGBs gelesen und akzeptiere sie.</label>
                    <?php if (isset($errors['agbs'])) : ?>
                        <div class="error">
                            <?php foreach ($errors['agbs'] as $error) : ?>
                                <p><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit">Submit</button>
            </form>
            <?php if (!empty($successMessage)) : ?>
                <p class="success"><?php echo $successMessage; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="index.php">Registrieren</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="">Impressum</a></li>
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
                    <a href="#"><img src="./assets/img/icon-linkedIn-white.svg" alt="linkedin" /></a>
                    <a href="#"><img src="./assets/img/icon-xing-white.svg" alt="xing" /></a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>