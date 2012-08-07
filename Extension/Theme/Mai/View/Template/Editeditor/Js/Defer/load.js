$(document).ready(
    function()
    {
        $('#redactor_content').redactor({
            css: 'docstyle.css',
            autoresize: true,
            fixed: true,
            imageUpload: 'scripts/image_upload.php',
            fileUpload: 'file_upload.php',
            imageGetJson: 'json/data.json'
        });
    }
);
