<?php

   /* ��������� ���������� ��������� � UTF-8 */
  mb_internal_encoding("UTF-8");

  include_once( "Connection.php" );

  class SimplexMethodModel {
    var $SimplexMethodModelId;
    var $SimplexMethodModelName = '';
    var $ModelText = '';

    var $Connection;

    function Fill($SimplexMethodModelId){
          if(!$this->Connection->cnn){
             $this->ModelText = "����������� ���������� � ����� ������";
             return;
          }
		  /*����� ������ ��� ��������*/
          $this->SimplexMethodModelId = $SimplexMethodModelId;
          $sqlText = "SELECT SimplexMethodModelName FROM SimplexMethodModel 
		  WHERE SimplexMethodModelId=".$SimplexMethodModelId.";";
          $this->ModelText = $sqlText;
          if ($res = mysqli_query($this->Connection->cnn, $sqlText)) {
             if ($sqlRow = mysqli_fetch_assoc($res)){
                $this->SimplexMethodModelName = $sqlRow["SimplexMethodModelName"];
				/*������� ������� �������*/
                $this->ModelText = '/* ������� ������� */'.'\n';
				/*������� ���������� ������� �������*/
                $sqlText = "SELECT ObjectiveFunctionType, ExpressionTermListId FROM ObjectiveFunction 
							WHERE SimplexMethodModelId=".$SimplexMethodModelId.";";
                if ($res = mysqli_query($this->Connection->cnn, $sqlText)) {
                   if ($sqlRow = mysqli_fetch_assoc($res)){
                      $ExpressionTermListId = $sqlRow["ExpressionTermListId"];
                      $ObjectiveFunctionType = $sqlRow["ObjectiveFunctionType"];
                      if($ObjectiveFunctionType==1) $this->ModelText .= 'min:'; else
                      if($ObjectiveFunctionType==2) $this->ModelText .= 'max:';
					/*������� ������������� � ���������� ������� �������*/
                      $sqlText = "SELECT ExpressionTermId, VariableName, CoefficientValue FROM ExpressionTerm 
								  INNER JOIN Variable ON ExpressionTerm.VariableId=Variable.VariableId 
								   WHERE ExpressionTermListId=".$ExpressionTermListId." ORDER BY ExpressionTerm.SortOrder;";
                      if ($res = mysqli_query($this->Connection->cnn, $sqlText)) {
                         while ($sqlRow = mysqli_fetch_assoc($res)){ 
                            if($sqlRow["CoefficientValue"]<0)
                               $this->ModelText .= ' -'.$sqlRow["CoefficientValue"]; 
                            else
                               $this->ModelText .= ' +'.$sqlRow["CoefficientValue"]; 
                            $this->ModelText .= ' '.$sqlRow["VariableName"]; 
                         }
                       } else {
                          $this->ModelText ="������: ".mysqli_error($this->Connection->cnn).'\n';
                       }
                       $this->ModelText .= ';'.'\n\n';
                   }
                } else {
                  $this->ModelText ="������: ".mysqli_error($this->Connection->cnn).'\n';
                }
				/*������� �����������*/
                $this->ModelText .= '/* ����������� */'.'\n';
                $sqlText = "SELECT CoefficientValue, LogicalOperation, ExpressionTermListId FROM LinearConstraint WHERE SimplexMethodModelId=".$SimplexMethodModelId." ORDER BY SortOrder;";
                if ($resConst = mysqli_query($this->Connection->cnn, $sqlText)) {
                   while ($sqlConstRow = mysqli_fetch_assoc($resConst)){
                      $ExpressionTermListId = $sqlConstRow["ExpressionTermListId"];
                      $CoefficientValue = $sqlConstRow["CoefficientValue"];
                      $LogicalOperation = $sqlConstRow["LogicalOperation"];
               
                      $sqlText = "SELECT ExpressionTermId, VariableName, CoefficientValue FROM ExpressionTerm INNER JOIN Variable ON ExpressionTerm.VariableId=Variable.VariableId WHERE ExpressionTermListId=".$ExpressionTermListId." ORDER BY ExpressionTerm.SortOrder;";
                      if ($res = mysqli_query($this->Connection->cnn, $sqlText)) {
                         $first=true;
                         while( $sqlRow = mysqli_fetch_assoc($res)){ 
                            $space='';
                            if(!$first) $space=' ';
                            $first=false;
                            if($sqlRow["CoefficientValue"]<0)
                               $this->ModelText .= $space.'-'.$sqlRow["CoefficientValue"]; 
                            else
                               $this->ModelText .= $space.'+'.$sqlRow["CoefficientValue"]; 
                            $this->ModelText .= ' '.$sqlRow["VariableName"]; 
                         }
                         $this->ModelText .= ' '.$LogicalOperation;
                         $this->ModelText .= ' '.$CoefficientValue;
                         $this->ModelText .= ';'.'\n';
                       } else {
                          $this->ModelText ="������: ".mysqli_error($this->Connection->cnn).'\n';
                       }
                   } 
                } else {
                   $this->ModelText ="������: ".mysqli_error($this->Connection->cnn).'\n';
                }
             } else {
               $this->ModelText ="������: ".mysqli_error($this->Connection->cnn).'\n';
             }
          } else {
              $this->ModelText ="������: ".mysqli_error($this->Connection->cnn).'\n';
          }
    }
  }
?>