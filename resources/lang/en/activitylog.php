<?php

return [
    'action' => [
        'label' => 'Log',
        'heading' => 'Log',
    ],
    'form' => [
        'fields' => [
            'comment' => [
                'label' => 'Comment',
                'helper_text' => 'Add a comment to the log to document changes. These comments are not intended to communicate with other users. You can use Markdown in the comment.',
            ],
        ],
        'buttons' => [
            'save' => 'Save',
        ],
    ],
    'events' => [
        'created' => [
            'label' => 'Created',
        ],
        'updated' => [
            'label' => 'Updated',
        ],
        'deleted' => [
            'label' => 'Deleted',
        ],
    ],
    'attributes_table' => [
        'columns' => [
            'attribute' => 'Attribute',
            'value' => 'Value',
            'old_value' => 'Old value',
            'new_value' => 'New value',
        ],
        'values' => [
            'null' => 'Not set',
            'empty' => 'Empty',
            'unknown' => 'Unknown',
            'yes' => 'Yes',
            'no' => 'No',
        ],
    ],
];
