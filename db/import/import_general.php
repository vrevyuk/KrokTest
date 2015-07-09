<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 05.07.15
 * Time: 19:26
 */

include('../db.php');

$includeFile = 'general';

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
    echo '<form action="import_'.$includeFile.'.php" method="post">';
    echo '<select name="category">';
    while($r = mysql_fetch_assoc($result)) {
        echo '<option value="' . $r['id'] . ($_REQUEST['category']==$r['id']?'" selected':'"') . '>' . $r['catname'] . '</option>';
    }
    echo '</select><br>';
    echo '<textarea name="text" cols="100" rows="30">';  include_once($includeFile.'.txt'); echo '</textarea><br>';
    echo '<input type="checkbox" name="act" value="1"> save<br>';
    echo '<input type="submit" value="Calc"></td>';
    echo '<td style="width: 50%; background-color: cadetblue" valign="top">';
    $catid = $_REQUEST['category'];
    $question_text = $answer1 = $answer2 = $answer3 = $answer4 = $answer5 = '';
    $success_answer = '';
    $new_question = 0;
    $f = fopen($includeFile.'.txt', 'r');
    while(!feof($f)) {
        $line = fgets($f);
        //echo $line . '<br>';

        if(preg_match('/(\?\?\?\))(.+)/i', $line, $r) || preg_match('/(\*\*\*\))(.+)/i', $line, $r)) {
            //echo '<font color="red">' . $r[2] . '</font><br>';
            $question_text = $r[2];
            $answer1 = $answer2 = $answer3 = $answer4 = $answer5 = '';
            $new_question = 1;
            continue;
        }
        if(preg_match('/(Answer: )([1,2,3,4,5])\*/i', $line, $r)) {
            $success_answer = $r[2];
            printf("%d, %s, %s, %s, %s, %s, %d, %s<hr>", $catid, $answer1, $answer2, $answer3, $answer4, $answer5, $success_answer, $question_text);
            write2db($con, $catid, $answer1, $answer2, $answer3, $answer4, $answer5, $success_answer, $question_text);
        }
        if(preg_match('/(Answer: )([A,B,C,D,E])\*/i', $line, $r)) {
            $success_answer = array_search($r[2], ['A','B','C','D','E']) + 1;
            printf("%d, %s, %s, %s, %s, %s, %d, %s<hr>", $catid, $answer1, $answer2, $answer3, $answer4, $answer5, $success_answer, $question_text);
            write2db($con, $catid, $answer1, $answer2, $answer3, $answer4, $answer5, $success_answer, $question_text);
        }

        if($new_question > 0) {
            switch($new_question) {
                case 1:
                    //echo '<font color="green">' . $r[2] . '</font><br>';
                    if(preg_match('/^(A. )(.+)/i', $line, $res)) {
                        $answer1 = str_replace('A. ', '', $line);
                    } else if(preg_match('/^(1- )(.+)/i', $line, $res)) {
                        $answer1 = str_replace('1- ', '', $line);
                    } else {
                        $answer1 = $line;
                    }
                    $new_question++;
                    break;
                case 2:
                    //echo '<font color="blue">' . $r[2] . '</font><br>';
                    if(preg_match('/^(B. )(.+)/i', $line, $res)) {
                        $answer2 = str_replace('B. ', '', $line);
                    } else if(preg_match('/^(2- )(.+)/i', $line, $res)) {
                        $answer2 = str_replace('2- ', '', $line);
                    } else {
                        $answer2 = $line;
                    }
                    $new_question++;
                    break;
                case 3:
                    //echo '<font color="#ff6347">' . $r[2] . '</font><br>';
                    if(preg_match('/^(C. )(.+)/i', $line, $res)) {
                        $answer3 = str_replace('C. ', '', $line);
                    } else if(preg_match('/^(3- )(.+)/i', $line, $res)) {
                        $answer3 = str_replace('3- ', '', $line);
                    } else {
                        $answer3 = $line;
                    }
                    $new_question++;
                    break;
                case 4:
                    //echo '<font color="fuchsia">' . $r[2] . '</font><br>';
                    if(preg_match('/^(D. )(.+)/i', $line, $res)) {
                        $answer4 = str_replace('D. ', '', $line);
                    } else if(preg_match('/^(4- )(.+)/i', $line, $res)) {
                        $answer4 = str_replace('4- ', '', $line);
                    } else {
                        $answer4 = $line;
                    }
                    $new_question++;
                    break;
                case 5:
                    //echo '<font color="#32cd32">' . $r[2] . '</font><br>';
                    if(preg_match('/^(E. )(.+)/i', $line, $res)) {
                        $answer5 = str_replace('E. ', '', $line);
                    } else if(preg_match('/^(5- )(.+)/i', $line, $res)) {
                        $answer5 = str_replace('5- ', '', $line);
                    } else {
                        $answer5 = $line;
                    }
                    $new_question = 0;
                    break;
            }
        }
    }
    fclose($f);
    echo '</td></tr></table>';
}
mysql_close($con);
?>