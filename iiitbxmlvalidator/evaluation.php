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
class evaluation {

    //put your code here

    public function evaluate_response($response, $answer) {

        try{

		$sxe = simplexml_load_string($response);
		if (!$sxe) {
		 return 0;
		}
		$dom_sxe = dom_import_simplexml($sxe);
		$dom = new DOMDocument('1.0');
		$dom_sxe = $dom->importNode($dom_sxe, true);
		$dom_sxe = $dom->appendChild($dom_sxe);
		$dom->saveXML();

		$schema = $answer;

		if ($dom->schemaValidateSource($schema)) {
		   return 1;
		} else {
		   return 0;
		}
	}catch(Exception $e){
		    	return 0;
		    }
	}
}
?>
