<?php
//-----------------------------------------------------------//
// 					HTML for each file category 	         //
//-----------------------------------------------------------//

function categoryTable($array, $parentCat = 'New Category') {
	$html = '';
	$counter = 1;
	$isFileAllowed = false;
	
	// loop through array
	$html .= "<td class=\"tablecat-wrapper\">";
	$html .= 	"<table>";
					foreach ($array as $key => $item) : if ($item) :
						
						// variable creation
						$tableName = $key;
						$parentCatSlug = pageSlug($key);
																	
						// parent category title
						foreach ($item as $file) : if ( $file['isFileAllowed'] == true ) :
							$parentCat = ( $counter == 1 ? "<tr><th colspan=\"5\"><h1>{$parentCat}</h1></th></tr>" : null );
							$counter++;
						endif; endforeach;
						
						// file html
						$html .= $parentCat;
						$html .= "<td class=\"tablecat-wrapper\">";
						$html .= 	"<table>";
						$html .= 		fileTable($item, $tableName);
						$html .= 	"</table>";
						$html .= "</td>";
						
					endif; endforeach;
	$html .= 	"</table>";
	$html .= "</td>";	
				
	return $html; // return our html now that it's ready
}

//-----------------------------------------------------------//
// 				Create a table for each file category        //
//-----------------------------------------------------------//

function fileTable($array, $tableName = 'File Category') {
	$html = '';
	$counter = 1;
	
	// loop through array
	foreach ($array as $item) : if ($item) :
				
		//variable creation
		
		$fileName 		= ucwords($item['fileTitle']);
		$fileSlug 		= pageSlug($item['fileTitle']);
		$fileDate 		= $item['fileModDate'];
		$fileType 		= $item['fileMemeType'];
		$fileDesc 		= $item['fileDescription'];
		$fileURL		= $item['fileURL'];
		$isFileAllowed 	= $item['isFileAllowed'];

		// html
		if ($isFileAllowed == true) : // var is set from outside this function. Default is true
			
			// print file's parent category name
			if ($counter == 1) :
				$html .=	"<tr><th colspan=\"5\" >{$tableName}</th></tr>";
				$counter++;
			endif; 

			// print file html
			$html .= 	"<tr class=\"file-{$fileSlug}\">";
			$html .= 		"<td>{$fileName}</td>";
			$html .= 		"<td>{$fileDate}</td>";
			$html .= 		"<td>{$fileType}</td>";
			$html .= 		"<td>{$fileDesc}</td>";
			$html .= 		"<td><a href=\"{$fileURL}\" title=\"{$fileName}\" download>Download</a></td>";
			$html .= 	"</tr>";
			
		endif;
	
	endif; endforeach;
	
				
	return $html; // return our html now that it's ready
}
