<?php
error_reporting(0);
$string = <<<XML
<?xml version="1.0"?>
<bills xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.iiitb.ac.in/bills bill.xsd"
  xmlns="http://www.iiitb.ac.in/bills">
  <bill id="1234">
    <amount>111</amount>
    <bill_item code="abc12x">
      <item_name>jeans</item_name>
      <item_category>A</item_category>
      <quantity>4</quantity>
      <discount>20.2</discount>
    </bill_item>
    <bill_item code="a5812x">
      <item_name>tshirt</item_name>
      <item_category>B</item_category>
      <quantity>1</quantity>
    </bill_item>
  </bill>
  <bill id="2321">
    <amount>198.2</amount>
    <bill_item code="gfc12x">
      <item_name>tshirt</item_name>
      <item_category>D</item_category>
      <quantity>2</quantity>
      <discount>3.2</discount>
    </bill_item>
    <bill_item code="ga712x">
      <item_name>hoodie</item_name>
      <item_category>C</item_category>
      <quantity>7</quantity>
      <discount>31.2</discount>
    </bill_item>
  </bill>
</bills>
XML;


$answer=<<<XML
<?xml version="1.0"?>
<schema xmlns="http://www.w3.org/2001/XMLSchema"
  xmlns:target="http://www.iiitb.ac.in/bills"
  targetNamespace="http://www.iiitb.ac.in/bills"
  elementFormDefault="qualified">
  <element name="bills">
    <complexType>
      <sequence>
        <element name="bill" minOccurs="1" maxOccurs="unbounded">
          <complexType>
            <sequence>
              <element name="amount">
                <simpleType>
                  <restriction base="decimal">
                    <fractionDigits value="2"/>
                  </restriction>
                </simpleType>
              </element>
              <element name="bill_item" minOccurs="1" maxOccurs="unbounded">
                <complexType>
                  <sequence>
                    <element name="item_name">
                      <simpleType>
                        <restriction base="string">
                          <maxLength value="20"/>
                        </restriction>
                      </simpleType>
                    </element>
                    <element name="item_category">
                      <simpleType>
                        <restriction base="string">
                          <enumeration value="A"/>
                          <enumeration value="B"/>
                          <enumeration value="C"/>
                          <enumeration value="D"/>
                        </restriction>
                      </simpleType>
                    </element>
                    <element name="quantity">
                      <simpleType>
                        <restriction base="integer">
                          <minInclusive value="1"/>
                          <maxInclusive value="99"/>
                        </restriction>
                      </simpleType>
                    </element>
                    <element name="discount" minOccurs="0">
                      <simpleType>
                        <restriction base="decimal">
                          <minInclusive value="1"/>
                          <maxInclusive value="100"/>
                        </restriction>
                      </simpleType>
                    </element>
                  </sequence>
                  <attribute name="code">
                    <simpleType>
                      <restriction base="string">
                        <pattern value="([a-zA-z0-9])+"/>
                        <minLength value="6"/>
                        <maxLength value="6"/>
                      </restriction>
                    </simpleType>
                  </attribute>
                </complexType>
              </element>
            </sequence>
            <attribute name="id" type="decimal"/>
    </complexType>
  </element>
</sequence>
</complexType>
</element>
</schema>
XML;
/*libxml_use_internal_errors(true);

$doc = simplexml_load_string($string);
$xml = explode("\n", $string);

if (!$doc) {
echo "error\n";
}
else 
{
echo "\nvalid";
}
*/

/*
libxml_use_internal_errors(true);
$string=$response;
$doc = simplexml_load_string($string);
$xml = explode("\n", $string);

if (!$doc) {

return 0;
}
else 
{
return 1;
}

*/
$sxe = simplexml_load_string($string);
if(!$sxe)
{
echo "error";
}
$dom_sxe = dom_import_simplexml($sxe);
$dom = new DOMDocument('1.0');
$dom_sxe = $dom->importNode($dom_sxe, true);
$dom_sxe = $dom->appendChild($dom_sxe);
$dom->saveXML();

$schema = $answer;


//$ab = new DOMDocument();
//$ab->load($file);

if ($dom->schemaValidateSource($schema)) {
   echo "true";

} else {
   echo "false";
}





/*
$schema = 'test.xsd';


$ab = new DOMDocument();
$ab->load($file);

if ($ab->schemaValidate($schema)) {
    print "$file is valid.\n";
} else {
    print "$file is invalid.\n";
}*/
?>
