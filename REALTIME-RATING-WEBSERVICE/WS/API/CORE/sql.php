<?php
    
    define('SQL_SERVER',($_SERVER['DEV'] ? 'localhost' : 'localhost'));
    define('SQL_DB', ($_SERVER['DEV'] ? 'RTR' : 'rate'));
    define('SQL_USER', ($_SERVER['DEV'] ? 'root' : 'rate'));
    define('SQL_PWD', ($_SERVER['DEV'] ? 'root' : 'WDxqzTAaTycjjUyU'));

    mysql_connect(SQL_SERVER,SQL_USER,SQL_PWD);
    mysql_select_db(SQL_DB);

    function toSQL($arg) {
        if(is_numeric($arg)) {
            return $arg;
        } else {
            return '"' . mysql_real_escape_string($arg) . '"';
        }
    }

    
?>