<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XO</title>
    <link rel="stylesheet" href="styles/style.css?time=<?= microtime(true) ?>">
</head>
<body>
    <div class="container">
        <h1 class="hello-text">
            <?php
                if($username):
            ?>
                    Здравствуйте, <span id="username"><?= $username ?></span>!
            <?php
                else:
            ?>
                    Представьтесь:
            <?php
                endif;
            ?>
        </h1>
      
        <?php
                if($username):
                    include_once('game.php');
                else:
                    include_once('auth.php');
                endif;
        ?>
    </div>
    <script src="scripts/jquery-3.7.1.min.js"></script>
    <script src="scripts/script.js?time=<?= microtime(true) ?>"></script>
</body>
</html>