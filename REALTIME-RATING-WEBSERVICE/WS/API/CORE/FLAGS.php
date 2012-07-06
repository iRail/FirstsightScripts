<?php
/*
	//////////////////////////////////////
	// FLAGS defined in the application //
	//////////////////////////////////////
	
	// core/throw.php
	FLAG_ERROR_DIE or 0 { 
		0: die() on everything, 
		1: die() on error, 
		2: never die()
	}

    $_SERVER['DEV'] or false {
        true:  dev server (soft error handling),
        false: prod server (strict error handling)
    }
	
	// core/sql.php
	$_ENV['sqlState'] or false {
		false: sql not connected,
		true:  sql connected
		// an error is usually thrown if false; 
		// use only if you specifically discarded the error
		// or to disable SQL connection (set the variable to false before including core.php)
	}
	
*/

?>