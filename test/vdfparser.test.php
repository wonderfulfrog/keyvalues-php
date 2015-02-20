<?php

include("../vdfparser.php");

$kv = VDFParse("keyvalue.txt");

if($kv['KeyValues']['Key1'] != 'Value1')
	die("Key1 != Value1");

if($kv['KeyValues']['Key2'] != '"EscapedValue2"\n\r')
	die("Key2 != \"EscapedValue2\"\n\r");

if($kv['KeyValues']['ComplexKey']['ComplexKey1'] != 'ComplexValue1')
	die("ComplexKey1 != ComplexValue1");

if($kv['KeyValues']['ComplexKey']['ComplexKey2'] != 'ComplexValue2')
	die("ComplexKey1 != ComplexValue1");

if($kv['KeyValues']['ComplexKey']['FurtherComplexity1']['ComplexKey3'] != 'ComplexValue3')
	die("ComplexKey3 != ComplexValue3");

if($kv['KeyValues']['ComplexKey']['FurtherComplexity1']['ComplexKey4'] != 'ComplexValue4')
	die("ComplexKey4 != ComplexValue4");

echo "All tests passed";

?>
