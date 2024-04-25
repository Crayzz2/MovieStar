<?php

    require_once ("models/Review.php");
    require_once ("models/Message.php");
    require_once ("dao/UserDAO.php");

    class ReviewDAO implements ReviewDAOInterface{

        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url){
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }



        public function buildReview($data){

            $reviewObject = new Review();

            $reviewObject->id = $data["id"];
            $reviewObject->rating = $data["rating"];
            $reviewObject->review = $data["review"];
            $reviewObject->id_user = $data["id_user"];
            $reviewObject->id_movie = $data["id_movie"];

            return $reviewObject;

        }
        public function create(Review $review){

            $ins = $this->conn->prepare("INSERT INTO reviews (rating, review, id_user, id_movie) VALUES (:PRating, :PReview, :PId_user, :PId_movie)");
            $ins->execute(array(":PRating"=>$review->rating, ":PReview"=>$review->review, ":PId_user"=>$review->id_user, ":PId_movie"=>$review->id_movie));

            $this->message->setMessage("Crítica adicionada com sucesso", "success", "back");

        }
        public function getMoviesReview($id){

            $reviews = [];

            $sel = $this->conn->prepare("SELECT * FROM reviews WHERE id_movie = :PId_movie");
            $sel->execute(array(":PId_movie"=>$id));

            if($sel->rowCount()>0){

                $reviewData = $sel->fetchAll();

                $userDao = new UserDAO($this->conn, $this->url);

                foreach($reviewData as $review){

                    $reviewObject = $this->buildReview($review);

                    $user = $userDao->findById($reviewObject->id_user);

                    $reviewObject->user = $user;

                    $reviews[] = $reviewObject;
                }

            }

            return $reviews;

        }
        public function hasAlreadyReviewed($id, $id_user){

            $sel = $this->conn->prepare("SELECT * FROM reviews WHERE id_movie = :PId_movie AND id_user = :PId_user");
            $sel->execute(Array(":PId_movie"=>$id, ":PId_user"=>$id_user));

            if($sel->rowCount()>0){
                return true;
            } else {
                return false;
            }

        }
        public function getRatings($id){

            $sel = $this->conn->prepare("SELECT * FROM reviews WHERE id_movie = :PId_movie");
            $sel->execute(array(":PId_movie"=>$id));

            if($sel->rowCount()>0){
                
                $rating = 0;

                $reviews = $sel->fetchAll();

                foreach($reviews as $review){
                    $rating += $review["rating"];
                }
                $rating = $rating / count($reviews);

            } else {
                $rating = "Não avaliado";
            }
            return $rating;

        }
    }