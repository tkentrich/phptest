<?php
session_start();
$imageFolder = "Images";
// SESSION VARIABLES THAT SHOULD BE SET:
//    TestId
//    QuestionList
//    TestName
//    InputName
// Create Connection
$PDO = new PDO('sqlite:../var/test.db');
$testId = $_SESSION['TestId'];
$questionList = $_SESSION['QuestionList'];
$testName = $_SESSION['TestName'];
$inputName = $_SESSION['InputName'];
?>
<html>
<head>
<title>
<?php echo $_SESSION['TestName']; ?>
</title>
<link rel="stylesheet" type="text/css" href="test.css" />
</head>
<body>
<div id="testResultDiv"> </div>
<?php

$testStmt = $PDO->prepare("SELECT Q.oid QuestionID, Q.Text QuestionText, A.oid AnswerID, A.Text AnswerText, Q.CorrectAnswer CorrectAnswer FROM Question Q INNER JOIN QuestionAnswer QA ON QA.Question = Q.oid INNER JOIN Answer A ON QA.Answer = A.oid WHERE Q.oid in $questionList ORDER BY QuestionID, AnswerID");
$testStmt->execute();
$result=$testStmt->fetchAll();
?>
<form action="result.php" method="post">
<table><tr><th>Test: <?php echo $_SESSION['TestName']; ?></th></tr>
<?php
$currentQuestion = false;
$questionIndex = 0;
$questionsCorrect = 0;
foreach ($result as $row) {
	if ($currentQuestion != $row[0]) {
		$correctAnswer = ($_REQUEST["Q".$row[0]] == $row[4]);
		if ($correctAnswer) {
			$questionsCorrect++;
		}
		// Print Question
		$questionIndex++;
		$currentQuestion = $row[0];
		?>
			<tr><td class="question"><?php echo $questionIndex.") ".$row[1]; ?></td></tr>
	<?php
	}
	// Print Answer
	$answer = $row[3];
	if (substr($answer, 0, 4) === 'img:') {
		$answer = "<img src=\"$imageFolder/".substr($answer, 4)."\" />";
	}
	$answerId = $row[2];
	$correctAnswer = ($row[2] == $row[4]);
	$thisAnswer = ($answerId == $_REQUEST["Q".$row[0]]);
	$ansClass = "answer";
	if ($correctAnswer) {
		$ansClass = "correctAnswer";
	} else if ($thisAnswer) {
		$ansClass = "incorrectAnswer";
	}
	?>
		<tr><td class="<?php echo $ansClass; ?>"><?php echo $answer; ?></td></tr>
	<?php
}
$testResult = 100 * $questionsCorrect / $questionIndex;
?>
<script>
document.getElementById("testResultDiv").innerHTML="<?php echo $_SESSION['InputName'].", you scored ".$testResult."%" ?>";
</script>
</table>
<input type="submit" value="Submit Test" />
</form>
</body>
</html>
