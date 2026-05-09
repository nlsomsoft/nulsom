<?php
function GetValue($str , $name) 
{
    $pos1 = 0;  //length의 시작 위치
    $pos2 = 0;  //:의 위치

    while( $pos1 <= strlen($str) )
    {
        $pos2 = strpos( $str , ":" , $pos1);
        $len = (int)substr($str , $pos1 , $pos2 - $pos1);

        $key = substr($str , $pos2 + 1 , $len);
        $pos1 = $pos2 + $len + 1;
        if( $key == $name )
        {
            $pos2 = strpos( $str , ":" , $pos1);
            $len = substr($str , $pos1 , $pos2 - $pos1);
            $value = substr($str , $pos2 + 1 , $len);
            return $value;
        }
        else
        {
            // 다르면 스킵한다.
            $pos2 = strpos( $str , ":" , $pos1);
            $len = substr($str , $pos1 , $pos2 - $pos1);
            $pos1 = $pos2 + $len + 1;
        }            
    }
}