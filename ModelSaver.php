<?php

 
  class ExpressionTerm {
     var $ExpressionTermId;
     var $VariableName;
     var $CoefficientValue;
  }

  class ObjectiveFunction {
     var $ObjectiveFunctionId;
     var $ObjectiveFunctionType;
     var $ExpressionTermListId;
     var $ExpressionTermList;
  }

  class LinearConstraint {
     var $CoefficientValue;
     var $LogicalOperation;
     var $ExpressionTermListId;
     var $ExpressionTermList;
  }

  class SimplexMethodModel {
     var $SimplexMethodModelId;
     var $SimplexMethodModelName = '';

     var $ObjectiveFunctionId;
     var $ObjectiveFunction;

     var $LinearConstraintList;
     var $VariableNameList;
    
     var $FileLines = '';

     function Fill($FileLines){
        $this->FileLines = $FileLines;

        $this->LinearConstraintList = array();
        $this->VariableNameList = array();
        
        $eol1=false;
        $start=0;
        for ($i=0; $i < count($FileLines); $i++) {
           $str = $FileLines[$i];

           $pos_comm = strpos($str, "/*");
           $items = explode(" ", $str);

           if($pos_comm === false && count($items)>2 ) {

               if(strpos($str, "min:")!== false || strpos($str, "max:")!== false
               || !$eol1){

                  if(strpos($str, "min:")!== false || strpos($str, "max:")!== false){

                    $this->ObjectiveFunction = new ObjectiveFunction();
                    $this->ObjectiveFunction->ExpressionTermList = array();
                    if(strpos($str, "min:")!== false) $this->ObjectiveFunction->ObjectiveFunctionType=1; else 
                                                       $this->ObjectiveFunction->ObjectiveFunctionType=2;
  
                    $items1 = explode(":", $str);
                     //echo $items1[0]." ";
                    //echo $items1[1]." ";

                    $items = explode(" ", $items1[1]);
                    //echo $i." ".count($items)." ".$str."<br>";

                         //echo $items1[0]."<br>";
                         //echo count($items)."<br>";
                  } 

                 $k=0;
                 for ($j=0; $j < count($items); $j++) {
                     $item0 = $items[$j];
                     if(trim($item0) != ""){
                        if($k==0){
                           $item1 = $items[$j];
                           $k=1;
                            //echo $j." ".$item1."<br>";
                        } else {
                            $item2 = $items[$j];
                           //echo $j." ".$item2."<br>";
                           $pos = strpos($item2, ";");
                           if($pos !== false) {
                               $item2 = substr($item2, 0, $pos); 
                               $eol1=true;
                           }
                           $term = new ExpressionTerm();
                           $term->CoefficientValue = $item1;
                           $term->VariableName = $item2;
                           array_push($this->ObjectiveFunction->ExpressionTermList, $term);
                           $item_Index = array_search($item2, $this->VariableNameList);
                           if($item_Index == false) array_push($this->VariableNameList, $item2);
                          $k=0;
                        }   
                        if($eol1) break;
                     } 
                 }
                 //echo "<br>";
               }
           } 
           if(strpos($str, ";")!== false) {
              $start=$i+1;
              break;
           }
       }
       
     //echo "start=".$start."<br>";

       for ($i=$start; $i < count($FileLines); $i++) {
         $str = $FileLines[$i];

         $pos_comm = strpos($str, "/*");
         $items = explode(" ", $str);

         if($pos_comm === false && count($items)>2 ) {

                $items = explode(" ", $str);
                $LinearConstraint = new LinearConstraint();
                $LinearConstraint->ExpressionTermList = array();

                 $k=0;
                 for ($j=0; $j < count($items); $j++) {
                     $item0 = $items[$j];
                     if(trim($item0) != ""){
                       if($item0 == "<"
                       || $item0 == ">"
                       || $item0 == "<="
                       || $item0 == ">="
                       || $item0 == "="
                       || $item0 == "!="
                       || $item0 == "<>"){

                           $LinearConstraint->LogicalOperation=$item0;
                           $item0=$items[$j+1];
                           $pos = strpos($item0, ";");
                           if($pos!== false) {
                               $item0 = substr($item0, 0, $pos); 
                           }
                           $LinearConstraint->CoefficientValue=$item0;
                           array_push($this->LinearConstraintList,$LinearConstraint);
                          //$count=count($SimplexMethodModel->LinearConstraintList);
                           //echo " ".$i." ".$count=".<br>";
                           break;
                        } else 

                        if($k==0){
                           $item1 = $items[$j];
                           if(is_numeric($item1))
                              $k=1;
                            else {
                              if(strpos($item1, "+")!== false) $item1=1;
                              if(strpos($item1, "-")!== false) $item1=-1;
                              $item2 =  substr($items[$j],1);

                              $term = new ExpressionTerm();
                              $term->CoefficientValue = $item1;
                              $term->VariableName = $item2;
                              array_push($LinearConstraint->ExpressionTermList, $term);
                              $item_Index = array_search($item2, $this->VariableNameList);
                              if($item_Index == false) array_push($this->VariableNameList, $item2);

                            }
                            //echo $j." ".$item1." ";
                         } else {
                            $item2 = $items[$j];
                           //echo $j." ".$item2."<br>";
                           $pos = strpos($item2, ";");
                           if($pos !== false) {
                               $item2 = substr($item2, 0, $pos); 
                           }

                           $term = new ExpressionTerm();
                           $term->CoefficientValue = $item1;
                           $term->VariableName = $item2;
                           array_push($LinearConstraint->ExpressionTermList, $term);
                           $item_Index = array_search($item2, $this->VariableNameList);
                           if($item_Index == false) array_push($this->VariableNameList, $item2);

                           $k=0;
                         }   
                     } 
                 }
         }
       }
     }
  }
?> 