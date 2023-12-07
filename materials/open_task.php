<?php
require __DIR__ . '/shared/auth.php';
$login = getUserLogin();
$taskJson = "";
if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];
    $mysqli = new mysqli("db", "root", "password", "mydb");
    $result = $mysqli->query("SELECT task_json FROM task WHERE id_task = '$taskId'");
    $mysqli->close();
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } 
    $taskJson = $row["task_json"];
}
else {
    echo 'task_id not set';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tasks</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/tasks.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Russo+One&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    </head>
    <body>
    <?php
    require __DIR__ . '/shared/menu.php';
    ?>
        <var style="visibility:hidden; position:absolute" id="taskJson"><?php echo $taskJson;?></var>
        <var style="visibility:hidden; position:absolute" id="random-task-type">match</var>
        <main>
        <h1 class="info">Кликните по слову и по переводу, чтобы соединить их</h1>
        <div class="task-zone">
            <div class="task-container"></div>
        </div>
        </main>
        <script src="js/task.js" defer>
    </script>
    </body>
</html>