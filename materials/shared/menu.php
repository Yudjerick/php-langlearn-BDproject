<?php
    $login = getUserLogin();
    $is_author = false;
    $is_admin = false;
    if($login != null){
        $mysqli = new mysqli("db", "root", "password", "mydb");
        $result = $mysqli->query("SELECT email, is_author, is_admin FROM profile WHERE email = '$login'");
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $is_author = $row["is_author"] == 1? true : false;
            $is_admin = $row["is_admin"] == 1? true : false;
        }
    }
?>
<div class="header">
    <nav>
        <form class="nav-container">
            <button class="nav-button" formaction="index.php">
                <p class="nav-button-text">Главная</p>
            </button>
            <button class="nav-button" formaction="lessons.php">
                <p class="nav-button-text">Уроки</p>
            </button>
            <?php if ($is_author): ?>
            <button class="nav-button" formaction="mylessons.php">
                <p class="nav-button-text">Мои уроки</p>
            </button>  
            <button class="nav-button" formaction="create_task.php">
                <p class="nav-button-text">Добавить задание</p>
            </button>  
            <?php endif; ?>
            <?php if ($is_admin): ?>
            <button class="nav-button" formaction="profiles_admin.php">
                <p class="nav-button-text">Пользователи</p>
            </button>  
            <?php endif; ?>
            <button class="nav-button" formaction="profile.php">
                <p class="nav-button-text">Профиль <?php if ($login != null ){echo "($login)";} ?></p>
            </button>
        </form>
    </nav>
    <style>
   

    </style>
</div>