<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 4.1.2019.
 * Time: 19:19
 */

    require_once "dbconnect.php";
    require_once "functions.php";


    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];

    echo "hehe";
    $username = stripcslashes($username);
    $password = stripcslashes($password);
    $username = mysqli_real_escape_string($conn,$username);
    $password = mysqli_real_escape_string ($conn, $password);

    /*
     * 1. Provjeravam radi li se o donoru
     */

    $query ="select *from donor where username = '$username' and password = '$password'";
    $run = mysqli_query($conn, $query);
    $result = $run or die ("Failed to query database". mysqli_error($conn));
    $flag=0;
    $row = mysqli_fetch_array($result);

    if ($row['username'] == $username && $row['password'] == $password && ("" !== $username || "" !== $password) ) {
        $url = 'donor.php?OIB='.urlencode($row['OIB_donora']);
        $flag=1;
    }
    else{
        $info ="select *from admin where username = '$username' and password = '$password'";
        $run = mysqli_query($conn, $info);
        $result = $run or die ("Failed to query database". mysqli_error($conn));
        $row = mysqli_fetch_array($result);

        if ($row['username'] == $username && $row['password'] == $password && ("" !== $username || "" !== $password) ) {
            $url = "admin.php";
            $flag = 1;
        }
        else{
            $info ="select *from bolnica where  idbolnica = '$username' and password = '$password'";
            $run = mysqli_query($conn, $info);
            $result = $run or die ("Failed to query database". mysqli_error($conn));
            $row = mysqli_fetch_array($result);

            if ($row['idbolnica'] == $username && $row['password'] == $password && ("" !== $username || "" !== $password) ) {
                $url = 'bolnica.php?idbolnice='.urlencode($row['idbolnica']);
                $flag = 1;
            }
        }
    }
    if($flag) header("Location:$url");
    else echo "Pogresan unos!";

?>