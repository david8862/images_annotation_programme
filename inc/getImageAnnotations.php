<?php

include 'xmlVocReadAnnotationsFile.php';
include 'configuration.php';

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

$image = $_POST["img"];
$folder = $_POST["flr"];
$year = $_POST["yr"];
$file = 'file.log';
file_put_contents($file, "INFO - getImageAnnotations.php\n");
$url = $DATASET_ROOT_WEB_DIR."/".$year . "/" . $folder . "/" . $image;

# Remove extension
$id = str_replace(array(".jpg",".JPG"),".jpg", $image);

# Get the xml file, replace .jpg by xml
$xml_filename = str_replace(array(".jpg",".JPG"), ".xml", $id);

# Try to find the annotation
$xml_filepath = getXmlFile($ANNOTATIONS_DIR, $xml_filename);

if ($xml_filepath != null)
{
    #echo "xml_filepath" . $xml_filepath;
    $annotations = [];
    $xml = new xmlVocReadAnnotationsFile($xml_filepath);

    file_put_contents($file, "xml_filepath ".$xml_filepath."\n",FILE_APPEND | LOCK_EX);

    if (!$xml->hasError())
    {
        file_put_contents($file, "Parse XML\n",FILE_APPEND | LOCK_EX);
        $xml->parseXML();
        if (!$xml->hasError())
        {
            $annotations = $xml->getAnnotations();
            file_put_contents($file, "Annotations ".serialize($annotations)."\n",FILE_APPEND | LOCK_EX);
        }
    }
    else
    {
        file_put_contents($file, "An error occurs\n",FILE_APPEND | LOCK_EX);
        $annotations = [];
    }
}
else
{
    file_put_contents($file, "No annotations found.\n",FILE_APPEND | LOCK_EX);
    $annotations = [];
}

file_put_contents($file, "Annotations ".serialize($annotations)."\n",FILE_APPEND | LOCK_EX);
file_put_contents($file, "URL image = ".$url."\n",FILE_APPEND | LOCK_EX);

# Prepare message to send
$data =  $annotations;
header('Content-Type: application/json');
echo json_encode($data);
?>
