<?php

return [
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
            'rules' => 'required'
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
            'fillable' => false,
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
