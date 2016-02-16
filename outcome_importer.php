<?php

require_once('lib/canvasAPI.php');

$outcomeGroups = getOutcomeGroups(State::courseId());
//$result = listAssignments(Config::courseId());

var_dump($outcomeGroups[0]->id);

$result = createOutcomeSubGroup(State::courseId(), $outcomeGroups[0]->id, 'Strategie en Concept', 'Omschrijving');


var_dump($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Daily Snapshot</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="bower_components/normalize-css/normalize.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/dailysnapshot.css">
  <link rel="stylesheet" href="css/pong.css">

  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/anchorme.js"></script>
  <script src="js/moment.min.js"></script>
</head>
<body>
<header>
  <h1>Daily Snapshot</h1>
  <h2>You've moved mountains today!</h2>

  <div id="select-wrapper">
    <select id="student-filter" title="Student filter">
      <option>Show all</option>
    </select>
  </div>
</header>

<section id="student-blog" class="container">
  <div class="pong-loader">
    Loading…
  </div>
</section>
<script>


</script>


</body>
</html>
