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

$app->post('/signup',function($req,$res){
    $key = $req->getParsedBody();
    $username = $key['username'];
    $password = $key['password'];

    if(empty($username))
    {
        $data=[
            'error'=>true,
            'message'=>"Username is missing."
        ];
    }else if(empty($password)){
        $data=[
            'error'=>true,
            'message'=>"password is missing."
        ];
    }
    else{
        if(addUser($username,$password)){
            $data=[
                'error'=>false,
                'message'=>"Signup Successful"
            ];
        }
        else{
            $data=[
                'error'=>false,
                'message'=>"User Already Exist. Please login"
            ];
        }
    }

    return $res->withJson($data);
});
$app->run();
