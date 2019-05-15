<?php
$dbcon = new mysqli("localhost", "root", "123456", "ionic");

if ($dbcon->connect_error) {
    die("Error found" . $dbcon->connect_error);
}

function addUser($username, $userpass)
{
    global $dbcon;

    $stmt = $dbcon->prepare("INSERT INTO `user` (`id`, `username`, `password`, `token`) VALUES (NULL, ?, ?, ?)");

    $stmt->bind_param('sss', $username, $userpass, getToken());

    return $stmt->execute();
}
function getToken()
{
    return bin2hex(openssl_random_pseudo_bytes(16));
}
function getUserAll()
{
    global $dbcon;
    $sql = $dbcon->query('SELECT * FROM `user`');
    if ($sql->num_rows > 0) {
        while ($row = $sql->fetch_assoc()) {
            $data[] = [
                'user_id' => $row['id'],
                'username' => $row['username'],
                'token' => $row['token'],
            ];
        }
        return $data;
    } else {
        return null;
    }
}

function isUserExist($token)
{
    global $dbcon;
    $sql = $dbcon->query("SELECT * FROM `user` WHERE `token`='$token'");
    return $sql->num_rows > 0;
}

function login($username, $password)
{
    global $dbcon;
    $sql = $dbcon->query("SELECT * FROM `user` WHERE username='$username' and password='$password'");
    if ($sql->num_rows > 0) {
        while ($row = $sql->fetch_assoc()) {
            $data = [
                'user_id' => $row['id'],
                'username' => $row['username'],
                'token' => $row['token'],
            ];
        }
        return $data;
    } else {
        return null;
    }
}

function getUserDetailsByToken($token)
{
    #
    global $dbcon;
    $sql = $dbcon->query("SELECT * FROM `user` WHERE token='$token'");
    if ($sql->num_rows > 0) {
        while ($row = $sql->fetch_assoc()) {
            $data = [
                'user_id' => $row['id'],
                'username' => $row['username'],
                'token' => $row['token'],
            ];
        }
        return $data;
    } else {
        return null;
    }
}

function getUserId($token)
{
    return getUserDetailsByToken($token)['user_id'];
}

function addNote($title, $description, $token)
{
    global $dbcon;
    $sql = $dbcon->prepare("INSERT INTO `note` (`id`, `title`, `description`, `user_id`) VALUES (NULL, ?, ?, ?)");
    $user_id = getUserId($token);
    $sql->bind_param('ssi', $title, $description, $user_id);
    return $sql->execute();
}

function getAllNotes($token)
{
    global $dbcon;
    $user_id = getUserId($token);
    $sql = $dbcon->query("SELECT * FROM `note` WHERE user_id=$user_id");
    while ($row = $sql->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
        ];
    }
    return  $data;
}
