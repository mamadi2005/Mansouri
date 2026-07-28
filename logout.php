<?php
require_once 'includes/functions.php';
start_app_session();

unset(
    $_SESSION['is_logged'],
    $_SESSION['student_code'],
    $_SESSION['full_name'],
    $_SESSION['code_verified']
);

$logout_redirect = 'login.php';
require 'includes/logout_page.php';
