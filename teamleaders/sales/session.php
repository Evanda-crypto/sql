<?php
session_start();
if(!isset($_SESSION['Sales']) && $_SESSION['Sales']==false)
{
    header("location: ../techie/login.php");
}
?>
