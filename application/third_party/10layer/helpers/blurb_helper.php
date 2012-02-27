<?php

//this function takes a piece of text and shortens it to the required length in words

function clean_blurb($text,$len)
{
        $string = "";
        if(strlen($text) > 0){
        	if(strlen($text) > $len+1)
        	{
                $pos = strpos(strip_tags($text), ' ', $len);
                $string =  substr(strip_tags($text), 0, $pos)."...";
        	}
        	else
        	{
                $string = strip_tags($text);
        	}
        	
        }else{
        	$string = "Blank content title";
        }
        
        return $string;	
        
}

?>