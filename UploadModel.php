<?php
 /* Установка внутренней кодировки в UTF-8 */
    mb_internal_encoding("UTF-8");

    include_once( "Connection.php" );
    include_once( "SimplexMethodModel.php" );

    $SimplexMethodModel = new SimplexMethodModel();
    $SimplexMethodModel->Connection = new Connection();
    $SimplexMethodModel->Connection->Connect();
    $SimplexMethodModel->Fill($_REQUEST['SimplexMethodModelId']);

    //header("Content-Type: text/plain; charset=utf-8");
    header("Content-Type: text/plain; charset=windows-1251");
    header("Content-Disposition: attachment; filename="."SimplexMethodModel.lp");

   if(!$SimplexMethodModel->Connection->cnn)
       echo $SimplexMethodModel->Connection->error;
    else{
       $pieces = explode('\n', $SimplexMethodModel->ModelText);
       foreach($pieces as $part) {
          echo $part."\r\n";
      }
    }
?>