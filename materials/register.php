<html lang="en">
<head>
<title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="css/login.css" type="text/css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<?php
    require __DIR__ . '/shared/auth.php';
    require __DIR__ . '/shared/menu.php';
?>
<form  class="glass glass-form" action="register.php" method="post">
    <h2 class="form-title">Регистрация</h2>
    <div class="glass-square" id="sq1"></div>
    <div class="glass-square" id="sq2"></div>
    <div class="glass-square" id="sq3"></div>
    <div class="glass-square" id="sq4"></div>
    <div class="glass-square-h" id="hsq2"></div>
    <div class="glass-square-h" id="hsq3"></div>
    <div class="glass-square-h" id="hsq4"></div>
    <input name="email" class="glass glass-field" type="text" placeholder="Email" required>
    <input name="password" class="glass glass-field" type="password" placeholder="Пароль" required>
    <input class="glass-button" type="submit" value="Отправить">
    <a style="margin-left: 10%" href="login.php">Есть аккаунт? Авторизируйтесь</a>
</form>
<?php
$mysqli = new mysqli("db", "root", "password", "mydb");
if(isset($_POST["email"]) && isset($_POST["password"])){
    $email = $mysqli->real_escape_string($_POST["email"]);
    $password = $mysqli->real_escape_string($_POST["password"]);
    if($mysqli->query("INSERT INTO profile (email, password_hash) VALUES ('$email', '$password')")){
        echo "Данные успешно добавлены";
        setcookie('login', $email, 0, '/');
        setcookie('password', $password, 0, '/');
        header('Location: /index.php');
    } else{
        echo "Ошибка: " . $mysqli->error;
    }
}
$mysqli->close();
?>
</body>
</html>