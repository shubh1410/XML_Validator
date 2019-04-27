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
class evaluation_xmlwellform {

    //put your code here

    public function evaluate_response($response, $answer) {

        try{

		$sxe = simplexml_load_string($response);
		if (!$sxe) {
		 return 0;
		} else {
		 return 1;
		}
	}catch(Exception $e){
		    	return 0;
		    }
	}
}
?>
