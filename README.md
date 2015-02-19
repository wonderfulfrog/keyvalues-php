PHP Valve Data Format Parser
============================

Information
-----------

Returns a VDF Key-Value file as an associative array in PHP.


Usage
-----

Include the file 'vdfparser.php' where the parsing is required.  

```
include('vdfparser.php');
```

Then call the `VDFParse()` function to parse the file.

```
$kv = VDFParse("some_file_to_parse.txt");
```

An example test is included in the `test` directory.

License
-------

This project is licensed under the terms of the [MIT License](http://opensource.org/licenses/MIT).
