<?php
require_once('lib/config.php');
require_once('lib/state.php');

State::fromInitialPost();

if (!State::courseId() || !State::canvasDomain()) {
  header('HTTP/1.1 403 Forbidden');
  echo 'Connect to this LTI using your Canvas course.';
  exit();
}

if (!State::refreshToken()) {
  // log in
  $uri = 'https://' . State::canvasDomain() . '/login/oauth2/auth?client_id=' . urlencode(Config::clientId(State::canvasDomain())) . '&response_type=code&redirect_uri=' . urlencode(Config::oauthCallbackURI()) . '&state=' . State::oauthState();
  header('Location: ' . $uri);
} else {
  // refresh the access token
  $data = array('client_id' => Config::clientId(State::canvasDomain()),
    'redirect_uri' => urlencode($uri),
    'client_secret' => rawurlencode(Config::clientSecret(State::canvasDomain())),
    'refresh_token' => State::refreshToken(),
    'grant_type' => 'refresh_token');

  $ch = curl_init('https://' . State::canvasDomain() . '/login/oauth2/token');
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);
  $result = json_decode($result, true);
  State::setAccessToken($result['access_token']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Digital Dummy</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="apple-mobile-web-app-capable" content="yes">

  <link rel="apple-touch-icon" sizes="144x144" href="img/apple-icon-144x144.png">
  <link rel="icon" type="image/png" href="img/digitaldummy.png">
  <link rel="stylesheet" href="bower_components/normalize-css/normalize.css">
  <link rel="stylesheet" href="bower_components/css-modal/build/modal.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
  <link rel="stylesheet" href="css/dailysnapshot.css">
  <link rel="stylesheet" href="css/pong.css">
  <link rel="stylesheet" href="bower_components/trumbowyg/dist/ui/trumbowyg.min.css">

  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.js"></script>
  <script src='bower_components/fastclick/lib/fastclick.js'></script>
  <script src="js/anchorme.js"></script>
  <script src="js/moment.min.js"></script>
  <script src="js/upload.js"></script>

  <style>
    .post-datapoint {
      position: absolute;
      background-color: rgba(0, 40, 0, 0.5);
      width: 0.5em;
      height: 1.5em;
    }

    .week-datapoint {
      background-color: rgba(0,0,0,0.05);
      position: absolute;
      font-weight: bold;
      width: 3.5em;
      height: 100%;
    }

    .student-datapoint {
      background-color: rgba(0,0,0,0.05);
      position: absolute;
      height: 1.5em;
      width: 100%;
      padding: 0 1em;
    }

    .week-datapoint:nth-child(2n),
    .student-datapoint:nth-child(2n) {
      background-color: rgba(0,0,0,0.1);;
    }

    .week-datapoint:hover,
    .student-datapoint:hover {
      background-color: rgba(144,238,144,1);
    }

    #student-blog {
      position: relative;
      overflow-x: scroll;
      margin: 2em auto;
    }
  </style>
</head>
<body>
<header>
  <h1>Digital Dummy</h1>
  <h2>You've moved mountains today!</h2>

  <div id="select-wrapper">
    <select id="student-filter" title="Student filter">
      <option>Show all</option>
    </select>
  </div>
</header>

<form enctype="multipart/form-data" id="upload-form">
  <div id="file-upload-wrapper">
    <i class="fa fa-upload"></i>
    <span>media</span>
    <input name="file" type="file" id="file-upload"/>
  </div>
  <a id="text-upload-wrapper" href="#modal-text">
    <i class="fa fa-plus-square"></i>
    <span>text</span>
  </a>
  <progress></progress>
</form>

<section class="modal--show" id="modal-text" tabindex="-1"
         role="dialog" aria-labelledby="modal-label" aria-hidden="true">

  <div class="modal-inner">
    <form id="text-upload-form">
      <div class="modal-content">
        <div id="submission-text" placeholder="Type your submission text here."></div>
        <button type="submit" id="text-submit">submit text</button>
      </div>
    </form>
  </div>

  <a href="#!" class="modal-close" title="Close this modal" data-close="Close"
     data-dismiss="modal">?</a>
</section>

<section id="student-blog" class="container">
  <div class="pong-loader">
    Loading…
  </div>
