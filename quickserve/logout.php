<?php

@include 'config.php';
// destroy session and go to index.php
session_start();
session_unset();
session_destroy();

header('location:index.php');

?>