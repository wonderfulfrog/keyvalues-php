<?php

include("../vdfparser.php");

$kv = VDFParse("keyvalue.txt");

if($kv['KeyValues']['Key1'] != 'Value1')
	die("Key1 != Value1\n");

if($kv['KeyValues']['Key2'] != '"EscapedValue2"\n\r')
	die("Key2 != \"EscapedValue2\"\n\r");

if($kv['KeyValues']['ComplexKey']['ComplexKey1'] != 'ComplexValue1')
	die("ComplexKey1 != ComplexValue1\n");

if($kv['KeyValues']['ComplexKey']['ComplexKey2'] != 'ComplexValue2')
	die("ComplexKey1 != ComplexValue1\n");

if($kv['KeyValues']['ComplexKey']['FurtherComplexity1']['ComplexKey3'] != 'ComplexValue3')
	die("ComplexKey3 != ComplexValue3\n");

if($kv['KeyValues']['ComplexKey']['FurtherComplexity1']['ComplexKey4'] != 'ComplexValue4')
	die("ComplexKey4 != ComplexValue4\n");

if($kv['KeyValues']['ComplexKey']['ValueWithCurlyBraces'] != 'Value { I have special chars }')
	die("ValueWithCurlyBraces != Value { I have special chars }\n");

echo "All tests passed\n";

?>
