<?php

/*
	* VDF (Valve Data Format) file parser
	* author: devinwl
	* version: 1.07
*/

define("QUOTE", "\"");
define("SINGLE_QUOTE", "'");
define("CURLY_BRACE_BEGIN", "{");
define("CURLY_BRACE_END", "}");
define("NEW_LINE", "\n");
define("C_RETURN", "\r");
define("C_TAB", "\t");
define("C_ESCAPE", "\\");
define("C_COMMENT", '/');
define("PATH_DEPTH_SEPARATOR", '>>>>');

function VDFParse($filename) {
	$parsed = array();
	$ptr = &$parsed;
	$path = '';			// The current level represented as a string
	$p = 0;				// How many quotes have been seen
	$string = "";			// The string of consumed characters
	$key = "";			// The discovered key
	$value = "";			// The discovered corresponding value
	$reading = false;		// Currently consuming characters
	$lastCharSeen = "";		// Tracks the last character seen
	$comment_chars_seen = 0;        // Tracks the number of comments characters seen (2 comment characters designates the start of a comment)

	$filecontent = file_get_contents($filename);

	// Begin parsing by character
	$chunks = str_split($filecontent, 2048);
	unset($filecontent);

	foreach ($chunks as &$chunk) {

		$chars = str_split($chunk, 1);

		foreach($chars as $c) {

			// Dont consume any escapes or quotes (unless the last seen character is escaping them)
			if($reading) {
				if($lastCharSeen == C_ESCAPE) {
					if($c == QUOTE || $c == SINGLE_QUOTE || $c == C_ESCAPE)
						$string .= $c;
					else
						$string .= C_ESCAPE . $c;
				}
				else if($c != C_ESCAPE && $c != QUOTE)
					$string .= $c;
			}

			// If both the key and value have been discovered store them and reset
			if(strlen($key) > 0 && strlen($value) > 0) {
				$ptr[$key] = $value;
				$key = '';
				$value = '';
				$p = 0;
			}

			// If I haven't seen a comment yet, parse the character
			// Otherwise, ignore all characters until a newline
			if($comment_chars_seen < 2) {

				// Handle the character
				switch($c) {
					case QUOTE:
					if($lastCharSeen != C_ESCAPE) {
						$comment_chars_seen = 0;
						$p++;	// Quote counter
						if($p == 5) $p = 1;
						if($reading) {
							// End parsing string
							$reading = false;

							switch($p) {
								case 2: // Key
									$key = $string;
									$string = '';
								break;
								case 4: // Value
									$value = $string;
									$string = '';
								break;
							}
						}
						else {
							$reading = true;
						}
					}

					break;

					case CURLY_BRACE_BEGIN:
						// Ignore character if we are consuming a key/value
						if(!$reading) {
							$comment_chars_seen = 0;
							if(!(strlen($key)>0)) { print_r($parsed); die("Not properly formed key-value structure"); }

							$ptr[$key] = array();
							$ptr = &$ptr[$key];

							// Keep track of depth via a string path
							if($path == '')
								$path .= $key;
							else
								$path .= PATH_DEPTH_SEPARATOR . $key;

							// Reset for new level
							$string = '';
							$key = '';
							$value = '';
							$p = 0;
						}

					break;

					case CURLY_BRACE_END:
						// Ignore character if we are consuming a key/value
						if(!$reading) {
							$ptr = &$parsed;
							$full_path = explode(PATH_DEPTH_SEPARATOR, $path);
							$new_path = '';
							if(count($full_path) > 0) {
								$i = 0;
								for($i = 0; $i < count($full_path)-1; $i++) {
									if($new_path == '')
										$new_path .= $full_path[$i];
									else
										$new_path .= PATH_DEPTH_SEPARATOR . $full_path[$i];
									$ptr = &$ptr[$full_path[$i]];
								}
							}

							$path = $new_path;
						}

					break;

					case C_COMMENT:
						// Only look for comments if we're not currently reading a key or value
						if(!$reading)
							$comment_chars_seen++;

					break;

					default:
						$comment_chars_seen = 0;

				}

			}

			// Reset comment counter
			if($c == NEW_LINE) {
				$comment_chars_seen = 0;
			}

			$lastCharSeen = $c;
		}
	}

	return $parsed;

}

?>
