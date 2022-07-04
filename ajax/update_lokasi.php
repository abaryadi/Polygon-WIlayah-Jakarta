<?php
$id_operator = $_POST['id'];
$lat = $_POST['lat'];
$long = $_POST['long'];

$jsonString = file_get_contents('../operator.json');
$data = json_decode($jsonString, true);

$operator = $data['operator'];

$i = 1;
foreach ($operator as $key => $value) {


    if($value['idOperator'] == $id_operator){

        $data['operator'][$key]['coordinate'] = [$lat, $long];
    }

}

$newJsonString = json_encode($data);
file_put_contents('../operator.json', $newJsonString);
?>