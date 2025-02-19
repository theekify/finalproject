<?php
session_start();
session_destroy();
header('Location: worker_login.php');
exit();
?>