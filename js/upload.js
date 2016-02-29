$().ready(function () {
  $(':file').change(function () {
    var file = this.files[0];

    var formData = new FormData($('#upload-form')[0]);
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
        if (window.hasOwnProperty('loadSubmissions')) {
          loadSubmissions();
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
      window.location = '#!';
      var formData = {submission: document.getElementById('submission-text').value};

      $.ajax({
        url: 'textsubmission.php',  //Server script to process data
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
          uploadForm.reset();
          // show new results
          document.body.classList.remove('uploading');
          toastr.success('Text upload completed!');
          // reload submissions if function exists
          if (window.hasOwnProperty('loadSubmissions')) {
            loadSubmissions();
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
