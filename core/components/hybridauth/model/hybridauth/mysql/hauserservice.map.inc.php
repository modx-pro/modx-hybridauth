<?php

$xpdo_meta_map['haUserService'] = array(
    'package' => 'hybridauth',
    'version' => '1.0',
    'table' => 'ha_user_services',
    'extends' => 'xPDOSimpleObject',
    'tableMeta' =>
        array(
            'engine' => 'InnoDB',
        ),
    'fields' =>
        array(
            'internalKey' => null,
            'identifier' => null,
            'provider' => null,
            'createdon' => null,
            'websiteurl' => null,
            'profileurl' => null,
            'photourl' => null,
            'displayname' => null,
            'description' => null,
            'firstname' => null,
            'lastname' => null,
            'gender' => null,
            'language' => null,
            'age' => null,
            'birthday' => null,
            'birthmonth' => null,
            'birthyear' => null,
            'email' => null,
            'emailverified' => null,
            'phone' => null,
            'address' => null,
            'country' => null,
            'region' => null,
            'city' => null,
            'zip' => null,
            'extended' => null,
        ),
    'fieldMeta' =>
        array(
            'internalKey' =>
                array(
                    'dbtype' => 'int',
                    'precision' => '10',
                    'phptype' => 'integer',
                    'null' => false,
                    'attributes' => 'unsigned',
                    'index' => 'unique',
                    'indexgrp' => 'unique_fields',
                ),
            'identifier' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => false,
                ),
            'provider' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '50',
                    'phptype' => 'string',
                    'null' => false,
                    'index' => 'unique',
                    'indexgrp' => 'unique_fields',
                ),
            'createdon' =>
                array(
                    'dbtype' => 'datetime',
                    'phptype' => 'datetime',
                ),
            'websiteurl' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'profileurl' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'photourl' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'displayname' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'description' =>
                array(
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'firstname' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'lastname' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'gender' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '50',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'language' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '50',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'age' =>
                array(
                    'dbtype' => 'tinyint',
                    'precision' => '3',
                    'phptype' => 'integer',
                    'null' => true,
                ),
            'birthday' =>
                array(
                    'dbtype' => 'tinyint',
                    'precision' => '2',
                    'phptype' => 'integer',
                    'null' => true,
                ),
            'birthmonth' =>
                array(
                    'dbtype' => 'tinyint',
                    'precision' => '2',
                    'phptype' => 'integer',
                    'null' => true,
                ),
            'birthyear' =>
                array(
                    'dbtype' => 'smallint',
                    'precision' => '4',
                    'phptype' => 'integer',
                    'null' => true,
                ),
            'email' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'emailverified' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'phone' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'address' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'country' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'region' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'city' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '100',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'zip' =>
                array(
                    'dbtype' => 'varchar',
                    'precision' => '25',
                    'phptype' => 'string',
                    'null' => true,
                ),
            'extended' =>
                array(
                    'dbtype' => 'text',
                    'phptype' => 'json',
                    'null' => true,
                    'index' => 'fulltext',
                ),
        ),
    'indexes' =>
        array(
            'unique_fields' =>
                array(
                    'alias' => 'unique_fields',
                    'primary' => false,
                    'unique' => true,
                    'type' => 'BTREE',
                    'columns' =>
                        array(
                            'internalKey' =>
                                array(
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ),
                            'provider' =>
                                array(
                                    'length' => '',
                                    'collation' => 'A',
                                    'null' => false,
                                ),
                        ),
                ),
        ),
    'aggregates' =>
        array(
            'User' =>
                array(
                    'class' => 'modUser',
                    'local' => 'internalKey',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ),
        ),
);
