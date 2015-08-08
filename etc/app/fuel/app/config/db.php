<?php
/**
 * Use this file to override global defaults.
 *
 * See the individual environment DB configs for specific config information.
 */

return array(
    'active' => 'default',
    'default' => array(
	'connection'     => array(
	    'persistent'     => false,
	    'compress'       => false,
	),
	'identifier'     => '`',
	'table_prefix'   => '',
	'charset'        => 'utf8',
	'enable_cache'   => true,
	'profiling'      => false,
    ),
);

