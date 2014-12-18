<?php
/*nastaveni propojeni s databazi*/
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_DATABASE_NAME', 'mydb');
define('DB_USER_LOGIN', 'root');
define('DB_USER_PASSWORD', '');

/**
 * V mainModelu jsou funkce pro praci s DB ze cviceni
 */
class MainModel {

    public $connection = null;

    public function __construct() {
        $this->Connect();
    }

    public function __destruct() {
        $this->Disconnect();
    }

    /**
     * Nacist 1 zaznamu z tabulky
     *
     * @param string $table_name - jméno tabulky
     * @param string $select_columns_string - jména sloupců oddělené čárkami, nebo jiné příkazy SQL
     * @param array $where_array - seznam podmínek<br/>
     * [] - column = sloupec; value - int nebo string nebo value_mysql = now(); symbol
     * @param string $limit_string - doplnit limit string
     */
    public function DBSelectOne($table_name, $select_columns_string, $where_array, $limit_string = "") {
       
        $mysql_pdo_error = false;

     
        $where_pom = "";

        if ($where_array != null)
            foreach ($where_array as $index => $item) {
                
                if ($where_pom != "")
                    $where_pom .= "AND ";

                $column = $item["column"];
                $symbol = $item["symbol"];

                if (key_exists("value", $item))
                    $value_pom = "?";       
                else if (key_exists("value_mysql", $item))
                    $value_pom = $item["value_mysql"];   


                $where_pom .= "`$column` $symbol  $value_pom ";
            }

        
        if (trim($where_pom) != "")
            $where_pom = "where $where_pom";

        
        $query = "select $select_columns_string from `" . $table_name . "` $where_pom $limit_string;";
       
        $statement = $this->connection->prepare($query);

   
        $bind_param_number = 1;

        if ($where_array != null)
            foreach ($where_array as $index => $item) {
                if (key_exists("value", $item)) {
                    $value = $item["value"];
                    

                    $statement->bindValue($bind_param_number, $value);  
                    $bind_param_number ++;
                }
            }

       
        $statement->execute();

       
        $errors = $statement->errorInfo();
        

        if ($errors[0] + 0 > 0) {
         
            $mysql_pdo_error = true;
        }

        if ($mysql_pdo_error == false) {
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return $row;
        } else {
            echo "Chyba v dotazu - PDOStatement::errorInfo(): ";
            printr($errors);
            echo "SQL dotaz: $query";
        }
    }

