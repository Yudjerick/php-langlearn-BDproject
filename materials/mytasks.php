<?php
require __DIR__ . '/shared/auth.php';
$login = getUserLogin();
$is_author = false;
$profileId = -1;
$mysqli = new mysqli("db", "root", "password", "mydb");
if($login != null){
    $result = $mysqli->query("SELECT email, is_author, is_admin, id_profile FROM profile WHERE email = '$login'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $profileId = $row["id_profile"];
        $is_author = $row["is_author"] == 1? true : false;
        $is_admin = $row["is_admin"] == 1? true : false;
    }
}
else{
    header('Location: /login.php', true); 
}
if(checkBanned($login)){
    http_response_code(403);
    die('Banned');
}
if (isset($_GET['lesson_id'])) {
    $lessonId = $_GET['lesson_id'];
}
else {
    echo 'lesson_id not set';
    die('');
}
?>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="css/login.css" type="text/css"/>
</head>
<body>
<?php
require __DIR__ . '/shared/menu.php';
$result = $mysqli->query("SELECT id_task FROM task WHERE id_lesson = '$lessonId'");?>
<form class="list-group">
<?php foreach ($result as $row):?>
    <?php echo "<p class='list-group-item '>ID задания: {$row['id_task']} <a href='open_task.php?task_id={$row['id_task']}'>перейти</a></p>"; ?>
<?php endforeach; ?>
</form>
</body>