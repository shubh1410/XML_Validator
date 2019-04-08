<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of evaluation
 *
 * @author root
 */
class evaluation_xquery {

    //put your code here

    public function evaluate_response($response, $answer, $dbname) {


      try{
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


        $query_ans = $answer;
        $query_res = $response;


      $entries_ans = $xpath->query($query_ans);
      $entries_res = $xpath->query($query_res);
      $ans="";
      $res="";
      foreach ($entries_ans as $entry) {
          $ans.=$entry->nodeValue;
}
foreach ($entries_res as $entry) {
    $res.=$entry->nodeValue;
}
if($ans==$res){
  return 1;
}
else{
  return 0;
}
}catch(Exception $e){
        return 0;
      }
}


}

?>
