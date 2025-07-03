<?php

return [
    'action' => [
        'label' => 'Protokoll',
        'heading' => 'Protokoll',
    ],
    'form' => [
        'fields' => [
            'comment' => [
                'label' => 'Kommentieren',
                'helper_text' => 'Fügen Sie dem Protokoll einen Kommentar hinzu, um Änderungen zu dokumentieren. Diese Kommentare sind nicht für die Kommunikation mit anderen Benutzern gedacht. Sie können in dem Kommentar Markdown verwenden.',
            ],
        ],
        'buttons' => [
            'save' => 'Speichern',
        ],
    ],
    'events' => [
        'created' => [
            'label' => 'Erstellt',
        ],
        'updated' => [
            'label' => 'Geändert',
        ],
        'deleted' => [
            'label' => 'Gelöscht',
        ],
    ],
    'attributes_table' => [
        'columns' => [
            'attribute' => 'Eigenschaft',
            'value' => 'Wert',
            'old_value' => 'Alter Wert',
            'new_value' => 'Neuer Wert',
        ],
        'values' => [
            'null' => 'Nicht gesetzt',
            'empty' => 'Leer',
            'unknown' => 'Unbekannt',
            'yes' => 'Ja',
            'no' => 'Nein',
        ],
    ],
];
