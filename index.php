<html>
<head>
  <title>Daily Snapshot</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="bower_components/normalize-css/normalize.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/dailysnapshot.css">
  <link rel="stylesheet" href="http://css-spinners.com/css/spinner/pong.css" type="text/css">

  <script src="js/moment.min.js"></script>
  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
</head>
<body>
<header>
  <h1>Daily Snapshot</h1>
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
            var img;
            attempt.attachments.forEach(function (attachment) {
              var article = document.createElement('article');
              article.classList.add('row');

              var contentType = attachment['content-type'];
              switch (contentType) {
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
                default:
                  var icon = document.createElement('i');
                  icon.setAttribute('class', 'fa fa-2x file-icon');
                  switch (contentType) {
                    case 'application/pdf':
                      icon.classList.add('fa-file-pdf-o');
                      break;
                    case 'application/msword':
                      icon.classList.add('fa-file-word-o');
                      break;
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
            paragraph.innerHTML = attempt.comment;

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
