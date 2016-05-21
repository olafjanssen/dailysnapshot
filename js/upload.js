$().ready(function () {
  $(':file').change(function () {
    var file = this.files[0];

    var formData = new FormData($('#upload-form')[0]);
    $.ajax({
      url: 'service/submission.php',  //Server script to process data
      type: 'POST',
      xhr: function () {  // Custom XMLHttpRequest
        var myXhr = $.ajaxSettings.xhr();
        if (myXhr.upload) { // Check if upload property exists
          myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
        }
        return myXhr;
      },
      //Ajax events
      beforeSend: function () {
        document.body.classList.add('uploading');
      },
      success: function () {
        // reset form
        //$('#upload-form').reset();
        document.getElementById('upload-form').reset();
        // show new results
        document.body.classList.remove('uploading');
        toastr.success('Upload completed!');
        if (window.hasOwnProperty('loadSubmission')) {
          loadSubmission(currentUserId);
        }
      },
      error: function () {
        document.body.classList.remove('uploading');
        toastr.error('Upload error:', e);
      },
      // Form data
      data: formData,
      //Options to tell jQuery not to process data or worry about content-type.
      cache: false,
      contentType: false,
      processData: false
    });
  });

  function progressHandlingFunction(e) {
    if (e.lengthComputable) {
      $('progress').attr({value: e.loaded, max: e.total});
    }
  }

  var uploadForm = document.getElementById('text-upload-form');
  if (uploadForm) {
    document.getElementById('text-upload-form').addEventListener('submit', function (e) {
      e.preventDefault();
      window.location = '#!';
      var formData = {submission: document.getElementById('submission-text').innerHTML};

      $.ajax({
        url: 'service/textsubmission.php',  //Server script to process data
        type: 'POST',
        xhr: function () {  // Custom XMLHttpRequest
          var myXhr = $.ajaxSettings.xhr();
          if (myXhr.upload) { // Check if upload property exists
            myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
          }
          return myXhr;
        },
        //Ajax events
        beforeSend: function () {
          document.body.classList.add('uploading');
        },
        success: function () {
          document.getElementById('submission-text').innerHTML = "";
          // show new results
          document.body.classList.remove('uploading');
          toastr.success('Text upload completed!');
          // reload submissions if function exists
          if (window.hasOwnProperty('loadSubmission')) {
            loadSubmission(currentUserId);
          }
        },
        error: function () {
          document.body.classList.remove('uploading');
          toastr.error('Upload error:', e);
        },
        // Form data
        data: formData,
        //Options to tell jQuery not to process data or worry about content-type.
        cache: false
      });
    });
  }

  var commentForm = document.getElementById('comment-upload-form');
  if (commentForm) {
    document.getElementById('comment-upload-form').addEventListener('submit', function (e) {
      e.preventDefault();
      var formData = {submission: document.getElementById('comment-text').innerHTML, user: currentUserId};
      $.ajax({
        url: 'service/comment.php',  //Server script to process data
        type: 'POST',
        xhr: function () {  // Custom XMLHttpRequest
          var myXhr = $.ajaxSettings.xhr();
          if (myXhr.upload) { // Check if upload property exists
            myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
          }
          return myXhr;
        },
        //Ajax events
        beforeSend: function () {
          document.body.classList.add('uploading');
        },
        success: function () {
          document.getElementById('comment-text').innerHTML = "";
          // show new results
          document.body.classList.remove('uploading');
          toastr.success('Comment posted!');
          // reload submissions if function exists
          if (window.hasOwnProperty('loadSubmission')) {
            loadSubmission(currentUserId);
          }
        },
        error: function () {
          document.body.classList.remove('uploading');
          toastr.error('Upload error:', e);
        },
        // Form data
        data: formData,
        //Options to tell jQuery not to process data or worry about content-type.
        cache: false
      });
    });
  }

  document.getElementById('text-upload-wrapper').addEventListener('click', function (e) {
    setTimeout(function () {
      document.getElementById('submission-text').focus();
    }, 100);
  });
});
