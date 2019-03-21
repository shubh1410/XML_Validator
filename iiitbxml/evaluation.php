<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'checkQuery.php';
/**
 * Description of evaluation
 *
 * @author root
 */
class evaluation {

    //put your code here

    public function evaluate_response($response, $answer, $dbname) {


        $db_hostname = get_string('db_hostname', 'qtype_iiitbsql');
        $db_database = $dbname;
        $db_username = get_string('db_username', 'qtype_iiitbsql');
        $db_password = get_string('db_password', 'qtype_iiitbsql');
        
        try{
        $con = new PDO("mysql:host=$db_hostname;dbname=$db_database", $db_username, $db_password);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query1 = rtrim($response,';');
        $query2 = rtrim($answer,';');
        $sth = $con->query($query1);
        $row = $sth->fetchAll();
        $sth2 = $con->query($query2);
        $row2 = $sth2->fetchAll();
        
        $n = new checkQuery();
        
        $irow = $n->my_func(',', $row);
        $irow2 = $n->my_func(',', $row2);

        if ($irow == $irow2) {

            return 1;
        } else {
            return 0;
        }
        }  catch (Exception $e){
            
            return 0;
        }
    }
    
 
}

?>
