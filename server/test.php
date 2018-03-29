<?php
session_start();
$testId = 1;
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
$questionList = "(";
foreach ($sampleSet as $sample) {
    $questionList = $questionList.$sampleSet[0].",";
}
echo "QuestionList: $questionList\n";
$questionList = substr($questionList, 0, -1).")";
echo "QuestionList: $questionList\n";
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
        ?><tr><td class="question" colspan="2"><?php echo $questionIndex.") ".$row[1]; ?></td></tr><?php
    }
    // Print Answer
}
?>
</table>
</body>
</html>
