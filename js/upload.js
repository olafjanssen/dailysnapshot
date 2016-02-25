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
        // show new results
        document.body.classList.remove('uploading');
        loadSubmissions();
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
});
