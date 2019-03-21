<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of checkQuery
 *
 * @author root
 */
class checkQuery {
    //put your code here
    
    public  function my_func( $glue, $pieces )
{
	foreach( $pieces as $r_pieces )
	{
    		if( is_array( $r_pieces ) )
    		{
      			$retVal[] = checkQuery::my_func( $glue, $r_pieces );
    		}
    		else	
    		{
      			$retVal[] = $r_pieces;
    		}
  	}
  	return implode( $glue, $retVal );
}
    
}

?>
