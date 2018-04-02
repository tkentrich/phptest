<?php
include("functions.php");
session_start();
$testId = 1;
$imageFolder = "Images";
// var_dump($_REQUEST);
if (isset($_REQUEST['testId'])) {
	$testId = $_REQUEST['testId'];
} else {
	echo "No test Id supplied\n";
}
$inputName = "NoName";
if (isset($_REQUEST['inputName'])) {
	$inputName = $_REQUEST['inputName'];
} else {
	echo "No Name supplied\n";
}
$_SESSION['TestId'] = $testId;
$_SESSION['InputName'] = $inputName;
// Create Connection
$PDO = new PDO('sqlite:../var/test.db');
$testStatement = $PDO->prepare("SELECT Name, Questions FROM Test WHERE oid=:id");
$testStatement->bindParam(':id', $testId, PDO::PARAM_INT);
$testStatement->execute();
$testInfo=$testStatement->fetchAll();
if (count($testInfo) !== 1) {
	die ("Query returned {$testInfo->rowCount} rows");
}
foreach ($testInfo as $infoRow) {
	echo "<!-- TEST INFO\n";
	echo " Name: ".$infoRow[0]."\n";
	echo " Questions: ".$infoRow[1]."\n";
	echo "-->\n";
	$testName = $infoRow[0];
	$questionCount = $infoRow[1];
	$_SESSION['TestName'] = $infoRow[0];
	$_SESSION['QuestionCount'] = $infoRow[1];
}
$sampleStmt = $PDO->prepare("SELECT oid FROM Question WHERE oid IN (SELECT Question FROM TestQuestion WHERE Test = :id) ORDER BY Random() LIMIT :count");
$sampleStmt->bindParam(':id', $testId, PDO::PARAM_INT);
$sampleStmt->bindParam(':count', $questionCount, PDO::PARAM_INT);
$sampleStmt->execute();
$sampleSet=$sampleStmt->fetchAll();
if (count($sampleSet) == 0) {
	$_SESSION['ErrorMessage'] = "Test $testID returned no questions";
	// header('Location: /error.php');
}
$questionList = "(";
foreach ($sampleSet as $sample) {
	$questionList = $questionList.$sample[0].",";
}
$questionList = substr($questionList, 0, -1).")";
$_SESSION['QuestionList'] = $questionList;
?>
<html>
<head>
<title>
<?php echo $_SESSION['TestName']; ?>
</title>
<link rel="stylesheet" type="text/css" href="test.css" />
</head>
<body>
<?php

$testStmt = $PDO->prepare("SELECT Q.oid QuestionID, Q.Text QuestionText, A.oid AnswerID, A.Text AnswerText FROM Question Q INNER JOIN QuestionAnswer QA ON QA.Question = Q.oid INNER JOIN Answer A ON QA.Answer = A.oid WHERE Q.oid in $questionList ORDER BY QuestionID, AnswerID");
$testStmt->execute();
$result=$testStmt->fetchAll();
?>
<form action="result.php" method="post">
<table><tr><th>Test: <?php echo $_SESSION['TestName']; ?></th></tr>
<?php
$currentQuestion = false;
$questionIndex = 0;
foreach ($result as $row) {
	if ($currentQuestion != $row[0]) {
		// Print Question
		$questionIndex++;
		$currentQuestion = $row[0];
		?>
		<tr><td class="question"><?php echo $questionIndex.") ".$row[1]; ?></td></tr>
		<?php
	}
	// Print Answer
	$answer = subs($row[3]);
	# if (substr($answer, 0, 4) === 'img:') {
		# $answer = "<img src=\"$imageFolder/".substr($answer, 4)."\" />";
	# }
	?>
		<tr><td class="answer"><input type="radio" class="radiobutton" name="<?php echo "Q".$row[0]; ?>" value="<?php echo $row[2]; ?>"><?php echo $answer; ?></input> </td></tr>
	<?php
}
?>
</table>
<input type="submit" value="Submit Test" />
</form>
</body>
</html>
