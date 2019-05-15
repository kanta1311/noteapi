<?php
require 'vendor/autoload.php';
include('db.php');

$app = new \Slim\App;

$app->post('/login', function ($req, $res) {

    $key = $req->getParsedBody();

    $username = $key['username'];
    $password = $key['password'];

    if (login($username, $password)) {
        $data = [
            'error' => false,
            'message' => "Login Successful",
            "data" => login($username, $password)
        ];
    } else {
        $data = [
            'error' => true,
            'message' => "Wrong username or password",
        ];
    }

    return $res->withJson($data);
});

$app->post('/signup', function ($req, $res) {
    $key = $req->getParsedBody();
    $username = $key['username'];
    $password = $key['password'];

    if (empty($username)) {
        $data = [
            'error' => true,
            'message' => "Username is missing."
        ];
    } else if (empty($password)) {
        $data = [
            'error' => true,
            'message' => "password is missing."
        ];
    } else {
        if (addUser($username, $password)) {
            $data = [
                'error' => false,
                'message' => "Signup Successful"
            ];
        } else {
            $data = [
                'error' => false,
                'message' => "User Already Exist. Please login"
            ];
        }
    }

    return $res->withJson($data);
});

$app->post('/note/add', function ($req, $res) {
    $key = $req->getParsedBody();
    $title = $key['title'];
    $description = $key['description'];

    $token = $req->getHeaders()['HTTP_TOKEN'][0];

    if (empty($token)) {
        return $res->withJson([
            'error' => true,
            'message' => "Token is missing"
        ]);
    } else if (empty($title)) {
        return $res->withJson([
            'error' => true,
            'message' => "Title is missing"
        ]);
    } else {
        if (isUserExist($token)) {
            if (addNote($title, $description, $token)) {
                return $res->withJson([
                    'error' => false,
                    'message' => "Your Note added successfully",
                    'result'=>getAllNotes($token)
                ]);
            } else {
                return $res->withJson([
                    'error' => true,
                    'message' => "Somthing Wrong with us please try again"
                ]);
            }
        } else {
            return $res->withJson([
                'error' => true,
                'message' => "Token not valid"
            ], 401);
        }
    }
});
$app->get('/note/list', function ($req, $res) {
    $token = $req->getHeaders()['HTTP_TOKEN'][0];

    if (empty($token)) {
        return $res->withJson([
            'error' => true,
            'message' => "Token is missing"
        ]);
    } else {
        if (isUserExist($token)) {
            return $res->withJson([
                'error' => false,
                'message' => "All Notes",
                'result'=>getAllNotes($token)
            ]);
        } else {
            return $res->withJson([
                'error' => true,
                'message' => "Token not valid"
            ], 401);
        }
    }
});
$app->run();