    /**
     * Nacist vsechny zaznamy z tabulky.
     * Poznamka: tato metoda je stejna jako DBSelectOne - lisi se to jen posledni casti Fetch vs FetchAll
     *
     * @param unknown_type $table_name
     * @param unknown_type $select_columns_string
     * @param unknown_type $where_array
     * @param unknown_type $limit_string
     * @param array			$order_by_array - pouze pole stringu: [0] => {[column] = "", [sort] => "DESC"}
     * @return mixed
     */
    public function DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string = "", $order_by_array = array()) {
        // PDO - MySQL
        //echo "metoda DBSelectAll"; 
        //printr($this->connection);
        //exit;
        // vznik chyby v PDO
        $mysql_pdo_error = false;

    
        $where_pom = "";

        if ($where_array != null)
            foreach ($where_array as $index => $item) {
                // pridat AND
                if ($where_pom != "")
                    $where_pom .= "AND ";

                // pokud neexistuje klic column, tak preskocit
                if (!key_exists("column", $item)) {
                    echo "asi chyba v metode DBSelectAll - chybi klic column <br/>";
                    continue;
                }

                $column = $item["column"];     
                $symbol = $item["symbol"];

                if (key_exists("value", $item))
                    $value_pom = "?";      
                else if (key_exists("value_mysql", $item))
                    $value_pom = $item["value_mysql"];   // je to systemove, vlozit rovnou - POZOR na SQL injection, tady to muze projit



                    

                $where_pom .= "`$column` $symbol  $value_pom ";
            }

        if (trim($where_pom) != "")
            $where_pom = "where $where_pom";

        $order_by_pom = "";

        if ($order_by_array != null)
            foreach ($order_by_array as $index => $item) {
                $column = $item["column"];
                $sort = $item["sort"];

                // carku pred
                if (trim($order_by_pom != null))
                    $order_by_pom .= ", ";

                $order_by_pom .= "`$column` $sort";
            }

     
        if (trim($order_by_pom) != "")
            $order_by_pom = "order by $order_by_pom";


    
        $query = "select $select_columns_string from `" . $table_name . "` $where_pom $order_by_pom $limit_string;";
       
        $statement = $this->connection->prepare($query);

       
        $bind_param_number = 1;

        if ($where_array != null)
            foreach ($where_array as $index => $item) {
                if (key_exists("value", $item)) {
                    $value = $item["value"];
                   

                    $statement->bindValue($bind_param_number, $value);  // vzdy musim dat value, abych si nesparoval promennou (to nechci)
                    $bind_param_number ++;
                }
            }

        
        $statement->execute();

        $errors = $statement->errorInfo();
       
        if ($errors[0] + 0 > 0) {
          
            $mysql_pdo_error = true;
        }

       
        if ($mysql_pdo_error == false) {
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $rows;
        } else {
            echo "Chyba v dotazu - PDOStatement::errorInfo(): ";
         
            echo "chyba";
            echo "SQL dotaz: $query";
        }
    }

    /**
     * 
     * Pridat polozku do DB - zakladni verze bez mysl fci typu now().
     * @param unknown_type $table_name
     * @param array $item - musi byt ve stejne podobe jako DB.
     * 
     * */
    public function DBInsert($table_name, $item) {
        // MySql
        $mysql_pdo_error = false;

      
        $insert_columns = "";
        $insert_values = "";

        if ($item != null)
            foreach ($item as $column => $value) {
                    if ($insert_columns != "")
                    $insert_columns .= ", ";
                if ($insert_columns != "")
                    $insert_values .= ", ";

                $insert_columns .= "`$column`";
                $insert_values .= "?";
            }

        
        $query = "insert into `$table_name` ($insert_columns) values ($insert_values);";

      
        $statement = $this->connection->prepare($query);

            $bind_param_number = 1;

        if ($item != null)
            foreach ($item as $column => $value) {
                $statement->bindValue($bind_param_number, $value);  // vzdy musim dat value, abych si nesparoval promennou (to nechci)
                $bind_param_number ++;
            }

      
        $statement->execute();

       
        $errors = $statement->errorInfo();
        

        if ($errors[0] + 0 > 0) {
            // nalezena chyba
            $mysql_pdo_error = true;
        }

        // 6) nacist ID vlozeneho zaznamu a vratit
        if ($mysql_pdo_error == false) {
            $item_id = $this->connection->lastInsertId();
            return $item_id;
        } else {
            echo "Chyba v dotazu - PDOStatement::errorInfo(): ";
            print_r($errors);
            echo "SQL dotaz: $query";
        }
    }

    /**
     * Pridat polozku do DB - rozsirena verze.
     *
     * @param string $table_name
     * @param array $item - column = sloupec; value - int nebo string nebo value_mysql
     */
    public function DBInsertExpanded($table_name, $item) {
        
        $insert_columns = "";
        $insert_values = "";

        if ($item != null)
            foreach ($item as $row) {
                // pridat carky
                if ($insert_columns != "")
                    $insert_columns .= ", ";
                if ($insert_columns != "")
                    $insert_values .= ", ";

                $column = $row["column"];

                if (key_exists("value", $row))
                    $value_pom = "?";       
                else if (key_exists("value_mysql", $row))
                    $value_pom = $row["value_mysql"];  


                $insert_columns .= "`$column`";
                $insert_values .= "$value_pom";
            }

 
        $query = "insert into `$table_name` ($insert_columns) values ($insert_values);";
       
        $statement = $this->connection->prepare($query);

        
        $bind_param_number = 1;

        if ($item != null)
            foreach ($item as $row) {
                if (key_exists("value", $row)) {
                    $value = $row["value"];
                    

                    $statement->bindValue($bind_param_number, $value);  
                    $bind_param_number ++;
                }
            }

        // 4) provest dotaz
        $statement->execute();

        // 5) kontrola chyb
        $errors = $statement->errorInfo();
        //printr($errors);

        if ($errors[0] + 0 > 0) {
            // nalezena chyba
            $mysql_pdo_error = true;
        }

        // 6) nacist ID vlozeneho zaznamu a vratit
        if ($mysql_pdo_error == false) {
            $item_id = $this->connection->lastInsertId();
            return $item_id;
        } else {
            echo "Chyba v dotazu - PDOStatement::errorInfo(): ";
            printr($errors);
            echo "SQL dotaz: $query";
        }
    }

    
    public function DBUpdate() {
        
    }

    // KONEC UNIVERZALNI METODY
    // ***********************************************************

    /**
     * Připojí k vybrané db.
     */
    function Connect() {
        // PDO - MySQL
        try {
            $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
            $this->connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE_NAME . "", DB_USER_LOGIN, DB_USER_PASSWORD, $options);

            // nastavit pripojeni na UTF-8 - pro starsi verze PHP
            //$this->connection->exec("SET NAMES UTF8");
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

// konec Connect

    /**
     * Odpojí se od vybrané databáze.
     */
    function Disconnect() {
        $this->connection = null;
    }

    /**
     * Vratit pdo pripojeni
     */
    public function GetConnection() {
        return $this->connection;
    }

}
