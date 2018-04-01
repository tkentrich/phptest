<?php
session_start();
?>
<html>
<head>
<title>
Test Taker
</title>
</head>
<body>
<form method="post" action="test.php">
<input type="hidden" name="testId" value="1" />
Enter your name: <input type="text" name="inputName" /><br />
<input type="submit" value="Start Test" />
</form>
</body>
</html>
