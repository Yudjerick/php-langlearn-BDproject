<?php
$lessonName = "";
$hidden = false;
if (isset($_GET['lesson_id'])) {
    $lessonId = $_GET['lesson_id'];
    $mysqli = new mysqli("db", "root", "password", "mydb");
    $result = $mysqli->query("SELECT * FROM lesson WHERE id_lesson = '$lessonId'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lessonName = $row['lesson_title'];
        $authorId = $row['id_author'];
        $hidden = $row['is_hidden'];
    }
    $mysqli->close();
}
else {
    echo 'lesson_id not set';
    http_response_code(403);
    die('Forbidden');
}
require __DIR__ . '/shared/auth.php';
$login = getUserLogin();
$is_author = false;
$is_admin = false;
$profileId = -1;
$mysqli = new mysqli("db", "root", "password", "mydb");
if($login != null){
    $result = $mysqli->query("SELECT email, is_author, id_profile, is_admin FROM profile WHERE email = '$login'");
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
if($is_author == false && $is_admin == false){
    http_response_code(403);
    die('Forbidden');
}
if($profileId != $authorId && $is_admin == false){
    http_response_code(403);
    die('Forbidden');
}
if(isset($_POST['lessonName'])){
    $lessonName = $_POST['lessonName'];
    if($mysqli->query("UPDATE lesson SET lesson_title = '$lessonName' WHERE id_lesson = '$lessonId'")){
    } else{
        echo "Ошибка: " . $mysqli->error;
    }
    
}
if(isset($_POST['del'])){
    if($mysqli->query("DELETE FROM lesson WHERE id_lesson = '$lessonId'")){
        header('Location: mylessons.php');
    } else{
        echo "Ошибка: " . $mysqli->error;
    }
}
if(isset($_POST['hide'])){
    $new_hidden = $hidden ? 0 : 1;
    if($mysqli->query("UPDATE lesson SET is_hidden = '$new_hidden' WHERE id_lesson = '$lessonId'")){
        $hidden = !$hidden;
    } else{
        echo "Ошибка: " . $mysqli->error;
    }
}
if(isset($_POST['del_task']) && isset($_GET['task_id'])){
    $taskId = $_GET['task_id'];
    if($mysqli->query("DELETE FROM task WHERE id_task = '$taskId'")){
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
require __DIR__ . '/shared/menu.php';
?>
<div style="margin-left: 5%" class="d-flex flex-row">
<?php echo '<form action="lesson_edit.php?lesson_id='. $lessonId . '" method="post">'?>
    <?php echo '<input type="text" name="lessonName" required value="' . $lessonName . '">' ?>
    <input type="submit" class="btn-primary" name="addLesson" value="Сохранить урок">
</form>
<?php echo '<form action="lesson_edit.php?lesson_id='. $lessonId . '" method="post">'?>
    <input type="text" name="del" value="del" style="visibility: hidden; position: absolute">
    <input type="submit" class="btn-danger" value="Удалить урок">
</form>
<?php echo '<form action="lesson_edit.php?lesson_id='. $lessonId . '" method="post">'?>
    <input type="text" name="hide" value="hide" style="visibility: hidden; position: absolute">
    <?php if(!$hidden):?>
        <input type="submit" class="btn-secondary" value="Скрыть урок">
    <?php else:?>
        <input type="submit" class="btn-outline-secondary" value="Показать урок">
    <?php endif;?>
</form>
</div>
<?php
$result = $mysqli->query("SELECT id_task FROM task WHERE id_lesson = '$lessonId'");

?>
<div class="list-group">
<?php foreach ($result as $row):?>
    <?php $taskId = $row['id_task'];
    echo '<form class="d-flex flex-row list-group-item" action="lesson_edit.php?lesson_id='. $lessonId . '&task_id='. $taskId . '" method="post">'?>
    <?php echo "<p style='margin-left:5%;margin-right:5%' class='font-weight-bold'>ID задания: {$row['id_task']} </p>"; ?>
    <input type="text" name="del_task" value="del_task" style="visibility: hidden; position: absolute">
    <input class="btn-danger" type="submit" value="Удалить задание">
    </form>
<?php endforeach; ?>
</div>