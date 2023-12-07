<?php
require __DIR__ . '/shared/auth.php';
$login = getUserLogin();
$is_author = false;
$profileId = -1;
$mysqli = new mysqli("db", "root", "password", "mydb");
if($login != null){
    $result = $mysqli->query("SELECT email, is_author, id_profile FROM profile WHERE email = '$login'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $profileId = $row["id_profile"];
        $is_author = $row["is_author"] == 1? true : false;
    }
}
else{
    header('Location: /login.php', true); 
}
if(isset($_POST['emailReg'])){
    $emailReg = $_POST['emailReg'] . '%';
}
else{
    $emailReg = '%';
}
if(isset($_POST['lessonNameReg'])){
    $lessonName = $_POST['lessonNameReg'] . '%';
}
else{
    $lessonName = '%';
}
?>
<head>
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="css/login.css" type="text/css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<?php
require __DIR__ . '/shared/menu.php';?>
<form style="margin-left:5%; margin-right:5%" action="lessons.php" method="post">
    <label for="emailReg">"Фильтр по email"</label>
    <input name="emailReg" type="text" placeholder="Фильтр по email автора">
    <label for="lessonNameReg">"Фильтр по названию урока"</label>
    <input name="lessonNameReg" type="text" placeholder="Фильтр по названию урока">
    <input class="btn-primary" type="submit" value="Отправить">
</form>
<?php
$result = $mysqli->query("SELECT lesson.id_lesson, lesson.lesson_title, lesson.is_hidden, profile.email, profile.is_banned FROM lesson,
 profile WHERE lesson.id_author = profile.id_profile AND profile.email LIKE '$emailReg' AND 
 lesson.lesson_title LIKE '$lessonName' AND  profile.is_banned = 0 AND lesson.is_hidden = 0");?>
<form>
<ul style="margin-left:5%; margin-right:5%" class="list-group">
<?php foreach ($result as $row):?>
    <?php echo "<li class='list-group-item'>урок: {$row['lesson_title']} автор: {$row['email']} <a href='mytasks.php?lesson_id={$row['id_lesson']}'>перейти</a></li>"; ?>
<?php endforeach; ?>
</ul>
</form>
</body>