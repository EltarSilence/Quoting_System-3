<?php
$bt = debug_backtrace();
$bt = $bt[0]['file'];

$dir = "";
if(dirname(__FILE__) != dirname($bt)){
	$dir = dirname(find_relative_path($bt, __FILE__)).DIRECTORY_SEPARATOR;
}

Zrequire($dir.'zexarel');

function Zrequire($dir = ""){
	$d = scandir($dir);
	foreach($d as $dd){
		if($dd != ".." && $dd != "."){
			if(is_file($dir."/".$dd)){
				require_once($dir."/".$dd);
			}else if(is_dir($dir."/".$dd)){
				Zrequire($dir."/".$dd);
			}
		}
	}
}
function find_relative_path ($frompath, $topath ) {
    $from = explode( DIRECTORY_SEPARATOR, $frompath ); // Folders/File
    $to = explode( DIRECTORY_SEPARATOR, $topath ); // Folders/File
    $relpath = '';

    $i = 0;
    // Find how far the path is the same
    while ( isset($from[$i]) && isset($to[$i]) ) {
        if ( $from[$i] != $to[$i] ) 
			break;
        $i++;
    }
    $j = count( $from ) - 1;
    // Add '..' until the path is the same
    while ( $i + 1 <= $j ) {
        if ( !empty($from[$j]) )
			$relpath .= '..'.DIRECTORY_SEPARATOR;
        $j--;
    }
    // Go to folder from where it starts differing
    while ( isset($to[$i]) ) {
        if ( !empty($to[$i]) )
			$relpath .= $to[$i].DIRECTORY_SEPARATOR;
        $i++;
    }
    
    // Strip last separator
	
    return substr($relpath, 0, -1);
}
?>