# What
The purpose of this PHP Class is to provide an example and a help to parse an ATCML file to process an ATCNA newswire.

#How to use

<?php  
require_once("class.atcmlparser.php");  
$parser = new ATCML();  
$content = $parser->parseContent('52e28e1febb2854aa29eef56_generic.xml');  

print_r($content);  
?>
