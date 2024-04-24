<?php

    require_once ("models/Movie.php");
    require_once ("models/Message.php");

    class MovieDAO implements MovieDAOInterface{
        
        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url){
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        public function buildMovie($data){
            $movie = new Movie();

            $movie->id = $data["id"];
            $movie->title = $data["title"];
            $movie->description = $data["description"];
            $movie->image = $data["image"];
            $movie->trailer = $data["trailer"];
            $movie->category = $data["category"];
            $movie->length = $data["length"];
            $movie->id_user = $data["id_user"];

            return $movie;

        }
        public function findAll(){

        }
        public function getLatestMovies(){

            $movies = [];

            $sel = $this->conn->query("SELECT * FROM movies ORDER BY id DESC");

            $sel->execute();

            if($sel->rowCount()>0){
                $moviesArray = $sel->fetchAll();

                foreach($moviesArray as $movie){
                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        }
        public function getMoviesByCategory($category){

            $movies = [];

            $sel = $this->conn->prepare("SELECT * FROM movies WHERE category = :PCategory ORDER BY id DESC");

            $sel->execute(array(":PCategory"=>$category));

            if($sel->rowCount()>0){
                $moviesArray = $sel->fetchAll();

                foreach($moviesArray as $movie){
                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;

        }
        public function getMoviesByUserId($id_user){

            $movies = [];

            $sel = $this->conn->prepare("SELECT * FROM movies WHERE id_user = :PId_user");

            $sel->execute(array(":PId_user"=>$id_user));

            if($sel->rowCount()>0){
                $moviesArray = $sel->fetchAll();

                foreach($moviesArray as $movie){
                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        }
        public function findById($id){

        }
        public function findByTitle($title){

        }
        public function create(Movie $movie){

            $ins = $this->conn->prepare("INSERT INTO movies (title, description, image, trailer, category, length, id_user) 
                                VALUES (:PTitle, :PDescription, :PImage, :PTrailer, :PCategory, :PLength, :PId_user)");
            $ins->execute(array(":PTitle"=>$movie->title, ":PDescription"=>$movie->description, ":PImage"=>$movie->image, ":PTrailer"=>$movie->trailer, 
                                ":PCategory"=>$movie->category, ":PLength"=>$movie->length, ":PId_user"=>$movie->id_user));

        }
        public function update(Movie $movie){

        }
        public function destroy($id){

        }

    }