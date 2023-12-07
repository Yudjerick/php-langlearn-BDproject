<?php
require __DIR__ . '/shared/auth.php';
require __DIR__ . '/shared/menu.php';
?>
<html>
<head>
<title>Вход</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="css/login.css" type="text/css"/>
</head>
<body>
<body>
<?php 
$login = getUserLogin();
?>
<?php if ($login === null): ?>
    
<a href="/login.php">Авторизуйтесь</a>
<?php else: ?>
    <?php if(checkBanned($login)){
        http_response_code(403);
        die('Banned');
    }?>
<h1 style="margin-left: 10%">Добро пожаловать, <?= $login ?></h1>
<?php endif; ?>
</body>
</html>