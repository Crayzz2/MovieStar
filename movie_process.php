<?php

    require_once ("models/Message.php");
    require_once ("models/Movie.php");
    require_once ("dao/UserDAO.php");
    require_once ("dao/MovieDAO.php");
    require_once ("globals.php");
    require_once ("db.php");

    $message = new Message($BASE_URL);

    $type = filter_input(INPUT_POST, "type");
    $userDao = new UserDAO($conn, $BASE_URL);
    $movieDao = new MovieDAO($conn, $BASE_URL);

    $userData = $userDao->verifyToken();

    if($type === "create"){

        $title = filter_input(INPUT_POST, "title");
        $description = filter_input(INPUT_POST, "description");
        $trailer = filter_input(INPUT_POST, "trailer");
        $category = filter_input(INPUT_POST, "category");
        $length = filter_input(INPUT_POST, "length");

        $movie = new Movie();

        if(!empty($title) && !empty($description) && !empty($category)){

            $movie->title = $title;
            $movie->description = $description;
            $movie->trailer = $trailer;
            $movie->category = $category;
            $movie->length = $length;
            $movie->id_user = $userData->id;

            if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){

                $image = $_FILES["image"];
                $imageTypes = ["image/jpeg", "image/png", "image/jpg"];
                $jpgArray = ["image/jpeg", "image/jpg"];

                if(in_array($image["type"], $imageTypes)){
                    if(in_array($image["type"], $jpgArray)){

                        $imageFile = imagecreatefromjpeg($image["tmp_name"]);

                    } else {
                        $imageFile = imagecreatefrompng($image["tmp_name"]);
                    }

                    $imageName = $movie->imageGenerateName();

                    imagejpeg($imageFile, "./img/movies/" . $imageName, 100);

                    $movie->image = $imageName;

                } else {
                    $message->setMessage("Tipo inválido de imagem, insira png, jpg ou jpeg", "error", "back");
                }

            }

            $movieDao->create($movie);

            $message->setMessage("Filme adicionado com sucesso!", "success", "index.php");

        } else {

            $message->setMessage("Adicione pelo menos título, descrição e categoria", "error", "back");

        }

    } else {

        $message->setMessage("Informações inválidas", "error", "index.php");

    }