</section>
<script>
  // write the upload link
  console.log('Your easy upload link: ', '<?echo State::createUploadLink();?>');

  var canvasDomain = 'https://<?php echo State::canvasDomain(); ?>';

  function loadSubmissions() {
    $.getJSON("submissions.php", function (resp) {
      console.log(resp);
      var submissions = resp;
      var selectElement = document.getElementById('student-filter');

      var students = [],
        studentIds = [];
      selectElement.innerHTML = '';
      var firstOption = document.createElement('option');
      firstOption.innerHTML = 'Show all';
      selectElement.appendChild(firstOption);

      // sort the user names
      submissions.sort(function (a, b) {
        return b.submission_history.length - a.submission_history.length;
      });

      submissions.forEach(function (submission) {
        submission.user.attempts = submission.submission_history.length;
        students.push(submission.user);
        studentIds.push(submission.user.id);

        var option = document.createElement('option');
        option.innerHTML = submission.user['sortable_name'] + ' (' + submission.user.attempts + ')';
        selectElement.appendChild(option);
      });

      function getStudentNameForId(id) {
        return students.filter(function (user) {
          return user.id === id;
        })[0].sortable_name;
      }

      selectElement.addEventListener('change', function () {
        var selectedIndex = selectElement.selectedIndex;
        showSubmissionIndex(selectedIndex);
      });

      if (submissions.length == 1) {
        showSubmissionIndex(1);
        selectElement.selectedIndex = 1;
        selectElement.setAttribute('disabled', 'true');
        selectElement.parentNode.setAttribute('disabled', 'true');
      } else {
        showSubmissionIndex(0);
      }

      function showSubmissionIndex(selectedIndex) {
        var articles = [];
        if (selectedIndex == 0) {
          submissions.forEach(function (submission) {
            articles = articles.concat(submission.submission_comments).concat(submission.submission_history);
          });
        } else {
          articles = articles.concat(submissions[selectedIndex - 1].submission_comments).concat(submissions[selectedIndex - 1].submission_history);
        }
        showData(articles);
      }

      function showData(articles) {
        var section = document.getElementById('student-blog');
        section.innerHTML = '';

        // filter out submissions that are not comments, text entries, or attached files (should not occur in practice)
        articles = articles.filter(function (article) {
          return article.comment || article.body || article.attachments;
        });

        articles.sort(function (a, b) {
          // sort by submission date and let attachments go before comments (new to old)
          var aa = a.submitted_at ? new Date(a.submitted_at) : new Date(new Date(a.created_at).valueOf() - 20000),
            bb = b.submitted_at ? new Date(b.submitted_at) : new Date(new Date(b.created_at).valueOf() - 20000);
          return bb - aa;
        });

        var firstArticle = articles[0], lastArticle = articles[articles.length - 1];
        var startDate = firstArticle.submitted_at ? firstArticle.submitted_at : firstArticle.created_at;
        var endDate = lastArticle.submitted_at ? lastArticle.submitted_at : lastArticle.created_at;

        // adding students
        studentIds.forEach(function (id, index) {
          var dataPoint = document.createElement('div');
          dataPoint.classList.add('student-datapoint');
          dataPoint.innerHTML = getStudentNameForId(id);
          dataPoint.style.left = 2 + 'em';
          dataPoint.style.top = (2 + index * 1.5) + 'em';

          section.appendChild(dataPoint);
        });
        section.style.height = (studentIds.length * 1.5 + 2) + 'em';

        // adding weeks
        var weeks = Math.ceil((moment(startDate) - moment(endDate)) / (3600 * 24 * 7 * 1000));
        console.log(weeks);
        for (var week = 0; week < weeks; week++) {
          var dataPoint = document.createElement('div');
          dataPoint.classList.add('week-datapoint');
          dataPoint.innerHTML = 'wk ' + (week + 1);
          dataPoint.style.left = (20 + (weeks-week -1) * 7 * 0.5) + 'em';
          dataPoint.style.top = 0 + 'em';

          section.appendChild(dataPoint);
        }

        // adding data
        articles.forEach(function (attempt) {
          var dataPoint = document.createElement('div');
          dataPoint.classList.add('post-datapoint');
          // get student id
          var userId = attempt.user_id ? attempt.user_id : attempt.author_id;
          var userOffset = studentIds.indexOf(userId);
          // get date
          var date = attempt.submitted_at ? attempt.submitted_at : attempt.created_at;
          var dayDelay = Math.floor((moment(startDate) - moment(date)) / (3600 * 24 * 1000));

          dataPoint.style.left = (20 + dayDelay * 0.5) + 'em';
          dataPoint.style.top = (2 + userOffset * 1.5) + 'em';

          section.appendChild(dataPoint);

        });

        document.body.appendChild(section);
      }
    })
    ;
  }

  $(function () {
    loadSubmissions();
  });
</script>

<script>
  if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function () {
      FastClick.attach(document.body);
    }, false);
  }

  $("a").click(function (event) {
    event.preventDefault();
    window.location = $(this).attr("href");
  });
</script>

<script src="bower_components/css-modal/modal.js"></script>
<script src="bower_components/trumbowyg/dist/trumbowyg.min.js"></script>
<script>
  $('#submission-text').trumbowyg({
      mobile: true,
      tablet: true,
      fullscreenable: false,
      btns: ['viewHTML',
        '|', 'formatting',
        '|', 'btnGrp-design',
        '|', 'link',
        '|', 'btnGrp-justify',
        '|', 'btnGrp-lists']
    }
  );
</script>
</body>
</html>
