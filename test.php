<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  </head>
  <body>
<?
require_once("class.atcmlparser.php");
$parser = new ATCMLParser();
echo "<pre>";
print_r($parser->parseContent('atcml/52e28e1febb2854aa29eef56_generic.xml'));
echo "</pre>";
?>
  </body>
</html>