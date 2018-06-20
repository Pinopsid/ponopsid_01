<?php
 /* Установка внутренней кодировки в UTF-8 */
    mb_internal_encoding("UTF-8");

    header("Content-Type: text/html; charset=utf-8", true);

    include_once( "ModelSaver.php" );
    include_once( "Connection.php" );

		//Проверяем пустые данные или нет
		if(!empty($_FILES))
		{
           //echo $_FILES['uploadfile']['name']."<br/><br/>";

           $FileLines = array();
 
           $fp = fopen($_FILES['uploadfile']['tmp_name'], 'rb');
           while ( ($line = fgets($fp)) !== false) {
              array_push($FileLines, $line);
              //echo $line."<br>";
           }

           $SimplexMethodModel = new SimplexMethodModel();
           $SimplexMethodModel->Fill($FileLines);


           $Connection = new Connection();
           $Connection->Connect();

           $SimplexMethodModel->SimplexMethodModelId = 1;
           $sqlText = "SELECT Max(SimplexMethodModelId) as SimplexMethodModelId FROM SimplexMethodModel;";
           if ($res = mysqli_query($Connection->cnn, $sqlText))
               if ($sqlRow = mysqli_fetch_assoc($res))
                  $SimplexMethodModel->SimplexMethodModelName = "Model".$sqlRow["SimplexMethodModelId"];

           $sqlText="INSERT INTO SimplexMethodModel (SimplexMethodModelName) VALUES ('".$SimplexMethodModel->SimplexMethodModelName."')";
           mysqli_query($Connection->cnn, $sqlText);

           $sqlText = "SELECT Max(SimplexMethodModelId) as SimplexMethodModelId FROM SimplexMethodModel;";
           if ($res = mysqli_query($Connection->cnn, $sqlText))
               if ($sqlRow = mysqli_fetch_assoc($res))
                  $SimplexMethodModel->SimplexMethodModelId = $sqlRow["SimplexMethodModelId"];

           $count=count($SimplexMethodModel->VariableNameList);
           for ($j=0; $j< $count; $j++) {
              echo $SimplexMethodModel->VariableNameList[$j]." ";
              $sqlText="INSERT INTO Variable (VariableName, SimplexMethodModelId) VALUES ('".$SimplexMethodModel->VariableNameList[$j]."', ".$SimplexMethodModel->SimplexMethodModelId.")";
              mysqli_query($Connection->cnn, $sqlText);
              echo $sqlText."<br>";
           }
           echo "<br/>";

           $count=count($SimplexMethodModel->ObjectiveFunction->ExpressionTermList);
           echo "ExpressionTermList Length ".$count."<br>";

           if($SimplexMethodModel->ObjectiveFunction->ObjectiveFunctionType==1) echo "min: ";
           if($SimplexMethodModel->ObjectiveFunction->ObjectiveFunctionType==2) echo "max: ";

           $sqlText="INSERT INTO ExpressionTermList () VALUES ()";
           mysqli_query($Connection->cnn, $sqlText);

           $sqlText = "SELECT Max(ExpressionTermListId) as ExpressionTermListId FROM ExpressionTermList;";
           if ($res = mysqli_query($Connection->cnn, $sqlText))
               if ($sqlRow = mysqli_fetch_assoc($res))
                  $SimplexMethodModel->ObjectiveFunction->ExpressionTermListId = $sqlRow["ExpressionTermListId"];

           $sqlText="INSERT INTO ObjectiveFunction (ObjectiveFunctionType, ExpressionTermListId, SimplexMethodModelId) VALUES (".$SimplexMethodModel->ObjectiveFunction->ObjectiveFunctionType.", ".
              $SimplexMethodModel->ObjectiveFunction->ExpressionTermListId.", ".$SimplexMethodModel->SimplexMethodModelId.")";
           mysqli_query($Connection->cnn, $sqlText);
        
           for ($i=0; $i< $count; $i++) {
              $ExpressionTerm = $SimplexMethodModel->ObjectiveFunction->ExpressionTermList[$i];
              echo " ".$ExpressionTerm->CoefficientValue." ".$ExpressionTerm->VariableName;
              $sqlText = "SELECT VariableId FROM Variable WHERE VariableName='".$ExpressionTerm->VariableName."' AND SimplexMethodModelId=".$SimplexMethodModel->SimplexMethodModelId.";";
              if ($res = mysqli_query($Connection->cnn, $sqlText))
                 if ($sqlRow = mysqli_fetch_assoc($res))
                    $VariableId = $sqlRow["VariableId"];
              
$             $sqlText="INSERT INTO ExpressionTerm (CoefficientValue, VariableId, ExpressionTermListId) VALUES (".$ExpressionTerm->CoefficientValue.",".$VariableId.", ".$SimplexMethodModel->ObjectiveFunction->ExpressionTermListId.")";
              echo $sqlText."<br>";
              mysqli_query($Connection->cnn, $sqlText);
           }
           echo "<br/>";

           $count=count($SimplexMethodModel->LinearConstraintList);
           echo "LinearConstraintList Length ".$count."<br>";
           for ($j=0; $j< $count; $j++) {
             $LinearConstraint=$SimplexMethodModel->LinearConstraintList[$j];

             
           $sqlText="INSERT INTO ExpressionTermList () VALUES ()";
           mysqli_query($Connection->cnn, $sqlText);

           $sqlText = "SELECT Max(ExpressionTermListId) as ExpressionTermListId FROM ExpressionTermList;";
           if ($res = mysqli_query($Connection->cnn, $sqlText))
               if ($sqlRow = mysqli_fetch_assoc($res))
                  $LinearConstraint->ExpressionTermListId = $sqlRow["ExpressionTermListId"];

             $sqlText="INSERT INTO LinearConstraint (CoefficientValue, LogicalOperation, SimplexMethodModelId, ExpressionTermListId) VALUES (".$LinearConstraint->CoefficientValue.", '".
             $LinearConstraint->LogicalOperation."', ".$SimplexMethodModel->SimplexMethodModelId.", ".$LinearConstraint->ExpressionTermListId.")";
             mysqli_query($Connection->cnn, $sqlText);

              $sqlText = "SELECT Max(LinearConstraintId) as LinearConstraintId FROM LinearConstraint;";
              if ($res = mysqli_query($Connection->cnn, $sqlText))
                 if ($sqlRow = mysqli_fetch_assoc($res))
                    $LinearConstraintId = $sqlRow["LinearConstraintId"];


           $sqlText="INSERT INTO ExpressionTermList () VALUES ()";
           mysqli_query($Connection->cnn, $sqlText);


             $count1=count($LinearConstraint->ExpressionTermList);
             for ($i=0; $i< $count1; $i++) {
                $ExpressionTerm = $LinearConstraint->ExpressionTermList[$i];
                echo $ExpressionTerm->CoefficientValue." ".$ExpressionTerm->VariableName." ";

              $sqlText = "SELECT VariableId FROM Variable WHERE VariableName='".$ExpressionTerm->VariableName."' AND SimplexMethodModelId=".$SimplexMethodModel->SimplexMethodModelId.";";
                if ($res = mysqli_query($Connection->cnn, $sqlText))
                   if ($sqlRow = mysqli_fetch_assoc($res))
                      $VariableId = $sqlRow["VariableId"];
               
                $sqlText="INSERT INTO ExpressionTerm (CoefficientValue, VariableId, ExpressionTermListId) VALUES (".$ExpressionTerm->CoefficientValue.",".$VariableId.", ".$LinearConstraint->ExpressionTermListId.")";
                mysqli_query($Connection->cnn, $sqlText);

             }
             echo $LinearConstraint->LogicalOperation." ".$LinearConstraint->CoefficientValue."<br>";
           }
       } else 
           echo "empty<br/>";
?>