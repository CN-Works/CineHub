<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/template.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Orbitron">
    <!-- Tab icons -->
    <link rel="icon" href="public/images/cinehub_small.png">
    <title> <?php echo $title ?> </title>
</head>

<body>
    <header>
        <img class="header-logo" src="public/images/cinehub.png" alt="CineHub logo">
        <ul>
            <li><a class="header-category" href="index.php?action=HomePage">Home</a></li>
            <li><a class="header-category" href="index.php?action=movieList">Movies</a></li>
            <li><a class="header-category" href="index.php?action=genreList">Genre</a></li>
            <li><a class="header-category" href="index.php?action=actorList">Actors</a></li>
            <li><a class="header-category" href="index.php?action=producerList">Producers</a></li>
        </ul>
        <a href="">Connexion</a>
    </header>

    <?php echo $content ?>

    <footer>
        <small class="footer-text">Movie website named "CineHub", made by <a href="https://github.com/CN-Works" class="text-orange gitlink">github.com/CN-Works</a>.</small>
    </footer>
</body>
</html>