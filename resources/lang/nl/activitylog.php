<?php

return [
    'action' => [
        'label' => 'Log',
        'heading' => 'Log',
    ],
    'form' => [
        'fields' => [
            'comment' => [
                'label' => 'Commentaar',
                'helper_text' => 'Voeg een commentaar toe aan de log om wijzigingen te documenteren. Deze commentaren zijn niet bedoeld voor communicatie met andere gebruikers. In het commentaar kun je Markdown gebruiken.',
            ],
        ],
        'buttons' => [
            'save' => 'Opslaan',
        ],
    ],
    'events' => [
        'created' => [
            'label' => 'Aangemaakt',
        ],
        'updated' => [
            'label' => 'Bijgewerkt',
        ],
        'deleted' => [
            'label' => 'Verwijderd',
        ],
    ],
    'attributes_table' => [
        'columns' => [
            'attribute' => 'Eigenschap',
            'value' => 'Waarde',
            'old_value' => 'Oude waarde',
            'new_value' => 'Nieuwe waarde',
        ],
        'values' => [
            'null' => 'Niet ingesteld',
            'empty' => 'Leeg',
            'unknown' => 'Onbekend',
            'yes' => 'Ja',
            'no' => 'Nee',
        ],
    ],
];
