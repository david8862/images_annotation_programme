<?php
$testGD = get_extension_funcs("gd"); // Grab function list 
if (!$testGD){
    echo "GD not even installed.";
    exit;
}
include 'xmlVocAnnotations.php';
include 'configuration.php';

$obj = json_decode($_POST["sendInfo"]);

$file = 'file.log';
file_put_contents($file, "INFO - Synthesis of last submit\n");
file_put_contents($file, date('l jS \of F Y h:i:s A')."\n",FILE_APPEND | LOCK_EX);
file_put_contents($file, serialize($obj)."\n",FILE_APPEND | LOCK_EX);

$folder = $obj->folder;
$id     = $obj->id;
$width  = $obj->width;
$height = $obj->height;

$annotations = $obj->{'annotations'};

file_put_contents($file, "Annotations = ".sizeof($annotations)."\n",FILE_APPEND | LOCK_EX);

$imageSize = [  "width"  => $width ,
    "height" => $height,
    "depth"  => 3 ];

$xml = new xmlVocAnnotations($folder, $id, $imageSize);

file_put_contents($file, "xmlVocAnnotations created\n",FILE_APPEND | LOCK_EX);
$im = imagecreatefromjpeg($IMAGES_DIR. DIRECTORY_SEPARATOR .$id);
$im_tmp = imagecreatefromjpeg($IMAGES_DIR. DIRECTORY_SEPARATOR .$id);
$blackPatch = imagecolorallocate($im, 0, 0, 0);
$blackPatchAdded = false;
foreach ($annotations as &$annotation)
{
    if($annotation->tag == 'black')
    {
        imagefilledrectangle($im, $annotation->x, $annotation->y, $annotation->x+$annotation->width-1, $annotation->y+$annotation->height-1, $blackPatch);
        $blackPatchAdded = true;
    }
    else
    {
        $xml->addBndBox($annotation->x,
            $annotation->y,
            $annotation->width,
            $annotation->height,
            $annotation->tag,
            $annotation->difficult,
            $annotation->groupof);
    }
}
if($blackPatchAdded)
{
    imagejpeg($im_tmp, $IMAGES_BACKUP_DIR. DIRECTORY_SEPARATOR .$id);
    imagejpeg($im, $IMAGES_DIR. DIRECTORY_SEPARATOR .$id);
}
file_put_contents($file, "Before saving\n",FILE_APPEND | LOCK_EX);
// Write xml to file
$xml->save($ANNOTATIONS_DIR);

$response_array['status']  = 'success'; /* match error string in jquery if/else */
if($blackPatchAdded){
    $response_array['message'] = $id. " and " .str_replace(strrchr($id, "."),"",$id).".xml have been created.";
}
else{
    $response_array['message'] = str_replace(strrchr($id, "."),"",$id).".xml has been created.";
}

file_put_contents($file, "End of file validationTagsAndRegions" ,FILE_APPEND | LOCK_EX);
file_put_contents($file, " " ,FILE_APPEND | LOCK_EX);
file_put_contents($file, " " ,FILE_APPEND | LOCK_EX);

header('Content-type: application/json');
echo json_encode($response_array);

?>
