<?php

return [
    'routes' => [
        'api' => [
            'fileName' => 'routes/api.php',
        ],
        'web' => [
            'fileName' => 'routes/web.php',
        ],
    ],
    'files' => [
        'model' => [
            'path' => 'Models',
            'fileNamePattern' => '*|$model->name|*.php',
        ],
        'apiController' => [
            'path' => 'app/Http/Controllers/Api',
            'fileNamePattern' => '*|$model->name|*Controller.php',
        ],
        'webController' => [
            'path' => 'app/Http/Controllers/Web',
            'fileNamePattern' => '*|$model->name|*Controller.php',
        ],
        'request' => [
            'path' => 'app/Http/Requests',
            'fileNamePattern' => '*|$model->name|*Request.php',
        ],
        'show' => [
            'path' => 'resources/views/*|strtolower($model->name)|*',
            'fileNamePattern' => 'show.blade.php',
        ],
        'editField' => [
            'path' => 'resources/assets/js/components/global',
            'fileNamePattern' => 'edit-field.vue',
        ],
        'vueForm' => [
            'path' => 'resources/assets/js/components/*|$lower|*',
            'fileNamePattern' => '*|$lower|*-form.vue',
            'dependencies' => [
                'storeActions',
                'storeIndex',
                'storeMutations',
                'storeMutationTypes'
            ]
        ],
        'vueList' => [
            'path' => 'resources/assets/js/components/*|$lower|*',
            'fileNamePattern' => '*|$lower|*-list.vue',
            'dependencies' => [
                'vueListItem'
            ]
        ],
        'vueListItem' => [
            'path' => 'resources/assets/js/components/*|$lower|*',
            'fileNamePattern' => '*|$lower|*-list-item.vue',
            'dependencies' => [
                'editField'
            ]
        ],
        'storeActions' => [
            'path' => 'resources/assets/js/store/modules/*|$lowerPlural|*',
            'fileNamePattern' => 'index.js',
        ],
        'storeIndex' => [
            'path' => 'resources/assets/js/store/modules/*|$lowerPlural|*',
            'fileNamePattern' => 'index.js',
        ],
        'storeMutations' => [
            'path' => 'resources/assets/js/store/modules/*|$lowerPlural|*',
            'fileNamePattern' => 'mutations.js',
        ],
        'storeMutationTypes' => [
            'path' => 'resources/assets/js/store/modules/*|$lowerPlural|*',
            'fileNamePattern' => 'mutation-types.js',
        ],
        'index' => [
            'path' => 'resources/views/*|strtolower($model->name)|*',
            'fileNamePattern' => 'index.blade.php',
        ],
    ],
    'variables' => [
        'lower' => 'strtolower($model->name)',
        'listItem' => 'strtolower($model->name) . "-list-item"',
        'list' => 'strtolower($model->name) . "-list"',
        'lowerPlural' => 'Illuminate\Support\Str::plural(strtolower($model->name))',
        'plural' => 'Illuminate\Support\Str::plural($model->name)',
        'vueFormName' => 'strtolower($model->name) . \'-form\'',

    ],

    'columnTypes' => [
        'bigIncrements' => '',
        'bigInteger' => '',
        'binary' => '',
        'boolean' => [
            'fillable' => true,
            'dates' => false,
            'touches' => false,
            'casts' => 'boolean',
            'rules' => 'required|boolean'
        ],
        'char' => '',
        'date' => '',
        'dateTime' => '',
        'dateTimeTz' => '',
        'decimal' => '',
        'double' => '',
        'enum' => [
            'fillable' => true,
            'dates' => false,
            'touches' => false,
            'casts' => false,
            'rules' => 'required'
        ],
        'float' => '',
        'geometry' => '',
        'geometryCollection' => '',
        'integer' => [
            'fillable' => false,
            'dates' => false,
            'touches' => false,
            'casts' => false,
            'rules' => 'required|integer'
        ],
        'increments' => [
            'fillable' => false,
            'dates' => false,
            'touches' => false,
            'casts' => false,
        ],
        'ipAddress' => '',
        'json' => '',
        'jsonb' => '',
        'lineString' => '',
        'longText' => [
            'fillable' => true,
            'dates' => false,
            'touches' => false,
            'casts' => false,
            'rules' => 'required'
        ],
        'macAddress' => '',
        'mediumIncrements' => '',
        'mediumInteger' => '',
        'mediumText' => '',
        'morphs' => '',
        'multiLineString' => '',
        'multiPoint' => '',
        'multiPolygon' => '',
        'nullableMorphs' => '',
        'nullableTimestamps' => '',
        'point' => '',
        'polygon' => '',
        'rememberToken' => '',
        'smallIncrements' => '',
        'smallInteger' => '',
        'softDeletes' => '',
        'softDeletesTz' => '',
        'string' => [
            'fillable' => true,
            'dates' => false,
            'touches' => false,
            'casts' => false,
            'rules' => 'required'
        ],
        'text' => [
            'fillable' => true,
            'dates' => false,
            'touches' => false,
            'casts' => false,
            'rules' => 'required'
        ],
        'time' => '',
        'timeTz' => '',
        'timestamp' => [
            'fillable' => false,
            'dates' => true,
            'touches' => false,
            'casts' => false,
            'rules' => false,
        ],
        'timestampTz' => '',
        'tinyIncrements' => '',
        'tinyInteger' => [
            'fillable' => true,
            'dates' => false,
            'touches' => false,
            'casts' => false,
            'rules' => 'required|integer'
        ],
        'unsignedBigInteger' => '',
        'unsignedDecimal' => '',
        'unsignedInteger' => [
            'fillable' => true,
            'dates' => false,
            'touches' => false,
            'casts' => false,
            'rules' => 'required|integer'
        ],
        'unsignedMediumInteger' => '',
        'unsignedSmallInteger' => '',
        'unsignedTinyInteger' => '',
        'uuid' => '',
        'year' => '',
    ]
];
