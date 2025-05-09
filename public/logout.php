<?php
session_start();
session_unset();
session_destroy();

// Correct redirection to home page inside the 'blog' folder
header("Location: ../index.php");
exit();
