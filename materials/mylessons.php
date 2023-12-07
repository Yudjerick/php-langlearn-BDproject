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
if($is_author == false){
    http_response_code(403);
    die('Forbidden');
    
}
if(checkBanned($login)){
    http_response_code(403);
    die('Banned');
}
if(isset($_POST['lessonName'])){
    $lessonName = $_POST['lessonName'];
    $mysqli->query("INSERT INTO lesson (lesson_title, lesson_order, id_author) VALUES ('$lessonName', 1,'$profileId')");
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
</head>
<body>
<?php
require __DIR__ . '/shared/menu.php';?>
<form style="margin-left:5%" action="lessons.php" method="post">
    <label for="emailReg">"Фильтр по email"</label>
    <input name="emailReg" type="text" placeholder="Фильтр по email автора">
    <label for="lessonNameReg">"Фильтр по названию урока"</label>
    <input name="lessonNameReg" type="text" placeholder="Фильтр по названию урока">
    <input type="submit" value="Отправить">
</form>
<?php
$result = $mysqli->query("SELECT lesson.id_lesson, lesson.lesson_title, lesson.is_hidden, profile.email FROM lesson,
 profile WHERE lesson.id_author = profile.id_profile AND profile.email LIKE '$emailReg' 
 AND lesson.lesson_title LIKE '$lessonName' AND lesson.id_author = '$profileId'");?>
<?php
if($is_admin){
    $result = $mysqli->query("SELECT lesson.id_lesson, lesson.lesson_title, lesson.is_hidden, profile.email FROM lesson,
 profile WHERE lesson.id_author = profile.id_profile AND profile.email LIKE '$emailReg' 
 AND lesson.lesson_title LIKE '$lessonName'");
 }
?>
<form>
<form>
<ul style="margin-left:5%; margin-right:5%" class="list-group">
<?php foreach ($result as $row):?>
    <div class="list-group-item">
    <?php echo "<p>Урок: {$row['lesson_title']} автор: {$row['email']} <a href='lesson_edit.php?lesson_id={$row['id_lesson']}'>перейти</a></p>";?>
    <?php if($row['is_hidden'] == 0):?>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
        </svg>
    <?php else:?>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
        <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486z"/>
        <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
        <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708"/>
        </svg>
    <?php endif;?>
    </div>
<?php endforeach; ?>
</ul>
</form>
<form style="margin-left:5%" action="mylessons.php" method="post">
    <input type="text" name="lessonName" required>
    <input type="submit" name="addLesson" value="Добавить урок" class="btn-primary">
</form>
</body>