<?php
require __DIR__ . '/shared/auth.php';
$login = getUserLogin();
$is_author = false;
$profileId = -1;
$mysqli = new mysqli("db", "root", "password", "mydb");
    if($login != null){
        $result = $mysqli->query("SELECT id_profile, email, is_author, is_admin FROM profile WHERE email = '$login'");
        if (mysqli_num_rows($result) > 0) {
            
            $row = mysqli_fetch_assoc($result);
            $is_author = $row["is_author"] == 1? true : false;
            $is_admin = $row["is_admin"] == 1? true : false;
            $profileId = $row["id_profile"];
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

if(isset($_POST['taskJson']) && isset($_POST['lessonId'])){
    $taskJson = $mysqli->real_escape_string($_POST["taskJson"]);
    $lessonId = $mysqli->real_escape_string($_POST["lessonId"]);
    
    if($mysqli->query("INSERT INTO task (id_lesson, task_json) VALUES ('$lessonId', '$taskJson')")){
        echo "Данные успешно добавлены";
    } else{
        echo "Ошибка: " . $mysqli->error;
    }
}
?>
<html>
<head>
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="css/editor.css" type="text/css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
        <?php
        require __DIR__ . '/shared/menu.php';
        ?>
        <div style="margin-left:5%; margin-right:5%" class="font-weight-bold">Создавайте свои задания на соединение слова и его перевода</div>
        <input style="margin-left:5%; margin-right:5%" type="text" id="task-name-input" placeholder="Введите название задания">
        <textarea style="margin-left:5%; margin-right:5%"  name="tasktext" id="tasktext" cols="30" rows="10" placeholder="Введите текст, который будет отбражатся перед заданием"></textarea>
        <div class="editor-frame">
            <div class="match-pair-list" id="match-pair-list">
                <p class="task-placeholder" id="task-placeholder">Пар слов пока не добавлено</p>
            </div>
        </div>
            <div style="display: flex;flex-direction:row;align-items: center;">
                <button style="margin-left:5%; margin-right:5%" id="add-pair" class="btn-primary">Добавить пару слов</button>
            </div>
        <div style="display: flex;flex-direction:row;align-items: center;">
            <button  style="margin-left:5%; margin-right:5%" id="download" class="btn-dark">Сохранить</button>
        </div>
    <form action="create_task.php" method="post">
        <label style="margin-left:5%; margin-right:5%" for="lessonId">Выберите урок:</label>
        <select style="margin-left:5%; margin-right:5%"class="dropdown" id="lessonId" name="lessonId">
        <?php
        $result = $mysqli->query("SELECT * FROM lesson WHERE id_author = '$profileId'");
        if($is_admin){
            $result = $mysqli->query("SELECT * FROM lesson WHERE id_author = '$profileId'");
        }
        foreach ($result as $row){
            echo "<option value='{$row['id_lesson']}'>{$row['lesson_title']}</option>";
        }?>
        </select>
        <input type="text" id="taskJson" name="taskJson"  value="" style="visibility:hidden; position:absolute"></input>
        <input type="submit" id="save" style="visibility:hidden; position:absolute"></input>
    </form> 
    <script src="js/editor3.js" defer></script>
</body>
