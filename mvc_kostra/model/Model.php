<?php

require_once './model/MainModel.php';

/* Vlasni funkce pro ziskani dat z databaze */

class Model extends MainModel {

    /**
     * Vrati vsechny uzivatlee z databaze
     * @return Vsechny uzivatele ..array
     */
    public function getAllUzivatele() {
        return $this->DBSelectAll("cisnik", "*", array());
    }

    /**
     * Vrati vsechny jidla z databaze
     * @return Vsechny jidla ..array
     */
    public function getAllMenu() {
        return $this->DBSelectAll("menu_jednotlive_polozky", "*", array());
    }

    /**
     * Vytvori novou objednavku prihlaseneho cisnika
     * @param array $data
     */
    public function newObjednavka(array $data) {
        $idstul = $data['stul_id'];
        $idmenu = $data['menu_id'];
        $dataa["stul_id_stul"] = $idstul;
        $dataa["menu_jednotlive_polozky_id_menu"] = $idmenu;
        $dataa["cas_objednani"] = $data['cas'];
        ;
        $this->DBInsert("patri_pokrm_stul", $dataa);
    }

    /**
     * Smaze cisnika
     * @param type $id
     */
    public function smazCisnika($id) {
        $this->connection->query("DELETE FROM cisnik WHERE id_cisnik='$id'");
    }

    /**
     * Vrati objednavky prihlaseneho cisnika
     * @param type $id_c
     * @return type
     */
    public function objednavkyUzitatele($id_c) {
        return $this->connection->query("SELECT stul.*, patri_pokrm_stul.* "
                        . "FROM stul "
                        . "JOIN patri_pokrm_stul ON patri_pokrm_stul.stul_id_stul = stul.id_stul "
                        . "WHERE cisnik_id_cisnik='$id_c'")->fetchAll();
    }

    /**
     * Smaze objdnavku prihlaseneho cisnika 
     * @param type $id
     */
    public function smazObjednavku($id) {
//        echO "fs";
        $this->connection->query("DELETE FROM patri_pokrm_stul WHERE id_obj='$id'");
//        die();
    }

    /**
     * Smaze stul
     * @param type $id
     */
    public function smazStul($id) {
        $this->connection->query("DELETE FROM stul WHERE id_stul='$id'");
    }

    /**
     * Updatuje cisnika
     * @param type $id
     * @param type $data
     */
    public function upravCisnika($id, $data) {
        $query = "UPDATE cisnik SET";
        $values = array();
        foreach ($data as $name => $value) {
            $query .= ' ' . $name . ' = :' . $name . ',';
            $values[':' . $name] = $value;
        }
        $query = substr($query, 0, -1);
        $query .= " WHERE id_cisnik='$id' " . ';';
        $sth = $this->connection->prepare($query);
        $sth->execute($values);
    }

    /**
     * Updatuje stul, az na to ze to nefunguje
     * @param type $id
     * @param type $data
     */
    public function upravStul($id, $data) {
        $query = "UPDATE stul SET";
        $values = array();
        foreach ($data as $name => $value) {
            $query .= ' ' . $name . ' = :' . $name . ',';
            $values[':' . $name] = $value;
        }
        $query = substr($query, 0, -1);
        $query .= " WHERE id_stul='$id' " . ';';
        $sth = $this->connection->prepare($query);
        $sth->execute($values);
    }

    /**
     * Zjisti stoly, ktere obsluhuje prihlaseny cisnik
     * @param type $id
     * @return type
     */
    public function getStolyCisnika($id) {
        return $this->connection->query("SELECT * FROM stul WHERE cisnik_id_cisnik='$id'")->fetchAll();
    }

    /**
     * Zjisti seznam objednavek, ktere ma na starost prihlaseny cisnik
     * @param type $id
     * @return type
     */
    public function objednakyUzivatele($id) {

        //TODO sem to krata posle
        $query = $this->connection->prepare(""
                . "SELECT menu_jednotlive_polozky.*, patri_pokrm_stul* "
                . "FROM stul");
        $sth = $query->execute();
        return $sth->fetchAll();
    }

    /**
     * Vytvori noveho uzivatele
     * @param array $data
     */
    public function registrujUzivatele(array $data) {
        echo "doslo to do regstruj uzivatele";
        $data['heslo'] = $this->hashuj($data['heslo']);
        $this->DBInsert("cisnik", $data);
    }

    public function getMenu($id) {
        return $this->GetConnection()->query("SELECT * FROM menu_jednotlive_polozky WHERE id_menu='$id'")->fetch();
    }

    public function upravitMenu($id, $data) {
        $query = "UPDATE menu_jednotlive_polozky SET";
        $values = array();
        foreach ($data as $name => $value) {
            $query .= ' ' . $name . ' = :' . $name . ',';
            $values[':' . $name] = $value;
        }
        $query = substr($query, 0, -1);
        $query .= " WHERE id_menu='$id' " . ';';
        $sth = $this->connection->prepare($query);
        $sth->execute($values);
    }

    /**
     * Vytvori novy stul danemu cisniku, ale nefunguje
     * @param array $data
     */
    public function pridejStul($stl, $urs) {

        $this->connection->query("UPDATE`stul` SET `cisnik_id_cisnik` = '$urs' WHERE `stul`.`id_stul` = $stl;");
    }

    /**
     * Jednoducha hashovaci funkce na heslo
     * @param type $pw string
     * @return Sting zahashovany string
     */
    public function hashuj($pw) {
        return md5(sha1($pw));
    }

    /**
     * vrati cisnika
     * @param type $id
     * @return type
     */
    public function getCisnik($id) {
        return $this->GetConnection()->query("SELECT * FROM cisnik WHERE id_cisnik='$id'")->fetch();
    }

    public function pridatDoMenu(array $data) {
        $this->DBInsert("menu_jednotlive_polozky", $data);
    }

    public function smazPolozkuMenu($id) {
        $this->connection->query("DELETE FROM menu_jednotlive_polozky WHERE id_menu='$id'");
    }

    /**
     * vrati stul
     * @param type $id
     * @return type
     */
    public function getStul($id) {
        return $this->GetConnection()->query("SELECT * FROM stul WHERE id_stul='$id'")->fetch();
    }

    public function getStoly($id) {
        return $this->connection->query("SELECT * FROM stul WHERE id_stul<>'$id'")->fetchAll();
    }

    /**
     *  Přihlásí uživatele
     * @param array $data data s parametry o novem uzivately
     * @return boolean uzivatele pokud se zdařilo, v opačném případě false
     */
    public function loginUser(array $data) {

        $password = $this->hashuj($data['password']);
        $user = $data['login'];
        $query = $this->GetConnection()->prepare("SELECT * FROM cisnik WHERE login=:login AND heslo=:heslo");
        $query->bindParam(":login", $user, PDO::PARAM_STR);
        $query->bindParam(":heslo", $password, PDO::PARAM_STR);
        $query->execute();
        if ($query === false) {
            var_dump($this->getDb()->errorInfo());
            return false;
        }
        return $query->fetch();
    }

}
