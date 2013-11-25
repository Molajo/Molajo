=======
Fileupload
=======

To use within the application, create an `$options` array.

You can set an options entry for `maximum_file_size`, `allowable_mimes_and_extensions`, `target_folder`,
    and `overwrite_existing_file`, if desired, or accept the system defaults.

To use, specify the `input_field_name` from the form and the `target_filename` desired upon upload.

```php

    try {

        $options = array();
        $options['input_field_name'] = 'input_field_name';
        $options['target_filename'] = 'target_filename.txt';

        $application = $this->frontcontroller->getService('Fileupload', $options);

    } catch (Exception $e) {
        // deal with the exception
    }

```
