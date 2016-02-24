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
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="apple-mobile-web-app-capable" content="yes">

  <link rel="apple-touch-icon" sizes="144x144" href="img/apple-icon-144x144.png">
  <link rel="icon" type="image/png" href="img/digitaldummy.png">
  <link rel="stylesheet" href="bower_components/normalize-css/normalize.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
  <link rel="stylesheet" href="css/dailysnapshot.css">
  <link rel="stylesheet" href="css/pong.css">

  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
  <script src="js/anchorme.js"></script>
  <script src="js/moment.min.js"></script>
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
    <input name="file" type="file" id="file-upload">
  </div>
  <progress></progress>
</form>

<script>

  $(':file').change(function () {
    var file = this.files[0];

    var formData = new FormData($('form')[0]);
    $.ajax({
      url: 'submission.php',  //Server script to process data
      type: 'POST',
      xhr: function () {  // Custom XMLHttpRequest
        var myXhr = $.ajaxSettings.xhr();
        if (myXhr.upload) { // Check if upload property exists
          myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
        }
        return myXhr;
      },
      //Ajax events
      beforeSend: beforeSendHandler,
      success: completeHandler,
      error: errorHandler,
      // Form data
      data: formData,
      //Options to tell jQuery not to process data or worry about content-type.
      cache: false,
      contentType: false,
      processData: false
    });
  });

  function beforeSendHandler() {
    document.body.classList.add('uploading');
  }

  function completeHandler() {
    // show new results
    document.body.classList.remove('uploading');
    loadSubmissions();
  }

  function errorHandler(e) {
    document.body.classList.remove('uploading');
    toastr.error('Upload error:', e);
  }

  function progressHandlingFunction(e) {
    if (e.lengthComputable) {
      $('progress').attr({value: e.loaded, max: e.total});
    }
  }
</script>

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
      var submissions = resp;

      var selectElement = document.getElementById('student-filter');

      var students = [];
      selectElement.innerHTML = '';
      var firstOption = document.createElement('option');
      firstOption.innerHTML = 'Show all';
      selectElement.appendChild(firstOption);

      submissions.forEach(function (submission) {
        students.push(submission.user);
        var option = document.createElement('option');
        option.innerHTML = submission.user['sortable_name'];
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

        var hiddenArticles = articles;
        appendFrom(hiddenArticles, 10);

        function appendFrom(attempts, pageLength) {
          for (var i = 0; i < pageLength; i++) {
            var attempt = attempts.shift();
            if (!attempt) {
              break;
            }
            append(attempt);
          }
        }

        function append(attempt) {
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

              // add metaheader
              var metaheader = document.createElement('header');
              var time = document.createElement('time');
              time.innerHTML = moment(date).format('LT');
              metaheader.appendChild(time);
              if (selectElement.selectedIndex === 0) {
                var author = document.createElement('span');
                author.classList.add('row-author');
                author.innerHTML = getStudentNameForId(attempt.user_id);
                metaheader.appendChild(author);
              }
              article.appendChild(metaheader);

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
                  console.log('unknown mime:', contentType);
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

            // add metaheader TODO REMOVE CODE DUPLICATE except for author id
            var metaheader = document.createElement('header');
            var time = document.createElement('time');
            time.innerHTML = moment(date).format('LT');
            metaheader.appendChild(time);
            if (selectElement.selectedIndex === 0) {
              var author = document.createElement('span');
              author.classList.add('row-author');
              author.innerHTML = getStudentNameForId(attempt.author_id);
              metaheader.appendChild(author);
            }
            article.appendChild(metaheader);

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
        }

        window.onscroll = respondToScroll;

        function respondToScroll() {
          if (window.scrollY + 1.5 * window.innerHeight - document.body.clientHeight > 0) {
            appendFrom(hiddenArticles, 10);
          }
        }

        document.body.appendChild(section);
      }
    });
  }

  $(function () {
    loadSubmissions();
  });
</script>

</body>
</html>
