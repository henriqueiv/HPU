<?php

/**
 * @author Henrique Valcanaia
 */
interface IDAO {

    public function load($fields = "*", $add = "");

    public function insert($fields);

    public function update($fields, $where = null);

    public function delete($where = null);
}
