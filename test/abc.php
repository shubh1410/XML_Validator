<?php


$xml_feed = <<<XML
<catalog><buildtime>2002-05-30T09:30:10.5</buildtime><book id="bk101"><author>Gambardella, Matthew</author><title>XML Developer Guide</title><genre>Computer</genre><price>44.95</price><publish_date>2000-10-01</publish_date><description>An in-depth look at creating applications
      with XML.</description></book><book id="bk102"><author>Ralls, Kim</author><title>Midnight Rain</title><genre>Fantasy</genre><price>5.95</price><publish_date>2000-12-16</publish_date><description>A former architect battles corporate zombies,
      an evil sorceress, and her own childhood to become queen
      of the world.</description></book></catalog>
XML;
$schema = <<<XML
<xs:schema attributeFormDefault="unqualified" elementFormDefault="qualified"><xs:element name="catalog"><xs:complexType><xs:sequence><xs:element type="xs:dateTime" name="buildtime"/><xs:element name="book" maxOccurs="unbounded" minOccurs="0"><xs:complexType><xs:sequence><xs:element type="xs:string" name="author"/><xs:element type="xs:string" name="title"/><xs:element type="xs:string" name="genre"/><xs:element type="xs:float" name="price"/><xs:element type="xs:date" name="publish_date"/><xs:element type="xs:string" name="description"/></xs:sequence><xs:attribute type="xs:string" name="id" use="optional"/></xs:complexType></xs:element></xs:sequence></xs:complexType></xs:element></xs:schema>
XML;
try{
$doc=simplexml_load_string($xml_feed);
$doc1=simplexml_load_string($schema); // load xml
$xml=new DOMDocument();
$xsd=new DOMDocument();
$xml=dom_import_simplexml($doc);
$xsd=dom_import_simplexml($doc1);
if ($xml->schemaValidate($xsd)) {
    echo 'valid';
    exit;
}
else{
echo 'invalid';
}
}catch(Exception e){
echo 'e';
}


?>
