<?php 
 include 'configuration.php';
 $file = 'file.log';
 $image = $_POST["img"];
 $id = str_replace(array(".jpg",".JPG"),".jpg", $image);
 $xml_filename = str_replace(array(".jpg",".JPG"), ".xml", $id);
 if(is_file($ANNOTATIONS_DIR."/".$xml_filename)) { 
  if(unlink($ANNOTATIONS_DIR."/".$xml_filename)) { 
   file_put_contents($file,  $ANNOTATIONS_DIR."/".$xml_filename." 删除成功"."\n",FILE_APPEND | LOCK_EX);
  } else { 
   file_put_contents($file,  $ANNOTATIONS_DIR."/".$xml_filename." 删除失败"."\n",FILE_APPEND | LOCK_EX);
  } 
 } else { 
   file_put_contents($file,  $ANNOTATIONS_DIR."/".$xml_filename." 文件不存在"."\n",FILE_APPEND | LOCK_EX);
 } 

 $wholeid = $IMAGES_DIR."/".$id;
 $wholeidnew = $IMAGES_BACKUP_DIR."/".$id;
 if(is_file($wholeid)) { 
    copy($wholeid, $wholeidnew);
    if(unlink($wholeid)) { 
     file_put_contents($file,  $wholeid." 删除成功"."\n",FILE_APPEND | LOCK_EX);
    } else { 
     file_put_contents($file,  $wholeid." 删除失败"."\n",FILE_APPEND | LOCK_EX);
    } 
   } 
 else { 
    file_put_contents($file,  $wholeid." 文件不存在"."\n",FILE_APPEND | LOCK_EX);
 }
 $response_array['status']  = 'success';
 $response_array['message'] = $wholeid. " copied to the backup directory and the corresponding xml file deleted";
 header('Content-type: application/json');
 echo json_encode($response_array);

?>