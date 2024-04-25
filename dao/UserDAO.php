<?php
    require_once ("models/User.php");
    require_once ("models/Message.php");

    class UserDAO implements UserDAOInterface{

        private $conn;
        private $url;
        private $message;
        
        public function __construct(PDO $conn, $url){
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        public function buildUser($data){

            $user = new User();

            $user->id = $data["id"];
            $user->name = $data["name"];
            $user->lastname = $data["lastname"];
            $user->email = $data["email"];
            $user->password = $data["password"];
            $user->image = $data["image"];
            $user->bio = $data["bio"];
            $user->token = $data["token"];

            return $user;

        }
        public function create(User $user, $authUser = false){
            $ins = $this->conn->prepare("INSERT INTO users (name, lastname, email, password, token) VALUES (:PName,:PLastname,:PEmail,:PPassword,:PToken)");
            $ins->execute(array(":PName"=>$user->name, ":PLastname"=>$user->lastname, ":PEmail"=>$user->email, ":PPassword"=>$user->password, ":PToken"=>$user->token));
            
            if($authUser){
                $this->setTokenToSession($user->token);
            }

        }
        public function update(User $user, $redirect = true){

            $update = $this->conn->prepare("UPDATE users SET name = :PName, lastname = :PLastname, email = :PEmail, image = :PImage, bio = :PBio, token = :PToken WHERE id = :PId");
            $update->execute(array(":PName"=>$user->name, 
                                   ":PLastname"=>$user->lastname, 
                                   ":PEmail"=>$user->email, 
                                   ":PImage"=>$user->image, 
                                   ":PBio"=>$user->bio, 
                                   ":PToken"=>$user->token, 
                                   ":PId"=>$user->id));

            if($redirect){
                $this->message->setMessage("Dados atualizados com sucesso!", "success", "editprofile.php");
            }

        }
        public function verifyToken($protected = false){

            if(!empty($_SESSION['token'])){
                $token = $_SESSION['token'];
                $user = $this->findByToken($token);

                if($user){
                    return $user;
                } else if ($protected){
                    $this->message->setMessage("Faça a autenticação para acessar esta página", "error", "index.php");
                }
            } else if ($protected){
                $this->message->setMessage("Faça a autenticação para acessar esta página", "error", "index.php");
            }

        }
        public function setTokenToSession($token, $redirect = true){

            $_SESSION['token'] = $token;

            if($redirect){
                $this->message->setMessage("Seja bem vindo!", "success", "editprofile.php");
            }

        }
        public function authenticateUser($email, $password){

            $user = $this->findByEmail($email);

            if($user){

                if(password_verify($password, $user->password)){

                    $token = $user->generateToken();

                    $this->setTokenToSession($token, false);

                    $user->token = $token;

                    $this->update($user, false);

                    return true;

                } else {
                    return false;
                }

            } else {
                return false;
            }
        }
        public function findByEmail($email){

            if($email != ""){
                $sel = $this->conn->prepare("SELECT * FROM users WHERE email=:PEmail");
                $sel->execute(array(":PEmail" => $email));
                if($sel->rowCount()>0){
                    $data = $sel->fetch();
                    $user = $this->buildUser($data);

                    return $user;
                } else {
                    return false;
                }
                
            } else {
                return false;
            }

        }
        public function findById($id){
            if($id != ""){
                $sel = $this->conn->prepare("SELECT * FROM users WHERE id=:PId");
                $sel->execute(array(":PId" => $id));
                if($sel->rowCount()>0){
                    $data = $sel->fetch();
                    $user = $this->buildUser($data);

                    return $user;
                } else {
                    return false;
                }
                
            } else {
                return false;
            }

        }
        public function findByToken($token){
            if($token != ""){
                $sel = $this->conn->prepare("SELECT * FROM users WHERE token=:PToken");
                $sel->execute(array(":PToken" => $token));
                if($sel->rowCount()>0){
                    $data = $sel->fetch();
                    $user = $this->buildUser($data);

                    return $user;
                } else {
                    return false;
                }
                
            } else {
                return false;
            }
        }
        public function destroyToken(){
            $_SESSION['token'] = '';
            $this->message->setMessage("Você fez o logout com sucesso!", "success", "index.php");
        }
        public function changePassword(User $user){

            $update = $this->conn->prepare("UPDATE users SET password = :PPassword WHERE id = :PId");
            $update->execute(array(":PPassword"=>$user->password, ":PId"=>$user->id));

            $this->message->setMessage("Senha alterada com sucesso!", "success", "editprofile.php");

        }
    }