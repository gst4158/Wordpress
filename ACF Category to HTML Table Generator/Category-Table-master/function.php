<?php

//------------------------------------------------------------------//
// Variables for all category functions
//------------------------------------------------------------------//
		
	$postFiles = array();
	foreach ( $flexcontent as $item ) {
		
		// Get parent category and set it as a variable for filtering
		$parentCat 	= $item['flex_parentcat']->name;
		
		
		// File repeater
		$fileArray = array(); // reset for each file
		$repeater = $item['flex_repeater'];
		foreach ( $repeater as $file ) : if ($file) :
			
			// check for file image sizes
			$fileSizes = ( isset($file['flex_repeater_file']['sizes']) ? $file['flex_repeater_file']['sizes'] : null );
			
			// file category
			$fileCategorySlug 	= $file['flex_repeater_tax']->slug;
			$fileCategoryName 	= $file['flex_repeater_tax']->name;
			
			
			// user role - remove empty keys b/c WP puts one for some reason
			$userRoles_files 	= $file['flex_repeater_userrole']; 

				// add administrator role to all files
				if(($key = array_search('administrator', $userRoles_files)) !== false) {unset($userRoles_files[$key]);} // remove administrator role incase it's set
				$userRoles_files[] 	= array_push($userRoles_files, 'administrator'); // add administrator to every file
				array_pop($userRoles_files); // remove numerical key the array_push function creates
				
				// remove empty keys and reset key numbers
				$needle = null; $key = array_search($needle,$userRoles_files);
				if($key!==false) 	unset($userRoles_files[$key]); 		// remove empty keys
				$userRoles_files 	= array_values($userRoles_files);	// reset array key numbers
			
			// determines if file is allowed to print to page
			$currentUserRole	= $user_roles; // set outside of this file
			$allowedFiles		= array_intersect($userRoles_files, $currentUserRole);
			$isFileAllowed 		= ( $allowedFiles ? true : false );
		
			// create file array
			$theFile = array(
				// file parts
				'fileID'			=> $file['flex_repeater_file']['ID'],
				'fileTitle'			=> $file['flex_repeater_file']['title'],
				'fileFileName'		=> $file['flex_repeater_file']['filename'],
				'fileURL'			=> $file['flex_repeater_file']['url'],
				'fileAlt'			=> $file['flex_repeater_file']['alt'],
				'fileAuthor'		=> $file['flex_repeater_file']['author'],
				'fileDescription'	=> $file['flex_repeater_file']['description'],
				'fileCaption'		=> $file['flex_repeater_file']['caption'],
				'fileSlug'			=> $file['flex_repeater_file']['name'],
				'fileUploadDate'	=> $file['flex_repeater_file']['date'],
				'fileModDate'		=> $file['flex_repeater_file']['modified'],
				'fileMemeType'		=> $file['flex_repeater_file']['mime_type'],
				'fileFileType'		=> $file['flex_repeater_file']['type'],
				'fileIcon'			=> $file['flex_repeater_file']['icon'],
				'fileSizes'			=> $fileSizes,
				
				// file category
				'fileParentCatName'		=> $parentCat,
				'fileCategorySlug'		=> $fileCategorySlug,
				'fileCategoryName'		=> $fileCategoryName,
				
				// user roles
				'fileUserroles'			=> $userRoles_files,
				'currentUserRole'		=> $user_roles,
				'isFileAllowed'			=> $isFileAllowed,
			);
		
			// load all file data into an array sorted by file categories
			
			$fileArray[$parentCat][$fileCategoryName][] = $theFile;
			
		endif; endforeach;

		// put post files into parent category arrays
		$postFiles[] = $fileArray;
		$mergedPostFiles = call_user_func_array('array_merge_recursive', $postFiles); // merges parent categories incase someone created multiple inside a single post
	};	
