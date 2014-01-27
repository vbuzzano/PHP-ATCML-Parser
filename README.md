How to use

require_once("class.atcml.php");
$parser = new ATCML();
$content = $parser->parseContent('52e28e1febb2854aa29eef56_generic.xml');

print_r($content);
