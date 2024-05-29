<?php
// session started if not then redirect back to index (login)
session_start();

// destroys the session
session_destroy();

// redirect admin to login (index)
header("Location: index.php");
exit();
?>
