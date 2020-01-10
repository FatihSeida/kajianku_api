<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });

    $app->get('/about/', function (Request $request, Response $response, array $args) {
        // kirim pesan ke log
        $this->logger->info("ada orang yang mengakses '/about/'");
    
        // tampilkan pesan
        echo "ini adalah halaman about!";
        
    });

    $app->get("/kajian/", function (Request $request, Response $response){
        $sql = "SELECT * FROM kajian";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->get("/kajian/search/", function (Request $request, Response $response, $args){
        $keyword = $request->getQueryParam("keyword");
        $sql = "SELECT * FROM kajian WHERE nama_kajian LIKE '%$keyword%' OR pembicara_kk LIKE '%$keyword%' ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    //Rute POST /login
    $app->post("/login/", function (Request $request, Response $response){

        $login = $request->getParsedBody();

        $sql = "SELECT email, password FROM users WHERE email=:email AND password=:password";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":email" => $login["email"],
            ":password" => $login["password"],
        ];

        $stmt->execute($data);
        $result = $stmt->fetch();


        if($result)
            return $response->withJson(["status" => "success", "data" => $result], 200);
    
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    });

    //Rute POST /register
    $app->post("/register/", function (Request $request, Response $response){
        $register = $request->getParsedBody();

        $sql = "INSERT INTO users(nama, alamat, no_hp, email, password) VALUES (:nama,:alamat, :no_hp,:email,:password)";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":nama" => $register["nama"],
            ":alamat" => $register["alamat"],
            ":no_hp" => $register["no_hp"],
            ":email" => $register["email"],
            ":password" => $register["password"],
        ];


        if($stmt->execute($data))
            return $response->withJson(["status" => "success", "data" => "1"], 200);

        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    });
};
