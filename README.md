#How to use

<?php  
require_once("class.atcmlparser.php");  
$parser = new ATCML();  
$content = $parser->parseContent('52e28e1febb2854aa29eef56_generic.xml');  

print_r($content);  
?>
