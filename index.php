<?php
require_once('lib/config.php');
require_once('lib/state.php');

State::fromInitialPost();

if (!State::courseId() || !State::canvasDomain()) {
  header('HTTP/1.1 403 Forbidden');
  echo 'Connect to this LTI using your Canvas course.';
  exit();
}

if (!State::accessToken()) {
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
  <h1>Digital Dummy</h1>
  <h2>You've moved mountains today!</h2>

  <div id="select-wrapper">
    <select id="student-filter">
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
  // write the upload link
  console.log('Your easy upload link: ', '<?echo State::createUploadLink();?>');


  var canvasDomain = 'https://<?php echo State::canvasDomain(); ?>';

  $(function () {
    $.getJSON("submissions.php", function (resp) {
      var submissions = resp;

      var selectElement = document.getElementById('student-filter');

      var students = [];
      submissions.forEach(function (submission) {
        students.push(submission.user);
        var option = document.createElement('option');
        option.innerHTML = submission.user['sortable_name'];
        document.getElementById('student-filter').appendChild(option);
      });

      selectElement.addEventListener('change', function (e) {
        var selectedIndex = selectElement.selectedIndex;
        showSubmissionIndex(selectedIndex);
      });

      showSubmissionIndex(0);

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

        // filter out submissions that are not comments or attached files (should not occur in practice)
        articles = articles.filter(function (article) {
          return article.comment || article.attachments;
        });

        articles.sort(function (a, b) {
          // sort by submission date and let attachments go before comments (new to old)
          var aa = a.submitted_at ? new Date(a.submitted_at) : new Date(new Date(a.created_at).valueOf() - 20000),
            bb = b.submitted_at ? new Date(b.submitted_at) : new Date(new Date(b.created_at).valueOf() - 20000);
          return bb - aa;
        });

        var dateString = '';
        articles.forEach(function (attempt) {

          var date = attempt.submitted_at ? attempt.submitted_at : attempt.created_at;
          var newDateString = moment(date).format('dddd, MMMM Do');
          if (newDateString !== dateString) {
            dateString = newDateString;
            var header = document.createElement('h4');
            var dateHeader = document.createElement('time');
            dateHeader.setAttribute('datetime', date);
            dateHeader.innerHTML = dateString;
            header.appendChild(dateHeader);
            section.appendChild(header);
          }

          if (attempt.attachments) {
            var img, audio, video, mediaSource, iframe;
            attempt.attachments.forEach(function (attachment) {
              var article = document.createElement('article');
              article.classList.add('row');
              var contentType = attachment['content-type'];
              switch (contentType) {
                case 'image/gif':
                case 'image/png':
                  img = document.createElement('img');
                  img.src = attachment.url;
                  img.classList.add('blog-image');
                  article.appendChild(img);
                  break;
                case 'image/jpeg':
                case 'image/jpg':
                  img = document.createElement('img');
                  img.src = 'jpegProxy.php?path=' + encodeURIComponent(attachment.url);
                  img.classList.add('blog-image');
                  article.appendChild(img);
                  break;
                case 'audio/aac':
                case 'audio/mp4':
                case 'audio/mpeg':
                case 'audio/ogg':
                case 'audio/wav':
                case 'audio/webm':
                  audio = document.createElement('audio');
                  audio.setAttribute('controls', 'true');
                  audio.src = attachment.url;
                  audio.classList.add('blog-image');
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
                  video.setAttribute('controls', 'true');
                  video.src = attachment.url;
                  video.classList.add('blog-image');
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
                  iframe = document.createElement('iframe');
                  iframe.setAttribute('src', canvasDomain + attachment.preview_url);
                  iframe.classList.add('blog-embed');
                  article.appendChild(iframe);
                  break;
                default:
                  var icon = document.createElement('i');
                  icon.setAttribute('class', 'fa fa-2x file-icon');
                  switch (contentType) {
                    case 'application/zip':
                    case 'application/x-rar-compressed':
                      icon.classList.add('fa-file-zip-o');
                      break;
                    default:
                      icon.classList.add('fa-file-image-o');
                      break;
                  }
                  article.appendChild(icon);

                  var anchor = document.createElement('a');
                  anchor.innerHTML = attachment.filename;
                  anchor.href = attachment.url;
                  article.classList.add('file');
                  article.appendChild(anchor);
                  break;
              }
              section.appendChild(article);
            });
          }
          if (attempt.comment) {
            var article = document.createElement('article');
            article.classList.add('row');

            var avatar = document.createElement('img');
            avatar.src = attempt.author.avatar_image_url;
            avatar.classList.add('avatar');
            var author = document.createElement('em');
            author.innerHTML = attempt.author.display_name;
            author.classList.add('author');
            var paragraph = document.createElement('p');
            paragraph.innerHTML = anchorme.js(attempt.comment); // replaces links!

            article.classList.add('comment');
            article.appendChild(avatar);
            article.appendChild(author);
            article.appendChild(paragraph);
            section.appendChild(article);
          }

        });

        document.body.appendChild(section);
      }

    });
  });
</script>


</body>
</html>
