<?php

return [

    'label' => 'Import :label',

    'modal' => [

        'heading' => 'Import :label',

        'form' => [

            'file' => [

                'label' => 'File',

                'placeholder' => 'Upload a CSV file',

                'rules' => [
                    'duplicate_columns' => '{0} The file must not contain an empty column header.|{1,*} The file must not contain duplicate column headers: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Columns',
                'placeholder' => 'Select a column',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Download example CSV file',
            ],

            'import' => [
                'label' => 'Import',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import completed',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Download failed row information|Download failed rows information',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Uploaded CSV file too large',
            'body' => 'You may not import more than 1 row at a time.|You may not import more than :count rows at a time.',
        ],

        'started' => [
            'title' => 'Import started',
            'body' => 'Your import has begun and 1 row will be processed in the background.|Your import has begun and :count rows will be processed in the background.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'System error, please contact support.',
        'column_mapping_required_for_new_record' => 'The :attribute column was not mapped to a column in the file, but this is required to create new records.',
    ],

];
