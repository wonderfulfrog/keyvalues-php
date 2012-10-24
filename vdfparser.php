<?php

/*

	* VDF (Valve Data Format) file parser
	* author: devinwl
	* version: 1.0

*/

define("QUOTE", "\"");
define("CURLY_BRACE_BEGIN", "{");
define("CURLY_BRACE_END", "}");
define("NEW_LINE", "\n");
define("C_RETURN", "\r");
define("C_TAB", "\t");

function VDFParse($filename) {
	$parsed = array();
	$ptr = &$parsed;		
	$path = '';				// The current level represented as a string
	$p = 0;					// How many quotes have been seen
	$string = "";			// The string of consumed characters
	$key = "";				// The discovered key
	$value = "";			// The discovered corresponding value
	$reading = false;		// Currently consuming characters

	$filecontent = file_get_contents($filename);

	// Strip all comments before parsing
	$filecontent = preg_replace('!//.*!', '', $filecontent);

	// Begin parsing by character
	$chars = preg_split('//', $filecontent, -1, PREG_SPLIT_NO_EMPTY);

	foreach($chars as $c) {

		// Consume characters provided they are not special characters
		if($reading) {
			if($c != NEW_LINE && $c != C_RETURN && $c != QUOTE)
				$string .= $c;
		}

		// If both the key and value have been discovered store them and reset
		if(strlen($key) > 0 && strlen($value) > 0) {
			$ptr[$key] = $value;
			$key = '';
			$value = '';
			$p = 0;
		}

		// Handle the character
		switch($c) {
			case QUOTE:
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

			break;

			case CURLY_BRACE_BEGIN:
				$comment_chars_seen = 0;
				if(!(strlen($key)>0)) die("Not properly formed key-value structure" . print_r($parsed));

				$ptr[$key] = array();
				$ptr = &$ptr[$key];

				// Keep track of depth via a string path
				if($path == '')
					$path .= $key;
				else
					$path .= '.' . $key;

				// Reset for new level
				$string = '';
				$key = '';
				$value = '';
				$p = 0;

			break;

			case CURLY_BRACE_END:
				$ptr = &$parsed;
				$full_path = explode(".", $path);
				$new_path = '';
				if(count($full_path) > 0) {
					$i = 0;
					for($i = 0; $i < count($full_path)-1; $i++) {
						if($new_path == '')
							$new_path .= $full_path[$i];
						else
							$new_path .= '.' . $full_path[$i];
						$ptr = &$ptr[$full_path[$i]];
					}
				}

				$path = $new_path;

			break;

			default: 
				$comment_chars_seen = 0;

		}
	}

	return $parsed;

}

?>