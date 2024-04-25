<?php

    require_once ("models/Message.php");
    require_once ("models/Movie.php");
    require_once ("models/Review.php");
    require_once ("dao/UserDAO.php");
    require_once ("dao/MovieDAO.php");
    require_once ("dao/ReviewDAO.php");
    require_once ("globals.php");
    require_once ("db.php");

    $message = new Message($BASE_URL);

    $type = filter_input(INPUT_POST, "type");
    $userDao = new UserDAO($conn, $BASE_URL);
    $movieDao = new MovieDAO($conn, $BASE_URL);
    $reviewDao = new ReviewDAO($conn, $BASE_URL);

    $userData = $userDao->verifyToken();

    if($type === "create"){
        
        $rating = filter_input(INPUT_POST, "rating");
        $review = filter_input(INPUT_POST, "review");
        $id_movie = filter_input(INPUT_POST, "id_movie");

        $reviewObject = new Review();

        $movieData = $movieDao->findById($id_movie);

        if($movieData){

            if(!empty($rating) && !empty($review) && !empty($id_movie)){

                $reviewObject->rating = $rating;
                $reviewObject->review = $review;
                $reviewObject->id_movie = $id_movie;
                $reviewObject->id_user = $userData->id;

                $reviewDao->create($reviewObject);

            } else {

                $message->setMessage("Você precisa inserir a nota e o comentario", "error", "back");

            }

        } else {

            $message->setMessage("Informações inválidas", "error", "index.php");

        }

    } else {

        $message->setMessage("Informações inválidas", "error", "index.php");

    }