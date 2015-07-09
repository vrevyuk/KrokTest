<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 04.07.15
 * Time: 21:45
 */
include('db.php');

function write2db($con, $catid, $a1, $a2, $a3, $a4, $a5, $success, $question) {
    if($_REQUEST['act'] == 1) {
        $a1 = mysql_real_escape_string($a1);
        $a2 = mysql_real_escape_string($a2);
        $a3 = mysql_real_escape_string($a3);
        $a4 = mysql_real_escape_string($a4);
        $a5 = mysql_real_escape_string($a5);
        $question = mysql_real_escape_string($question);
        $query = "insert into questions values(0, $catid, '$a1', '$a2', '$a3', '$a4', '$a5', $success, '$question')";
        if(!mysql_query($query, $con)) {
            echo $query . '<br>';
        }
    }
}

$con = mysql_connect($host, $user, $passwd);
if(!$con) { die('{"status":false,"message":"Error connection to db"}'); }
mysql_select_db($db);
$query = 'select * from categories';
if($result = mysql_query($query)) {
    echo '<table width="100%"><tr><td style="width: 50%; background-color: #eeeeee" valign="top">';
    echo '<form action="import_internal.php" method="post">';
    echo '<select name="category">';
    while($r = mysql_fetch_assoc($result)) {
        echo '<option value="' . $r['id'] . ($_REQUEST['category']==$r['id']?'" selected':'"') . '>' . $r['catname'] . '</option>';
    }
    echo '</select><br>';
    echo '<textarea name="text" cols="100" rows="30">';  include_once('internal.txt'); echo '</textarea><br>';
    echo '<input type="checkbox" name="act" value="1"> save<br>';
    echo '<input type="submit" value="Calc"></td>';
    echo '<td style="width: 50%; background-color: cadetblue" valign="top">';
    $catid = $_REQUEST['category'];
    $question_text = $answer1 = $answer2 = $answer3 = $answer4 = $answer5 = '';
    $success_answer = '';
    $f = fopen('internal.txt', 'r');
    while(!feof($f)) {
        $line = fgets($f);
        //echo $line . '<br>';

        if(preg_match('/(\*\*\*\))(.+)/i', $line, $r)) {
            //echo '<font color="red">' . $r[2] . '</font><br>';
            $question_text = $r[2];
            $answer1 = $answer2 = $answer3 = $answer4 = $answer5 = '';
        }
        if(preg_match('/(Answer: )([A,B,C,D,E])\*/i', $line, $r)) {
            $success_answer = array_search($r[2], ['A','B','C','D','E']) + 1;
            //echo '<font color="white">' . $success_answer . '</font><br>';
            write2db($con, $catid, $answer1, $answer2, $answer3, $answer4, $answer5, $success_answer, $question_text);
        }
        if(preg_match('/([A,B,C,D,E])\. (.+)/i', $line, $r)) {
            switch($r[1]) {
                case 'A':
                    //echo '<font color="green">' . $r[2] . '</font><br>';
                    $answer1 = $r[2];
                    break;
                case 'B':
                    //echo '<font color="blue">' . $r[2] . '</font><br>';
                    $answer2 = $r[2];
                    break;
                case 'C':
                    //echo '<font color="#ff6347">' . $r[2] . '</font><br>';
                    $answer3 = $r[2];
                    break;
                case 'D':
                    //echo '<font color="fuchsia">' . $r[2] . '</font><br>';
                    $answer4 = $r[2];
                    break;
                case 'E':
                    //echo '<font color="#32cd32">' . $r[2] . '</font><br>';
                    $answer5 = $r[2];
                    break;
            }
        }
    }
    fclose($f);
    echo '</td></tr></table>';
}
mysql_close($con);
?>