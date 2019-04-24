<?php



// We don't want to bother with white spaces
//$doc->preserveWhiteSpace = false;

/*$doc->load('books.xml');
if($doc)
{echo "doc loaded\n";}*/
/*
$dbname=<<<XML


<bookstore>

<book category="COOKING">
  <title lang="en">Everyday Italian</title>
  <author>Giada De Laurentiis</author>
  <year>2005</year>
  <price>30.00</price>
</book>

<book category="CHILDREN">
  <title lang="en">Harry Potter</title>
  <author>J K. Rowling</author>
  <year>2005</year>
  <price>29.99</price>
</book>

<book category="WEB">
  <title lang="en">XQuery Kick Start</title>
  <author>James McGovern</author>
  <author>Per Bothner</author>
  <author>Kurt Cagle</author>
  <author>James Linn</author>
  <author>Vaidyanathan Nagarajan</author>
  <year>2003</year>
  <price>49.99</price>
</book>

<book category="WEB">
  <title lang="en">Learning XML</title>
  <author>Erik T. Ray</author>
  <year>2003</year>
  <price>39.95</price>
</book>

</bookstore>
XML;
$doc = new DOMDocument;
$sxe = simplexml_load_string($dbname);
  if (!$sxe) {
   return 0;
  }
  $dom_sxe = dom_import_simplexml($sxe);
  $dom = new DOMDocument('1.0');
  $dom_sxe = $dom->importNode($dom_sxe, true);
  $dom_sxe = $dom->appendChild($dom_sxe);
  $dom->saveXML();
  $xpath = new DOMXPath($dom);
  $query = 'for $x in /bookstore/book
where $x/price>30
return $x/title';
$query1 = '/bookstore/book';


$entries = $xpath->query($query);
$entries1 = $xpath->query($query1);
$ans="";
$res="";
foreach ($entries as $entry) {
    $ans.=$entry->nodeValue;
	echo $entry->nodeValue;
}
echo "\n";
foreach ($entries1 as $entry) {
    $res.=$entry->nodeValue;
	echo $entry->nodeValue;
}
if($ans==$res){
	echo "Correct answer";
}
else{
echo "wrong answer";
}*/
//exec("javac XQueryTester.java", $output);
exec("java -cp .:\* XQueryTester", $output);
echo "string".$output[0];
?>
