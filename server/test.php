<?php
session_start();
$testId = 1;
var_dump($_REQUEST);
if (isset($_REQUEST['testId'])) {
	$testId = $_REQUEST['testId'];
} else {
	echo "No test Id supplied\n";
}
// Create Connection
$PDO = new PDO('sqlite:../var/test.db');
$testInfo = $PDO->query("SELECT Name, Questions FROM Test WHERE oid = $testId");
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
$sampleSet = $PDO->query("SELECT oid FROM Question WHERE oid IN (SELECT Question FROM TestQuestion WHERE Test = $testId) ORDER BY Random() LIMIT $questionCount");
echo "Row Count:".($sampleSet->rowCount())."\n";
if ($sampleSet->rowCount() == 0) {
	$_SESSION['ErrorMessage'] = "Test $testID returned no questions";
	header('Location: /error.php');
}
$questionList = "(";
foreach ($sampleSet as $sample) {
	$questionList = $questionList.$sampleSet[0].",";
}
$questionList = substr($questionList, 0, -1).")";
?>
<html>
<head>
<title>
<?php echo $_SESSION['TestName']; ?>
</title>
</head>
<body>
<?php
$sql = "SELECT Q.oid QuestionID, Q.Text QuestionText, A.oid AnswerID, A.Text AnswerText FROM Question Q INNER JOIN QuestionAnswerQA ON QA.Question = Q.oid INNER JOIN Answer A ON QA.Answer = A.oid WHERE Q.oid in $questionList";
echo "SQL: $sql\n";
$result = $PDO->query($sql);
?>
<table><tr><th>Test: <?php echo $_SESSION['TestName']; ?></th></tr>
<?php
$currentQuestion = false;
$questionIndex = 0;
foreach ($result as $row) {
	echo "New Row\n";
	if ($currentQuestion != $row[0]) {
		// Print Question
		$questionIndex++;
		$currentQuestion = $row[0];
		?><tr><td class="question" colspan="2"><?php echo $questionIndex.") ".$row[1]; ?></td></tr><?php
	}
	// Print Answer
	echo "<tr><td></td><td class=\"answer\"><input type=\"checkbox\" name=\"Q$row[0]\" value=\"\">$row[3]</input> </td></tr>\n";
}
?>
</table>
</body>
</html>
