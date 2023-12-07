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
if (isset($_GET['lesson_id'])) {
    $lessonId = $_GET['lesson_id'];
}
else {
    echo 'lesson_id not set';
}
?>
<head>
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="css/login.css" type="text/css"/>
</head>
<body>
<?php
require __DIR__ . '/shared/menu.php';
$result = $mysqli->query("SELECT id_task FROM task WHERE id_lesson = '$lessonId'");?>
<form>
<?php foreach ($result as $row):?>
    <?php echo "<p>{$row['id_task']} <a href='tasks.php?task_id={$row['id_task']}'>перейти</a></p>"; ?>
<?php endforeach; ?>
</form>
</body>