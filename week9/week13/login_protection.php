<?php
session_start();
if(!isset($_SESSION['login'])){ //if login in session is not set
    header("Location: login.html");
}?>
