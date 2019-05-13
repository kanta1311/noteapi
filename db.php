<?php
$dbcon = new mysqli("localhost", "root", "", "ionic");

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

function login($username,$password){
    global $dbcon;
    $sql=$dbcon->query("SELECT * FROM `user` WHERE username='$username' and password='$password'");
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
