<?php
function checkAuth(string $login, string $password): bool 
{
    $mysqli = new mysqli("db", "root", "password", "mydb");
    $result = $mysqli->query("SELECT email, password_hash FROM profile WHERE email = '$login'");
    $mysqli->close();
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if($row["password_hash"] == $password){
            
            return true;
        }
    } else {
        return false;
    }

    return false;
}

function checkBanned(string $login): bool
{
    $mysqli = new mysqli("db", "root", "password", "mydb");
    $result = $mysqli->query("SELECT is_banned FROM profile WHERE email = '$login'");
    $is_banned = mysqli_fetch_assoc($result)["is_banned"] == 1 ? true : false;
    $mysqli->close();
    return $is_banned;
}

function getUserLogin(): ?string
{
    $loginFromCookie = $_COOKIE['login'] ?? '';
    $passwordFromCookie = $_COOKIE['password'] ?? '';

    if (checkAuth($loginFromCookie, $passwordFromCookie)) {
        return $loginFromCookie;
    }

    return null;
}
