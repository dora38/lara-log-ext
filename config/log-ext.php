<?php

return [
    // Writing command logs in console commands.
    'command_log_enable' => (bool) env('COMMAND_LOG_ENABLE', false),

    // Log level of command logs: emergency|alert|critical|error|warning|notice|info|debug
    'command_log_level' => (string) env('COMMAND_LOG_LEVEL', 'info'),

    // Commands that will be excluded for command logs.
    'command_log_exclude' => array_values(array_filter(array_map('trim', explode(',', env('COMMAND_LOG_EXCLUDE', 'package:discover, schedule:run, schedule:finish, vendor:publish'))))),

    // Writing route logs in http operations.
    'route_log_enable' => (bool) env('ROUTE_LOG_ENABLE', false),

    // Log level of route logs: emergency|alert|critical|error|warning|notice|info|debug
    'route_log_level' => (string) env('ROUTE_LOG_LEVEL', 'info'),

    // Commands that will be excluded for command logs.
    'route_log_include' => array_values(array_filter(array_map('trim', explode(',', env('ROUTE_LOG_INCLUDE', ''))))),

    // Commands that will be excluded for command logs.
    'route_log_exclude' => array_values(array_filter(array_map('trim', explode(',', env('ROUTE_LOG_EXCLUDE', ''))))),

    // Writing SQL trace logs in console commands.
    'sql_log_console' => (bool) env('SQL_LOG_CONSOLE', false),

    // Writing SQL trace logs in http operations.
    'sql_log_http' => (bool) env('SQL_LOG_HTTP', false),

    // Log level of SQL trace logs: emergency|alert|critical|error|warning|notice|info|debug
    'sql_log_level' => (string) env('SQL_LOG_LEVEL', 'debug'),

    // Writing queue logs in queue processing.
    'queue-log-enable' => (bool) env('QUEUE_LOG_ENABLE', false),

    // Log level of queue logs: emergency|alert|critical|error|warning|notice|info|debug
    'queue-log-level' => env('QUEUE_LOG_LEVEL', 'debug'),
];
