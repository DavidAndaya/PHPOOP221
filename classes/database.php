<?php
class database
{
    function opencon()
    {
        return new PDO('mysql:host=localhost;dbname=phpoop_221','root','');
    }
 
    function check($username,$password){
        $con = $this->opencon();
        $query = "SELECT * FROM users WHERE user='".$username. "'&&pass='".$password."'";
        return $con->query($query)->fetch();
    }
 
    function signup($username,$password,$firstname,$lastname,$Birthday,$SEX){
        $con = $this->opencon();
        $query = $con->prepare("SELECT user FROM users WHERE user = ?");
        $query->execute([$username]);
        $existingUser = $query->fetch();
 
        if($existingUser){  
            return false;
    }
    return $con->prepare("INSERT INTO users (user,pass,firstname,lastname,birthday,sex) VALUES (?,?,?,?,?,?)")-> execute([$username,$password,$firstname,$lastname,$Birthday,$SEX]);
           
}
function signupUser($username,$password,$firstname,$lastname,$birthday,$sex){
    $con = $this->opencon();
    $query = $con->prepare("SELECT user FROM users WHERE user = ?");
    $query->execute([$username]);
    $existingUser = $query->fetch();

    if($existingUser){  
        return false;
}
    $con->prepare("INSERT INTO users (user,pass,firstname,lastname,birthday,sex) VALUES (?,?,?,?,?,?)")
        	->execute([$username, $password, $firstname, $lastname, $birthday,$sex]);
    return $con->lastInsertId();

}


function insertAddress($user_id, $street, $barangay, $city, $province){
    $con = $this->opencon();

    return $con->prepare("INSERT INTO user_address (user_id, user_street, user_barangay, user_city, user_province) VALUES (?,?,?,?,?)")
        ->execute([$user_id, $street, $barangay, $city, $province]);
   
}

function view(){
    $con = $this->opencon();
    return $con->query ("SELECT users.user_id, users.firstname, users.lastname, users.birthday, users.sex, users.pass, users.user, CONCAT(user_address.user_street, ' ', user_address.user_barangay,' ',user_address.user_city,' ',user_address.user_province) AS address FROM user_address INNER JOIN users ON user_address.user_id = users.user_id")->fetchAll();


}
function delete($id){
    try{
        $con = $this->opencon();
        $con->beginTransaction();
       $query= $con->prepare("DELETE FROM user_address WHERE user_id = ?");
        $query->execute([$id]);
        $query2= $con->prepare("DELETE FROM users WHERE user_id = ?");
        $query2->execute([$id]);
        $con->commit();
        return true;
    } catch (PDOException $e) {
$con->rollBack();
return false;
    }
}


    function viewdata($id){
        try{
            $con = $this->opencon();
            $query= $con->prepare("SELECT users.user_id, users.firstname, users.lastname, users.birthday, users.sex, users.user, users.pass, user_address.user_street, user_address.user_barangay, user_address.user_city, user_address.user_province FROM user_address INNER JOIN users ON users.user_id = user_address.user_id WHERE users.user_id=?");
            $query->execute([$id]);
            return $query->fetch();
        }catch (PDOException $e){
            return [];
        }
    }

    function updateUser($id,$firstname,$lastname,$birthday,$sex,$user,$password){
        try{
            $con = $this->opencon();
            $query= $con->prepare("UPDATE users SET firstname=?, lastname=?, birthday=?, sex=?, user=?, pass=? WHERE user_id=?");
            return  $query->execute([$firstname,$lastname,$birthday,$sex,$user,$password,$id]);
        }catch (PDOException $e){
        return false;
    }
}
        function updateUserAddress($id, $user_street, $user_barangay, $user_city, $user_province){
            try{
                $con = $this->opencon();
                $query= $con->prepare("UPDATE user_address SET user_street=?, user_barangay=?, user_city=?, user_province=? WHERE user_id=?");
                return  $query->execute([$user_street, $user_barangay, $user_city, $user_province, $id]);
            }catch (PDOException $e){
            return false;
        }
    }
}