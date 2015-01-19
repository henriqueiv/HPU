<?php

/**
 * @author Henrique Valcanaia
 */
include_once '../DAOBase/DAO.php';

class DataDAO extends DAO {

    const TABLE = "DATA";

    public function __construct() {
        $this->setTable(self::TABLE);
    }

    public function insert($array) {
        /*
         * Example demostrating an override method that
         * returns the id of the last inserted object considering 
         * that the db is using AUTO_INCREMENT
         */
        parent::insert($array);
        $data = parent::load("MAX(ID) AS ID");
        return $data[0]["ID"];
    }

}
