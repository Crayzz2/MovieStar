<?php
    class Review{
        public $id;
        public $rating;
        public $review;
        public $id_user;
        public $id_movie;
    }

    interface ReviewDAOInterface{
        public function buildReview($data);
        public function create(Review $review);
        public function getMoviesReview($id);
        public function hasAlreadyReviewed($id, $idUser);
        public function getRatings($id);
    }