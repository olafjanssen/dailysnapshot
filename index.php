<?php
require_once('lib/logging.php');
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
//    'redirect_uri' => urlencode($uri),
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
  <link rel="stylesheet" href="bower_components/toastr/toastr.min.css">
  <link rel="stylesheet" href="css/dailysnapshot.css">
  <link rel="stylesheet" href="css/pong.css">
  <link rel="stylesheet" href="bower_components/trumbowyg/dist/ui/trumbowyg.min.css">

  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="bower_components/toastr/toastr.min.js"></script>
  <script src='bower_components/fastclick/lib/fastclick.js'></script>
  <script src="js/anchorme.js"></script>
  <script src="js/moment.min.js"></script>
  <script src="js/upload.js?v1.1"></script>
  <script>
    <?php
    //ensure blacklist exists
    file_put_contents('service/blacklists/' . State::getKey(), '', FILE_APPEND | LOCK_EX);
    ?>
    // include object blacklist
    var blacklist = <?php echo json_encode(file('service/blacklists/' . State::getKey(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)); ?>;

  </script>
</head>
<body>
<header>
  <h1>Digital Dummy</h1>
  <h2>You've moved mountains today!</h2>

  <a id="mobile-upload-link" target="blank" href="<? echo State::createUploadLink(); ?>">easy mobile version <i
      class="fa fa-mobile" aria-hidden="true"></i></a>

  <div id="select-wrapper" style="display:none;">
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

<section id="comment-box" style="display: none;" class="container">
  <form id="comment-upload-form">
    <div id="comment-text" placeholder="Type your comment here."></div>
    <button type="submit" id="comment-submit">post</button>
  </form>
</section>

<section id="student-blog" class="container">
  <div class="pong-loader">
    Loading…
  </div>
</section>

<script>
  console.log('clearing cache');
  localStorage.clear();
  // write the upload link
  console.log('Your easy upload link: ', '<?echo State::createUploadLink();?>');

  var canvasDomain = 'https://<?php echo State::canvasDomain(); ?>',
    storeId = '<?php echo base64_encode(State::courseId() . ',' . State::assignmentId() . ',' . State::canvasDomain()); ?>',
    currentUserId = null,
    students = null;

  function getStudentNameForId(id) {
    return students.filter(function (user) {
      return user.id === id;
    })[0].sortable_name;
  }

  function loadStudents() {
    console.log('loading students');
    students = JSON.parse(localStorage.getItem(storeId + '/students'));
    if (students) {
      showStudents();
    }

    var selectElement = document.getElementById('student-filter');
    selectElement.addEventListener('change', function () {
      var selectedIndex = selectElement.selectedIndex;
      if (selectedIndex === 0) {
        currentUserId = null;
        students.forEach(function (student) {
          loadSubmission(student.id);
        });
      } else {
        currentUserId = students[selectedIndex - 1].id;
        loadSubmission(currentUserId);
      }
    });

    console.log('fetching students');
    $.getJSON("service/students.php", function (resp) {
      console.log('students received');
      var cached = localStorage.getItem(storeId + '/students');
      if (!cached) {
        cached = '';
      }
      localStorage.setItem(storeId + '/students', JSON.stringify(resp));

      if (cached.localeCompare(JSON.stringify(resp)) != 0) {
        students = resp;
        showStudents();
      }
    });

    function showStudents() {
      console.log('showing students', students);
      // skip if students list is empty
      if (students.length == 0) {
        var section = document.getElementById('student-blog');
        section.innerHTML = '<h2>No submissions yet...</h2>';
      }

      var selectElement = document.getElementById('student-filter'),
        commentElement = document.getElementById('comment-box'),
        uploadForm = document.getElementById('upload-form');

      selectElement.parentNode.style.display = "block";
      selectElement.innerHTML = '';
      var firstOption = document.createElement('option');
      firstOption.innerHTML = 'Show all';
      selectElement.appendChild(firstOption);

      // sort the user names
      students.sort(function (a, b) {
        return a.sortable_name.localeCompare(b.sortable_name);
      });

      students.forEach(function (student) {
        var option = document.createElement('option');
        option.innerHTML = student.sortable_name;
        selectElement.appendChild(option);
      });

      selectElement.selectedIndex = 1;
      if (students.length > 0 && currentUserId != students[0].id) {
        currentUserId = students[0].id;
        loadSubmission(currentUserId);
      }

      // this is a not-so-nice test to see if the user is a student or a teacher
      if (students.length < 2) {
        document.body.classList.add('isstudent');
        document.body.classList.remove('isteacher');
        selectElement.setAttribute('disabled', 'true');
        selectElement.parentNode.setAttribute('disabled', 'true');
        uploadForm.style.display = 'block';
      } else {
        document.body.classList.remove('isstudent');
        document.body.classList.add('isteacher');
        // teachers cannot upload (sad if you're a teacher AND a student TODO)
        uploadForm.style.display = 'none';
      }
    }
  }

  function loadSubmission(id) {
    console.log('getting', id);
    showSubmissions();

    $.get("service/singlesubmission.php", {user: id},
      function (resp) {
        console.log('receieved', id);
        var submission = resp;
        if (submission.length === 1) {
          localStorage.setItem(storeId + '/submission/' + id, JSON.stringify(submission[0]));
        }
        showSubmissions();
      });

    function showSubmissions() {
      console.log('showing', currentUserId);
      var submissions = [];
      if (currentUserId) {
        var submission = JSON.parse(localStorage.getItem(storeId + '/submission/' + currentUserId));
        if (submission) {
          submissions.push(submission);
        }
      } else {
        students.forEach(function (student) {
          var submission = JSON.parse(localStorage.getItem(storeId + '/submission/' + student.id));
          if (submission) {
            submissions.push(submission);
          }
        });
      }

      var commentElement = document.getElementById('comment-box');

      var articles = [];
      if (!currentUserId) {
        submissions.forEach(function (submission) {
          articles = articles.concat(submission.submission_history);
        });
        commentElement.style.display = 'none';
      } else if (submissions.length > 0) {
        articles = articles.concat(submissions[0].submission_comments).concat(submissions[0].submission_history);

        if (students.length > 1) {
          commentElement.style.display = 'block';
        } else {
          commentElement.style.display = 'none';
        }
      }

      showData(articles);

      function showData(articles) {
        console.log('showing articles', articles);
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

                // tag blacklisted items
                var blacklistId = 'aid/' + attachment.id;
                console.log(blacklistId);
                if (blacklist.indexOf(blacklistId) > -1) {
                  article.classList.add('blacklisted');
                }

                // add metaheader
                var metaheader = document.createElement('header');
                var time = document.createElement('time');
                time.innerHTML = moment(date).format('LT');
                metaheader.appendChild(time);
                if (!currentUserId) {
                  var author = document.createElement('span');
                  author.classList.add('row-author');
                  author.innerHTML = getStudentNameForId(attempt.user_id);
                  metaheader.appendChild(author);
                }
                var deleteButton = document.createElement('i');
                deleteButton.setAttribute('class', 'fa fa-times');
                deleteButton.classList.add( (blacklist.indexOf(blacklistId) > -1)? 'undeleteButton' : 'deleteButton' );
                deleteButton.setAttribute('title', 'Hide or unhide your post here for others.');
                metaheader.appendChild(deleteButton);
                article.appendChild(metaheader);

                deleteButton.addEventListener('click', function (e) {
                  if (deleteButton.classList.contains('deleteButton')) {
                    deleteButton.classList.remove('deleteButton');
                    deleteButton.classList.add('undeleteButton');
                    article.classList.add('blacklisted');
                    $.post('service/blacklists/add.php', {id: blacklistId})
                  } else {
                    deleteButton.classList.add('deleteButton');
                    deleteButton.classList.remove('undeleteButton');
                    article.classList.remove('blacklisted');
                    $.post('service/blacklists/remove.php', {id: blacklistId})
                  }
                });

                var contentType = attachment['content-type'];
                switch (contentType) {
                  case 'image/gif':
                  case 'image/png':
                  case 'image/jpeg':
                  case 'image/jpg':
                    img = document.createElement('img');
                    img.src = attachment.url;
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
                    if (window.self !== window.top) {
                      iframe = document.createElement('iframe');
                      iframe.setAttribute('src', canvasDomain + attachment.preview_url);
                      iframe.classList.add('blog-embed');
                      article.appendChild(iframe);
                      break;
                    }
                  default:
                    console.log('unknown mime:', contentType, attachment);
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
                    anchor.innerHTML = attachment.display_name;
                    anchor.href = attachment.url;
                    article.classList.add('file');
                    article.appendChild(anchor);
                    break;
                }
                section.appendChild(article);
              }
            );
          }
          if (attempt.comment) {
            var article = document.createElement('article');
            article.classList.add('row');

            // tag blacklisted items
            var blacklistId = 'cid/' + attempt.id;
            console.log(blacklistId);
            if (blacklist.indexOf(blacklistId) > -1) {
              article.classList.add('blacklisted');
            }

            if (attempt.author_id === currentUserId) {
              article.classList.add('byself');
            }

            // add metaheader TODO REMOVE CODE DUPLICATE except for author id
            var metaheader = document.createElement('header');
            var time = document.createElement('time');
            time.innerHTML = moment(date).format('LT');
            metaheader.appendChild(time);
            if (!currentUserId) {
              var author = document.createElement('span');
              author.classList.add('row-author');
              author.innerHTML = getStudentNameForId(attempt.author_id);
              metaheader.appendChild(author);
            }

            var deleteButton = document.createElement('i');
            deleteButton.setAttribute('class', 'fa fa-times');
            deleteButton.classList.add( (blacklist.indexOf(blacklistId) > -1)? 'undeleteButton' : 'deleteButton' );
            deleteButton.setAttribute('title', 'Hide or unhide your comment here for others.');
            metaheader.appendChild(deleteButton);
            article.appendChild(metaheader);

            deleteButton.addEventListener('click', function (e) {
              console.log('clicked');
              if (deleteButton.classList.contains('deleteButton')) {
                deleteButton.classList.remove('deleteButton');
                deleteButton.classList.add('undeleteButton');
                article.classList.add('blacklisted');
                $.post('service/blacklists/add.php', {id: blacklistId})
              } else {
                deleteButton.classList.add('deleteButton');
                deleteButton.classList.remove('undeleteButton');
                article.classList.remove('blacklisted');
                $.post('service/blacklists/remove.php', {id: blacklistId})
              }
            });

            article.appendChild(metaheader);
            // add author to comment
            var authorHeader = document.createElement('div');
            authorHeader.classList.add('commentauthor');
            authorHeader.innerHTML = attempt.author_name.split(" ")[0] + ' says:';
            article.appendChild(authorHeader);

            var paragraph = document.createElement('p');
            paragraph.innerHTML = anchorme.js(attempt.comment); // replaces links!

            article.classList.add('comment');
            article.appendChild(paragraph);
            section.appendChild(article);
          }
          if (attempt.body) {
            var article = document.createElement('article');
            article.classList.add('row');

            // tag blacklisted items
            var blacklistId = 'bid/' + attempt.id + '/' + attempt.attempt;
            console.log(blacklistId);
            if (blacklist.indexOf(blacklistId) > -1) {
              article.classList.add('blacklisted');
            }

            // add metaheader TODO REMOVE CODE DUPLICATE except for author id
            var metaheader = document.createElement('header');
            var time = document.createElement('time');
            time.innerHTML = moment(date).format('LT');
            metaheader.appendChild(time);
            if (!currentUserId) {
              var author = document.createElement('span');
              author.classList.add('row-author');
              author.innerHTML = getStudentNameForId(attempt.user_id);
              metaheader.appendChild(author);
            }

            var deleteButton = document.createElement('i');
            deleteButton.setAttribute('class', 'fa fa-times');
            deleteButton.classList.add( (blacklist.indexOf(blacklistId) > -1)? 'undeleteButton' : 'deleteButton' );
            deleteButton.setAttribute('title', 'Hide or unhide your post here for others.');
            metaheader.appendChild(deleteButton);
            article.appendChild(metaheader);

            deleteButton.addEventListener('click', function (e) {
              console.log('clicked');
              if (deleteButton.classList.contains('deleteButton')) {
                deleteButton.classList.remove('deleteButton');
                deleteButton.classList.add('undeleteButton');
                article.classList.add('blacklisted');
                $.post('service/blacklists/add.php', {id: blacklistId})
              } else {
                deleteButton.classList.add('deleteButton');
                deleteButton.classList.remove('undeleteButton');
                article.classList.remove('blacklisted');
                $.post('service/blacklists/remove.php', {id: blacklistId})
              }
            });

            article.appendChild(metaheader);

            var paragraph = document.createElement('p');
            paragraph.innerHTML = anchorme.js(attempt.body.replace(/\n/g, '<br>')); // replaces links!
            article.classList.add('textpost');
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
    }
  }

  $(function () {
    loadStudents();
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

  $('#comment-text').trumbowyg({
//      mobile: true,
//      tablet: true,
      fullscreenable: false,
      autogrow: true,
      btns: ['viewHTML',
        '|', 'formatting',
        '|', 'btnGrp-design',
        '|', 'link',
        '|', 'btnGrp-justify',
        '|', 'btnGrp-lists']
    }
  );

  // check in-frame for mobile upload link
  if (window != window.top) {
    document.getElementById('mobile-upload-link').style.display = 'block';
  }

</script>

</body>
</html>
