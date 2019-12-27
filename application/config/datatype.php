<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config["list"] = [
    'Numeric' => [
        'TINYINT',
        'SMALLINT',
        'MEDIUMINT',
        'INT',
        'BIGINT',
        'DECIMAL',
        'FLOAT',
        'DOUBLE',
        'REAL',
        'BIT',
        'BOOLEAN',
        'SERIAL'
    ],
    'Date and time' => [
        'DATE',
        'DATETIME',
        'TIMESTAMP',
        'TIME',
        'YEAR'
    ],
    'String' => [
        'CHAR',
        'VARCHAR',
        'TINYTEXT',
        'TEXT',
        'MEDIUMTEXT',
        'LONGTEXT',
        'BINARY',
        'VARBINARY',
        'TINYBLOB',
        'MEDIUMBLOB',
        'BLOB',
        'LONGBLOB',
        'ENUM',
        'SET'
    ],
    'Spartial' => [
        'GEOMETRY',
        'POINT',
        'LINESTRING',
        'POLYGON',
        'MULTIPOINT',
        'MULTILINESTRING',
        'MULTIPOLYGON',
        'GEOMETRYCOLLECTION'
    ]
];