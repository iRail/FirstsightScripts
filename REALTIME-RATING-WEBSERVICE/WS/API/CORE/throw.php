<?php
	
	/*
		This file is used to define the error handling mechanism of our project.
		
		Usage:
			
			// Allow to continue the execution if an error occur
			cCatch(null);
				
				// Throw an error
				cThrow(160,'Sample error message');
				echo 'This message will be shown.';
			
			// End catch hook
			cEndCatch();
					
	*/
	
	$_ENV['ERROR_CODE'] = 0;
	$_ENV['ERROR_CONTEXT'] = null;
	$_SERVER['DEV'] = ($_SERVER['SERVER_NAME'] == "127.0.0.1" || $_SERVER['SERVER_NAME'] == "localhost");
	
	// Throw a specified user error
	function cThrow($errCode, $vars=null, $errMessage="", $level=E_USER_ERROR) {
		$_ENV['ERROR_CODE'] = $errCode;
		$_ENV['ERROR_CONTEXT'] = $vars;
		if(defined('ERR_'.$errCode)) {
			if($errMessage=="") {
				if ($errCode==ERR_SQL) {
					trigger_error(constant('ERR_'.$errCode).'; '.mysql_error().'.', $level);
				} else {
					trigger_error(constant('ERR_'.$errCode).'.', $level);
				}
			} else {
				trigger_error(constant('ERR_'.$errCode).'; '.$errMessage.'.', $level);
			}
		} else {
			trigger_error($errMessage.'.', $level);
		}
		$_ENV['ERROR_CONTEXT'] = null;
		$_ENV['ERROR_CODE'] = 0;
	}
	
	// Throw a specified user warning (will not die in production)
	function cWarn($errCode, $vars=null, $errMessage="") {
		cThrow($errCode, $vars, $errMessage, E_USER_WARNING);
	}
	
	// Send a mail to the server administrator
	function adminMail($message,$title='Le site a repéré une action illégale') {
		$headers ='From: "Module de sécurité"<webmaster@firstsight.be>'."\n"; 
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n"; 
		$headers .='Content-Transfer-Encoding: 8bit'; 
		mail('webmaster@firstsight.be',$title,'<htm><head><title>'.htmlentities($title).'</title></head><body><pre>'.$message.'</pre></body></html>',$headers);
	}
	
	// Error reporting will be different on the production server, to avoid the Butterfly effect (small error, big consequences)
	if($_SERVER['DEV']) {
		
		error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR); 
		$FLAG_ERROR_DIE=0;
		
	} else {
		
		error_reporting(0); 
		$FLAG_ERROR_DIE=1;
		
	}

	// Allow to define temporary error handlers
	$cCatchFuncs = array();
	function cCatch($fun=null) { global $cCatchFuncs; if($fun==null) { $cCatchFuncs[]=(function() { return true; }); } else { $cCatchFuncs[] = $fun; }; }
	function cEndCatch() { global $cCatchFuncs; array_pop($cCatchFuncs); }
	function cTry($fun,$err=null) {
		$result=null; cCatch($err); $result=$fun(); $cEndCatch(); return $result;
	}
	
	// Walk all curently defined error handlers
	set_error_handler(function($level, $errMessage, $errFile, $errLine, $errContext) {
		
		global $cCatchFuncs;
		
		$errCode = $_ENV['ERROR_CODE'];
		if ($_ENV['ERROR_CONTEXT'] != null) { $errContext=$_ENV['ERROR_CONTEXT']; }
		if ($errCode == 0) { $errCode = 666; /* unknown error */ }
		
		$i=count($cCatchFuncs);
		while($i!=0) {
			$i--; $fun = $cCatchFuncs[$i];
			if($fun($errCode, $errMessage, $level, $errFile, $errLine, $errContext)) {
				return true; // stop the execution after an handler returned true
			}
		}
		
		// If no handler was set, there must be an error
		die();
		
	});
	
	// Default error handler
	cCatch(function($errCode, $errMessage, $level, $errFile, $errLine, $errContext) {
		
		// If the PHP has asked not to report errors, those are not reported
		if (error_reporting() != 0) {
			if(!$_SERVER['DEV']) {
			
				// DON'T DISCLOSE PRIVATE INFORMATIONS IN PRODUCTION
				if(isset($_ENV['json'])) {
					echo js_encode(array(false, $errCode, $errMessage));
				} else {
					echo "<PRE>ERREUR $errCode: $errMessage</PRE>";
				}
				
			} else {
			
				// GIVE MORE INFORMATION ABOUT THE ERROR IN DEBUG
				if(isset($_ENV['json'])) {
					
					echo js_encode(array(
						false, 
						$errCode, 
						$errMessage, 
						$errFile, 
						$errLine, 
						print_r($errContext, true), 
						print_r(debug_backtrace(), true)
					));
					
				} else {
					
					echo "<pre>ERREUR $errCode: $errMessage\n";
					var_dump(
						$errFile, 
						$errLine, 
						$errContext
					);
					
					foreach (debug_backtrace() as $item) {
						echo "<hr/>";
						var_dump($item);
					}
					
					echo "</pre>";
					
				}
				
			}
		}
		
		// Send a security mail if needed
		if($errCode>=2000 && $errCode<3000) {
			if(!$_SERVER['DEV']) {
				adminMail(print_r(array(
					false, 
					$errCode, 
					$errMessage, 
					$errFile, 
					$errLine, 
					print_r($errContext, true), 
					print_r(debug_backtrace(), true)
				), true));
			}
		}
		
		// Stop the execution, unless a special flag exist
		if(!isset($FLAG_ERROR_DIE)) {
			die();
		} else if($FLAG_ERROR_DIE==0) {
			die();
		} else if($FLAG_ERROR_DIE==1 && ($level!=E_WARNING && $level!=E_USER_WARNING && $level!=E_NOTICE)) {
			die();
		} else {
			// do nothing
			// this behavior is not safe but has been explicitely requested
			return true;
		}
		
	});

	require_once(pathto('/API/CORE/ERROR_CODES.php'));
	
	/*
        //TEST
	    cTry(function() {
	
		    // Throw some errors, to test out
		    cThrow(ERR_UNKNOWN);
		    cThrow(ERR_RIGHTS);
		
	    }, function($c) {
		
		    // Catch ERR_UNKNOWN
		    return ($c==ERR_UNKNOWN);
		
	    });
	*/
	
?>