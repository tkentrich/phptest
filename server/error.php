<?php
session_start();
?>
<html>
<head>
<title>Error</title>
</head>
<body>
<?php print "Error<br />\n{$_SESSION['ErrorMessage']}\n"; ?>
</body>
</html>
