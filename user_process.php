<?php

    require_once ("models/Message.php");
    require_once ("models/User.php");
    require_once ("dao/UserDAO.php");
    require_once ("globals.php");
    require_once ("db.php");

    $message = new Message($BASE_URL);
    $userDao = new UserDAO($conn, $BASE_URL);

    $type = filter_input(INPUT_POST, "type");

    if($type === "update"){

        $userData = $userDao->verifyToken();

        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $bio = filter_input(INPUT_POST, "bio");

        $user = new User();

        $userData->name = $name;
        $userData->lastname = $lastname;
        $userData->email = $email;
        $userData->bio = $bio;

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

                $imageName = $user->imageGenerateName();

                imagejpeg($imageFile, "./img/users/" . $imageName, 100);

                $userData->image = $imageName;

            } else {
                $message->setMessage("Tipo inválido de imagem, insira png, jpg ou jpeg", "error", "back");
            }

        }   


        $userDao->update($userData);

    } else if ($type === "changePassword"){

        $password = filter_input(INPUT_POST, "password");
        $confirmPassword = filter_input(INPUT_POST, "confirmPassword");
        $userData = $userDao->verifyToken();
        $id = $userData->id;

        if($password == $confirmPassword){

            $user = new User();

            $finalPassword = $user->generatePassword($password);

            $user->password = $finalPassword;
            $user->id = $id;

            $userDao->changePassword($user);

        } else {
            $message->setMessage("As senhas não são iguais", "error", "back");
        }

    } else {

        $message->setMessage("Informações inválidas", "error", "index.php");

    }