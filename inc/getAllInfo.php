<?php

include 'xmlVocReadAnnotationsFile.php';
include 'configuration.php';
//$service_requested = $_GET["info"];

# Search the xml file in a $dir
function getXmlFile($dir, $filename)
{
    $xml_filepath = null;
    $files = scandir($dir);
    $results = null;

    foreach($files as $key => $value)
    {
        if ( strcasecmp($value, $filename) == 0 )
        {
            $xml_filepath = $dir.DIRECTORY_SEPARATOR.$filename;
            return $xml_filepath;
        }
    }

    return $xml_filepath;
}

# List of images to process
$list_of_images = array();
# Index of images
$image_index = 0;
$annotated_image_index = 0;
$not_annotated_image_index = 0;

$file = 'file.log';
file_put_contents($file, "INFO - allInfo Start the loop\n");
$it = new RecursiveDirectoryIterator($IMAGES_DIR);
foreach(new RecursiveIteratorIterator($it) as $file)
{
    # Process file
    #if ( (strpos(strtoupper($file), '.JPG') !== false) && (strstr($file, $COLLECTION_NAME)) )
    if (strpos(strtoupper($file), '.JPG') !== false)
    {
        # echo $file . "<br>";
        $delimiter = "/";
        $item = explode($delimiter, $file);
        $nbItems = count($item);
        # Should be like "VOC2007/JPEGImages/000001.jpg"
        if ($nbItems >= 3)
        {
            $image_name = $item[$nbItems-1];
            $folder = $item[$nbItems-2];
            $year = $item[$nbItems-3];
            $url = $DATASET_ROOT_WEB_DIR."/".$year . "/" . $folder . "/" . $image_name;
            $id = str_replace(array(".jpg",".JPG"),".jpg", $image_name);
            
            # Try to find the annotation
            // $xml_filename = str_replace(array(".jpg",".JPG"), ".xml", $id);
            // $xml_filepath = getXmlFile($ANNOTATIONS_DIR, $xml_filename);
            // $annotations = [];
            // if ($xml_filepath != null)
            // {
            //     #echo "xml_filepath" . $xml_filepath;
            //     $annotations = [];
            //     $xml = new xmlVocReadAnnotationsFile($xml_filepath);
            //     file_put_contents($file, "xml_filepath ".$xml_filepath."\n",FILE_APPEND | LOCK_EX);

            //     if (!$xml->hasError())
            //     {
            //         file_put_contents($file, "Parse XML\n",FILE_APPEND | LOCK_EX);
            //         $xml->parseXML();
            //         if (!$xml->hasError())
            //         {
            //             $annotations = $xml->getAnnotations();
            //             file_put_contents($file, "Annotations ".serialize($annotations)."\n",FILE_APPEND | LOCK_EX);
            //         }
            //     }
            //     else
            //     {
            //         file_put_contents($file, "An error occurs\n",FILE_APPEND | LOCK_EX);
            //     }
            // }
            // else
            // {
            //     file_put_contents($file, "No annotations found.\n",FILE_APPEND | LOCK_EX); 
            // }

            $image_info = array("year" => $year, "folder" => $folder, "url" => $url, "id" => $id);

            # Add the image in the list
            $list_of_images[$image_index] = $image_info;
            $image_index = $image_index + 1;
        }
    }
}
header('Content-Type: application/json');
echo json_encode($list_of_images);
?>
