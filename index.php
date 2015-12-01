<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'lib/canvasAPI.php';
require_once('lib/dailysnapshot.php');

?>

<html>
<head>
    <title>Daily Snapshot</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/skeleton.css">
    <link rel="stylesheet" href="css/dailysnapshot.css">
    <script src="js/moment.min.js"></script>
</head>
<body>
<script>
    var submissions = <?php echo json_encode($submissions); ?>;

    submissions.forEach(function (submission) {
        var section = document.createElement('section');
        section.classList.add('container');

        // create a list of date-sorted attachments and comments
        var articles = [].concat(submission.submission_comments).concat(submission.submission_history);
        articles.sort(function (a, b) {
            // sort by submission date and let attachments go before comments (new to old)
            var aa = a.submitted_at ? new Date(a.submitted_at) : new Date(new Date(a.created_at).valueOf() - 20000),
                bb = b.submitted_at ? new Date(b.submitted_at) : new Date(new Date(b.created_at).valueOf() - 20000);
            return aa < bb;
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


            var article = document.createElement('article');
            article.classList.add('row');

            if (attempt.attachments) {
                var figure = document.createElement('figure');
                attempt.attachments.forEach(function (attachment) {
                    var contentType = attachment['content-type'];
                    console.log(contentType);
                    switch (contentType) {
                        case 'image/png':
                        case 'image/jpeg':
                        case 'image/jpg':
                            var img = document.createElement('img');
                            img.src = attachment.url;
                            img.classList.add('u-full-width');
                            figure.appendChild(img);
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
                            figure.appendChild(icon);

                            var anchor = document.createElement('a');
                            anchor.innerHTML = attachment.filename;
                            anchor.href = attachment.url;
                            figure.appendChild(anchor);
                            break;
                    }
                });
                article.appendChild(figure);
            }
            if (attempt.comment) {
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
            }

            section.appendChild(article);

        });

        document.body.appendChild(section);
    });
</script>


</body>
</html>