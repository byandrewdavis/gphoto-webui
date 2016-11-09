<?php
require_once("CameraRaw.php");



//time gphoto2 --quiet --capture-image-and-download --filename "./images/capture-%Y%m%d-%H%M%S-%03n.%C"
//exec ("gphoto2 --set-config uilock=1",$output);
//echo join("\n",$output);
//exec ("gphoto2  --capture-image",$output);
//echo join("\n",$output);
//exec ("gphoto2 --set-config uilock=1",$output);
//echo join("\n",$output);

$action = '';

if (isset($_GET['action'])){
	$action = $_GET['action'];
}


$returnObj;

try{
	switch($action){
		case "shutdown":	
			exec ("sudo shutdown -P now",$output);
			echo json_encode(true);					
			break;

		case "setOwner":	
			$ownerName = $_GET['ownerName'];
			$ownerName = strtoupper($ownerName);
			$ownerName = str_replace(' ', '-', $ownerName);

			exec ("gphoto2 --set-config=/main/settings/ownername=.$ownerName.",$output);
			echo json_encode(true);					
			break;
		case "setArtist":	
			$artistName = $_GET['artistName'];
			$artistName = strtoupper($artistName);
			$artistName = str_replace(' ', '-', $artistName);

			exec ("gphoto2 --set-config=/main/settings/artist=.$artistName.",$output);
			echo json_encode(true);					
			break;

		case "takePicture":
			exec ("gphoto2 --capture-image-and-download --filename \"./images/capture-%Y%m%d-%H%M%S-%03n.%C\"",$output);
			echo json_encode(true);					
			break;
	
		case "deleteFile":
			$file = $_GET['file'];
			$path_parts = pathinfo('images/'.$file);
			unlink('images/'.$file);				
			unlink('images/thumbs/'.$path_parts['basename'].'.jpg');				
			header('Content-Type: application/json');
			echo json_encode(true);					
			break;
			
		case "getImage":	
			$file = $_GET['file'];
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$file.'"');
			header('Content-Length: '.filesize('images/'.$file));
			$fp = fopen('images/'.$file, 'rb');
			fpassthru($fp);
			exit;
			break;
		
		
		case "getCamera":

			exec ("gphoto2 --auto-detect", $output);
			$returnObj->camera = trim(explode("usb", $output[count($output) - 1])[0]);
			header('Content-Type: application/json');
			echo json_encode($returnObj);
	
			break;

		case "getOwner":

			exec ("gphoto2 --auto-detect --get-config=/main/settings/ownername", $output);
			$output = explode('Current',$output[5]);
			$replaceMe = array(":", ".", " ");
			$returnObj->owner = trim(str_replace($replaceMe, "", "$output[1]"));;
			header('Content-Type: application/json');
			echo json_encode($returnObj);
			break;
		case "getArtist":
			exec ("gphoto2 --auto-detect --get-config=/main/settings/artist", $output);
			$output = explode('Current',$output[5]);
			$replaceMe = array(":", ".", " ");
			$returnObj->artist = trim(str_replace($replaceMe, "", "$output[1]"));;
			header('Content-Type: application/json');
			echo json_encode($returnObj);
			break;

		
		case "getImages":
	
			$files = array();
			$imageDir = opendir('images');
			while (($file = readdir($imageDir)) !== false) {			
				if(!is_dir('images/'.$file)){
					$path_parts = pathinfo('images/'.$file);				
					if (!file_exists('images/thumbs/'.$path_parts['basename'].'.jpg')){
						try { //try to extract the preview image from the RAW
							CameraRaw::extractPreview('images/'.$file, 'images/thumbs/'.$path_parts['basename'].'.jpg');
						} catch (Exception $e) { //else resize the image...
							$im = new Imagick('images/'.$file);
							$im->setImageFormat('jpg');
							$im->scaleImage(1024,0);					
							$im->writeImage('images/thumbs/'.$path_parts['basename'].'jpg');
							$im->clear();
							$im->destroy();
						}
					}				
					$returnFile;
					$returnFile->name = $path_parts['basename'];
					$returnFile->sourcePath = 'images/'.$file;
					$returnFile->thumbPath = 'images/thumbs/'.$path_parts['basename'].'.jpg';
				
					array_push($files,$returnFile);
				
					unset($returnFile);
				}
			}
			closedir($imageDir);
			$returnObj = $files;
			header('Content-Type: application/json');
			echo json_encode($returnObj);
			break;
		default:
			break;
	}
} catch (Exception $e) { //else resize the image...
	
}

?>