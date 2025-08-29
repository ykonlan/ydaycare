<?php
require_once __DIR__."/db.php";

function form_renderer($model){
    $model .= "s"; // pluralize
    $pdo = Database::get_connection();

    // describe table
    $sql = "DESCRIBE {$model}";
    $stmnt = $pdo->prepare($sql);
    $stmnt->execute();
    $fields = $stmnt->fetchAll(PDO::FETCH_ASSOC);

    // find foreign keys
    $fk_sql = "
        SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = :table
          AND REFERENCED_TABLE_NAME IS NOT NULL
    ";
    $fk_stmnt = $pdo->prepare($fk_sql);
    $fk_stmnt->execute(['table' => $model]);
    $foreignKeys = $fk_stmnt->fetchAll(PDO::FETCH_ASSOC);

    // map of FK fields
    $fkMap = [];
    foreach($foreignKeys as $fk){
        $fkMap[$fk['COLUMN_NAME']] = [
            'table' => $fk['REFERENCED_TABLE_NAME'],
            'column' => $fk['REFERENCED_COLUMN_NAME']
        ];
    }

    $formfields = [];
    $typeMap = [
        'int' => 'number',
        'tinyint' => 'checkbox',
        'varchar' => 'text',
        'text' => 'textarea',
        'datetime' => 'datetime-local',
        'date' => 'date',
        'float' => 'number',
        'double' => 'number'
    ];

    foreach($fields as $field){
        if(strpos($field["Extra"], "auto_increment") !== FALSE){
            continue;
        }

        $name = $field["Field"];

        // check if FK
        if(array_key_exists($name, $fkMap)){
            $refTable = $fkMap[$name]['table'];
            $refCol   = $fkMap[$name]['column'];

            // grab possible options
            $opt_sql = "SELECT {$refCol} FROM {$refTable}";
            $opt_stmnt = $pdo->prepare($opt_sql);
            $opt_stmnt->execute();
            $options = $opt_stmnt->fetchAll(PDO::FETCH_ASSOC);

            $formfields[] = [
                "name" => $name,
                "type" => "select",
                "options" => $options
            ];
        } else {
            // fall back to normal input
            preg_match("/^(\w+)/",$field["Type"],$matches);
            $formfields[] = [
                "name"=>$name,
                "type"=>$typeMap[$matches[1]] ?? "text"
            ];
        }
    }

    return $formfields;
}
?>