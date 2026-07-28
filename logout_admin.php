<?php
require_once 'includes/functions.php';
start_app_session();

session_unset();
session_destroy();

$logout_redirect = 'login_admin.php';
require 'includes/logout_page.php';
