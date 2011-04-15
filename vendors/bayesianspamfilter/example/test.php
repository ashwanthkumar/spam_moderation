<?php
/*
***************************************************************************
*   Copyright (C) 2007 by Cesar D. Rodas                                  *
*   cesar@sixdegrees.com.br                                               *
*                                                                         *
*   Permission is hereby granted, free of charge, to any person obtaining *
*   a copy of this software and associated documentation files (the       *
*   "Software"), to deal in the Software without restriction, including   *
*   without limitation the rights to use, copy, modify, merge, publish,   *
*   distribute, sublicense, and/or sell copies of the Software, and to    *
*   permit persons to whom the Software is furnished to do so, subject to *
*   the following conditions:                                             *
*                                                                         *
*   The above copyright notice and this permission notice shall be        *
*   included in all copies or substantial portions of the Software.       *
*                                                                         *
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       *
*   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    *
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.*
*   IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     *
*   OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, *
*   ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR *
*   OTHER DEALINGS IN THE SOFTWARE.                                       *
***************************************************************************
*/ 
require("../spam.php");
require("config.php");
$db = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS);
mysql_select_db(MYSQL_DB,$db);
/**
 *
 *    Because the system do not manage a method where you 
 *    can save the data, you must define a function which recives
 *    the wanted "n-grams" and return and array which is 
 *    "n-grams" and percent of accuracy (what its learn with example_trainer).
 *    In this example those datas are loaded from mysql.
 *
 */
$spam = new spam("handler");
/**/
$sql=mysql_query("select * from examples",$db);
echo "<h1>Spam test</h1>";
print '<table cellpadding="5" cellspacing="0" width="100%">';
echo "<tr align='center'>";
echo "<td><h1>Text</h1></td>";
echo "<td><h1>Expected</h1></td>";
echo "<td><h1>New Algorithm</h1></td>";
echo "<td><h1>Old Algorithm</h1></td>";
echo "</tr>";
$i=0;
while ($text=mysql_fetch_array($sql)){
    $score1 = number_format($spam->isItSpam_v2($text[0],'spam'),2);
    $score2 = number_format( $spam->isItSpam($text[0],'spam'),2);
    echo "<tr bgcolor='".(++$i%2 == 0 ? 'white' : '#c0c0c0')."'>";
    echo "<td width=50%><em><strong>$text[0]</strong></em></td>";
    echo "<td width=10%>".($text[1] == 1 ? "ham" : "spam" )."</td>";
    echo "<td width=10%>".($score1 > 60 ? "spam" : "ham")." ($score1 of spam)%</td>";
    echo "<td width=10%>".($score2 > 60 ? "spam" : "ham")." ($score2  of spam)%</td>";
    echo "</tr>";
}
print "</table>";

/**
 *  Callback function
 *
 *  This is function is called by the classifier class, and it must 
 *  return all the n-grams.
 *  
 *  @param Array $ngrams N-grams.
 *  @param String $type Type of set to compare
 */
function handler($ngrams,$type) {
    global $db;
    
    $info = array_keys($ngrams);
    
    $sql = "select ngram,percent from knowledge_base where belongs = '$type' && ngram in ('".implode("','",$info)."')";
    $r = mysql_query($sql,$db);
    
    while ( $row = mysql_fetch_array($r) ) {
        $t[ $row['ngram'] ]  = $row['percent'];     
    }

    return $t;
}
?>