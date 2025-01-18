jQuery(document).ready(function ($) {
  let mediaUploader;

  $(document).on("click", ".tag-icon-upload", function (e) {
    e.preventDefault();

    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    mediaUploader = wp.media({
      title: "Select Tag Image",
      button: {
        text: "Use This Image",
      },
      multiple: false,
    });

    mediaUploader.on("select", function () {
      const attachment = mediaUploader
        .state()
        .get("selection")
        .first()
        .toJSON();
      $("#tag_icon").val(attachment.url);
      $("#tag-icon-preview").html(
        '<img src="' +
          attachment.url +
          '" style="max-width: 100px; max-height: 100px;">'
      );
    });

    mediaUploader.open();
  });
});
