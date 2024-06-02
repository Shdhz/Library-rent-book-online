<?php
    include_once 'include/database.php';
    $database = new database();
    $connect = $database->getConnection();

    if($connect){
        echo "udh bisa masuk bos";
    }else{
        echo "koneksi error";
    }
