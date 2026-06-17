<?php
session_start();
session_unset();
session_destroy();

// Redirect to INDEX.html
header("Location: /parc_auto/INDEX.html");
exit();
?>
