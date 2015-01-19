<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$data = array(
    "col1" => "val1",
    "col2" => "val2",
    "col3" => "val3",
    "col4" => "val4",
);

include_once './DAO/DataDAO.php';
$dao = new DataDAO();
$dao->insert($data);