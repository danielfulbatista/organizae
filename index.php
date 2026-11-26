<?php
require_once __DIR__ . '/config/bootstrap.php';
redirect(is_logged_in() ? '/organizae/dashboard.php' : '/organizae/auth/login.php');
