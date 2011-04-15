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
set_time_limit(0);
ini_set('memory_limit','64M');

require("config.php");
require("../trainer.php");

$trainer = new trainer;


$db = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS);
mysql_select_db(MYSQL_DB,$db);

/* loading previus learn */
echo "<h1>Loading previous learn</h1>";flush();
$query = mysql_query("select belongs,ngram,repite from knowledge_base",$db);
$previouslearn = array();
while ( $row = mysql_fetch_array($query) )
    $previouslearn[$row['belongs']][$row['ngram']] = $row['repite'];
mysql_free_result($query);
$trainer->setPreviousLearn($previouslearn);

/* traine */
echo "<h1>Training</h1>";flush();
$query = mysql_query("select * from examples",$db);
// $sql=mysql_query("select comment_content as text,comment_approved as state from wp_comments",$db);
$sql=mysql_query("select text,state from examples",$db);
echo "<h2>Loading examples</h2>";flush();
while ( $row = mysql_fetch_array($query) ){
    $text = $row['text'];
    $text = strip_tags($text);
    $trainer->add_example($text,$row['state']);
}
mysql_free_result($query);

/* learn */
echo "<h2>Learning</h2>";flush();
$trainer->extractPatterns();

/* save what is learned */
echo "<h1>Saving learning</h1>";flush();
foreach ($trainer->knowledge as $tipo => $v) {
    foreach($v as $k => $y) {
        $k = addslashes($k);
        $sql = "replace knowledge_base values('$k','$tipo','".$y['cant']."','".$y['bayesian']."')";
        mysql_query($sql,$db) or die(mysql_error($db).":".$sql);
    }
}
echo "<h1>Optimizing database</h1>";flush();

mysql_query("create temporary table opttable as 
select ngram, count(*) total, min(percent) as nmin, max(percent) as nmax
from knowledge_base group by ngram having count(ngram) > 1",$db);

mysql_query("delete from knowledge_base where ngram in (select ngram from opttable where (nmax-nmin) < 0.30)",$db); 


?>