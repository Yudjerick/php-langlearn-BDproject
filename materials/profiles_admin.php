<?php
require __DIR__ . '/shared/auth.php';
$login = getUserLogin();
$is_author = false;
$profileId = -1;
$is_banned = false;
$mysqli = new mysqli("db", "root", "password", "mydb");
if($login != null){
    $result = $mysqli->query("SELECT * FROM profile WHERE email = '$login'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $profileId = $row["id_profile"];
        $is_author = $row["is_author"] == 1? true : false;
        $is_admin = $row["is_admin"] == 1? true : false;
        $is_banned = $row["is_banned"] == 1? true: false;
    }
}
else{
    header('Location: /login.php', true); 
}
if($is_banned){
    http_response_code(403);
    die('Вы забанены');
}
if($is_author == false){
    http_response_code(403);
    die('Forbidden');
}
if(isset($_POST['emailReg'])){
    $emailReg = $_POST['emailReg'] . '%';
}
else{
    $emailReg = '%';
}
if(isset($_POST['del_profile']) && isset($_GET['profile_id'])){
    $toDel = $_GET['profile_id'];
    if($mysqli->query("DELETE FROM profile WHERE id_profile = '$toDel'")){
    } else{
        echo "Ошибка: " . $mysqli->error;
    }
}
if(isset($_POST['ban_profile']) && isset($_GET['profile_id'])){
    $toDel = $_GET['profile_id'];
    $result = $mysqli->query("SELECT * FROM profile WHERE id_profile = '$toDel'");
    $banned = $result->fetch_assoc()["is_banned"] ? 0 : 1;
    if($mysqli->query("UPDATE profile SET is_banned = '$banned' WHERE id_profile = '$toDel'")){
    } else{
        echo "Ошибка: " . $mysqli->error;
    }
}
?>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="css/login.css" type="text/css"/>
</head>
<body>
<?php
require __DIR__ . '/shared/menu.php';?>
<form action="lessons.php" method="post">
    <label for="emailReg">"Фильтр по email"</label>
    <input name="emailReg" type="text" placeholder="Фильтр по email автора">
    <input type="submit" value="Отправить">
</form>
<?php
$result = $mysqli->query("SELECT * FROM profile WHERE email LIKE '$emailReg' AND email != '$login'");?>
<ul class="list-group">
<?php foreach ($result as $row):?>
    <?php 
    $toDelete = $row['id_profile'];
    $banned = $row['is_banned'] == 1 ? true : false;?>
    <div class="list-group-item">
    <?php echo "<p>id: {$row['id_profile']}  email: {$row['email']}  is_author: {$row['is_author']}</p>"; ?>
    <div class="d-flex flex-row">
    
    <?php 
    echo '<form class="d-flex flex-row" action="profiles_admin.php?profile_id='. $toDelete . '" method="post">'?>
    <input type="text" name="del_profile" value="del_profile" style="visibility: hidden; position: absolute">
    <input class="btn-danger" type="submit" value="Удалить аккаунт">
    </form>
    <?php 
    echo '<form action="profiles_admin.php?profile_id='. $toDelete . '" method="post">'?>
    <input type="text" name="ban_profile" value="ban_profile" style="visibility: hidden; position: absolute">
    <?php if (!$banned): ?>
        <input class="btn-secondary" type="submit" value="Забанить аккаунт">
    <?php else: ?>
        <input class="btn-outline-secondary" type="submit" value="Разбанить аккаунт">
    <?php endif; ?>
    </form>
    </div>
    </div>
<?php endforeach; ?>
</ul>