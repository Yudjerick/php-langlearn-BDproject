<html lang="en">
<head>
<title>Вход</title>
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="css/login.css" type="text/css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>

<?php
require __DIR__ . '/shared/auth.php';
$err_msg = "";
$mysqli = new mysqli("db", "root", "password", "mydb");
if(isset($_POST["email"]) && isset($_POST["password"])){
    $email = $mysqli->real_escape_string($_POST["email"]);
    $password = $mysqli->real_escape_string($_POST["password"]);
    $result = $mysqli->query("SELECT email, password_hash FROM profile WHERE email = '$email'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if($row["password_hash"] == $password){
            if(checkBanned($email)){
                $err_msg = "Вы были забанены";
            }
            else{
                setcookie('login', $row["email"], 0, '/');
                setcookie('password', $row["password_hash"], 0, '/');
                header('Location: /index.php');
            }
            
        }
    } else {
        $err_msg = "Пользователь не найден";
    }
}
$mysqli->close();
?>
<?php

require __DIR__ . '/shared/menu.php';
?>
<form  class="glass glass-form" action="login.php" method="post">
    <h2 class="form-title">Вход в аккаунт</h2>
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
    <a style="margin-left: 10%" href="register.php">Зарегистрироваться</a>
    <?php if($err_msg != ""){
        echo '<p style="margin-left: 10%" href="register.php">' . $err_msg . '</p>';
    }?>
</form>
</body>
</html>