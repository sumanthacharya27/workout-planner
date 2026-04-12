<?php
// ============================================
// LOGOUT PAGE
// ============================================
// logout.php
// Handles session termination

session_start();
session_destroy();
header('Location: login.php');
exit;
