<?php
require __DIR__ . '/shared/auth.php';

$mysqli = new mysqli("db", "root", "password", "mydb");
$login = getUserLogin();
if ($login === null) {
    header('Location: login.php');
}
if(checkBanned($login)){
    http_response_code(403);
    die('Banned');
}
$result = $mysqli->query("SELECT id_profile, email, is_author, reg_date FROM profile WHERE email = '$login'");
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $reg_date = $row["reg_date"];
    $profileId = $row['id_profile'];
}

if(isset($_POST['become_author'])) {
$result = $mysqli->query("UPDATE profile SET is_author = 1 WHERE email = '$login'");
    $result = $mysqli->query("SELECT email, is_author, reg_date FROM profile WHERE email = '$login'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $is_author = $row["is_author"] == 1? true : false;
        $mysqli->query("CALL create_default_lesson ('$profileId')");
    }
}
$mysqli->close();
?>
<head>
<title>Вход</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="css/login.css" type="text/css"/>
</head>
<body>
<?php
require __DIR__ . '/shared/menu.php';
?>
<?php if (!$is_author): ?>
    <form style="margin-left:5%; margin-right:5%" action="profile.php"  method="post">
        <input type="submit" name="become_author" value="Стать автором" class="btn-success" />
    </form>
<?php else: ?>
    <p style="margin-left:5%; margin-right:5%" class="font-weight-bold">Вы являетесь автором</p>
<?php endif; ?>
<p style="margin-left:5%; margin-right:5%"><?php echo "Дата регистрации: " .  $reg_date?></p>
<a style="margin-left:5%; margin-right:5%" href="/logout.php">Выйти</a>
</body>