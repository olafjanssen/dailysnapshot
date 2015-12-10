<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'lib/canvasAPI.php';
require_once('lib/dailysnapshot.php');

?>

<html>
<head>
    <title>Daily Snapshot</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="bower_components/webcomponentsjs/webcomponents-lite.min.js"></script>
    <link rel="import" href="bower_components/paper-card/paper-card.html">
    <link rel="import" href="bower_components/paper-dropdown-menu/paper-dropdown-menu.html">
    <link rel="import" href="bower_components/paper-menu/paper-menu.html">
    <link rel="import" href="bower_components/paper-item/paper-item.html">


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<!--    <link rel="stylesheet" href="css/skeleton.css">-->
<!--    <link rel="stylesheet" href="css/dailysnapshot.css">-->
    <script src="js/moment.min.js"></script>
</head>
<body>

<paper-dropdown-menu label="Student">
    <paper-menu class="dropdown-content" selected="0" id="student-filter">
        <paper-item>Show all</paper-item>
    </paper-menu>
</paper-dropdown-menu>

<!--<select id="student-filter">-->
<!--    <option>Show all</option>-->
<!--</select>-->
<section id="student-blog"></section>
<script>

    var submissions = <?php echo json_encode($submissions); ?>;
    var selectElement = document.getElementById('student-filter');

    var students = [];
    submissions.forEach(function (submission) {
        students.push(submission.user);
        var option = document.createElement('paper-item');
        option.innerHTML = submission.user['sortable_name'];
        selectElement.appendChild(option);
    });

    // todo
    selectElement.addEventListener('change', function (e) {
        console.log('hello!');
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
        section.classList.add('container');

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

                var card = document.createElement('paper-card');
                card.setAttribute('heading', dateString);
                section.appendChild(card);
            }


            var card = document.createElement('paper-card'),
                cardContent = document.createElement('div');

            cardContent.classList.add('card-content');
            card.setAttribute('preloadimage', 'true');
            card.appendChild(cardContent);

            if (attempt.attachments) {
                attempt.attachments.forEach(function (attachment) {
                    var contentType = attachment['content-type'];
                    switch (contentType) {
                        case 'image/png':
                            card.setAttribute('image', attachment.url);
                            break;
                        case 'image/jpeg':
                        case 'image/jpg':
                            card.setAttribute('image', 'jpegProxy.php?path=' + encodeURIComponent(attachment.url));
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
                            cardContent.appendChild(icon);

                            var anchor = document.createElement('a');
                            anchor.innerHTML = attachment.filename;
                            anchor.href = attachment.url;

                            cardContent.appendChild(anchor);
                            break;
                    }
                });
            }
            if (attempt.comment) {
//                var avatar = document.createElement('img');
//                avatar.src = attempt.author.avatar_image_url;
//                avatar.classList.add('avatar');
//                var author = document.createElement('em');
//                author.innerHTML = attempt.author.display_name;
//                author.classList.add('author');

                var paragraph = document.createElement('p');
                paragraph.innerHTML = attempt.comment;

//                article.classList.add('comment');
//                article.appendChild(avatar);
//                article.appendChild(author);
                cardContent.appendChild(paragraph);
            }

            section.appendChild(card);

        });

        document.body.appendChild(section);
    }

</script>


</body>
</html>