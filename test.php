<?php 
$output = file_get_contents('./sample.txt', true);



$output = explode ('Current',$output);
$replaceMe = array(":", ".", " ");
$output=trim(str_replace($replaceMe, "", "$output[1]"));
	print_r ($output);


?>