<?php
require_once('lib/config.php');
require_once('lib/state.php');

if (!State::courseId() || !State::canvasDomain()) {
  header('HTTP/1.1 403 Forbidden');
  echo 'Connect to this LTI using your Canvas course.';
  exit();
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
  <link rel="stylesheet" href="bower_components/toastr/toastr.min.css">
  <link rel="stylesheet" href="css/dailysnapshot.css">
  <link rel="stylesheet" href="css/pong.css">
  <link rel="stylesheet" href="bower_components/trumbowyg/dist/ui/trumbowyg.min.css">

  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/anchorme.js"></script>
  <script src="js/moment.min.js"></script>

  <style>
    article {
      background-color: black;
      position: absolute;
      height: 100%;
      width: 100%;
      left: 0;
      top: 0;
    }

    article header {
      opacity: 1 !important;
    }

    article .image-post {
      background-position: center center;
      background-repeat: no-repeat;
      background-size: contain;
      height: 100%;
      width: 100%;
    }
  </style>
</head>
<body>
<div id="show-container">

</div>
<script>

  var canvasDomain = 'https://<?php echo State::canvasDomain(); ?>';

  function loadSubmissions() {
    $.getJSON("submissions.php", function (resp) {
        var submissions = resp, students = [];

        submissions.forEach(function (submission) {
          students.push(submission.user);
        });

        function getStudentNameForId(id) {
          return students.filter(function (user) {
            return user.id === id;
          })[0].sortable_name;
        }

        showSubmissionIndex(0);

        function showSubmissionIndex(selectedIndex) {
          var articles = [];
          submissions.forEach(function (submission) {
            articles = articles.concat(submission.submission_comments).concat(submission.submission_history);
          });

          showNextItem(0);

          function showNextItem(delay) {
            setTimeout(function () {
              if (showData([articles[Math.floor(Math.random() * articles.length)]])){
                showNextItem(15000);
              } else {
                showNextItem(0);
              }
            }, delay);
          }
        }

        function showData(article) {
          var section = document.getElementById('show-container');

          var attempt = article[0];
          var date = attempt.submitted_at ? attempt.submitted_at : attempt.created_at;
          if (attempt.attachments) {

            var img, audio, video, mediaSource, iframe;
            var attachment = attempt.attachments[0];
            var article = document.createElement('article');
            article.classList.add('row');

            // add metaheader
            var metaheader = document.createElement('header');
            var time = document.createElement('time');
            time.innerHTML = moment(date).format('LT');
            metaheader.appendChild(time);
            var author = document.createElement('span');
            author.classList.add('row-author');
            author.innerHTML = getStudentNameForId(attempt.user_id);
            metaheader.appendChild(author);
            article.appendChild(metaheader);

            var contentType = attachment['content-type'];
            switch (contentType) {
              case 'image/gif':
              case 'image/png':
              case 'image/jpeg':
              case 'image/jpg':
                img = document.createElement('div');
                img.classList.add('image-post');
                img.style.backgroundImage = 'url(' + attachment.url + ')';
                console.log(attachment);
                article.appendChild(img);
                break;
              case 'audio/aac':
              case 'audio/mp4':
              case 'audio/mpeg':
              case 'audio/ogg':
              case 'audio/wav':
              case 'audio/webm':
                audio = document.createElement('audio');
                audio.src = attachment.url;
                audio.classList.add('blog-image');
                audio.setAttribute('autoplay','true');
                mediaSource = document.createElement('source');
                mediaSource.setAttribute('src', attachment.url);
                mediaSource.setAttribute('type', contentType);
                audio.appendChild(mediaSource);
                article.appendChild(audio);
                break;
              case 'video/quicktime':
              case 'video/mp4':
              case 'video/ogg':
              case 'video/webm':
                video = document.createElement('video');
                video.src = attachment.url;
                video.classList.add('blog-image');
                video.setAttribute('autoplay','true');
                mediaSource = document.createElement('source');
                mediaSource.setAttribute('src', attachment.url);
                mediaSource.setAttribute('type', contentType);
                video.appendChild(mediaSource);
                article.appendChild(video);
                break;
              case 'application/pdf':
              case 'application/msword':
              case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
              case 'application/vnd.ms-powerpoint':
              case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
              case 'application/vnd.ms-excel':
              case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
              case 'text/plain':
              case 'application/x-python':
              case 'text/x-python':
              case 'text/javascript':
              case 'application/x-javascript':
              case 'text/xml':
              case 'application/xml':
              case 'text/css':
              case 'text/x-markdown':
              case 'text/x-script.perl':
              case 'text/x-c':
              case 'text/x-m':
              case 'application/json':
              default:
                return false;
            }
            section.innerHTML = '';
            section.appendChild(article);
            return true;
          }
        }
      }
    );
  }

  $(function () {
    loadSubmissions();
  });
</script>

</body>
</html>
