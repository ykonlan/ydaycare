<?php
require_once __DIR__."/db.php";

function form_renderer($model){
    $pdo = Database::get_connection();

    // describe table
    $sql = "DESCRIBE {$model}";
    $stmnt = $pdo->prepare($sql);
    $stmnt->execute();
    $fields = $stmnt->fetchAll(PDO::FETCH_ASSOC);

    $fkMap = ["ward_parent"=>["users","user_name"],
            "ward_class"=>["classes","class_name"],
            "user_role"=>["roles","role_name"],
            "allergic_ward"=>["wards","ward_name"]
];


    $skip_fields = ["#\w+id#", "#\w+date_added", "user_last_login", "user_password"];


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
        if(in_array($field["Field"],$skip_fields)){
            continue;
        }
        else{
            $name = $field["Field"];
            if(array_key_exists($name, $fkMap)){
                $refTable = $fkMap[$name][0];
                $readable = $fkMap[$name][1];
                $opt_sql = "SELECT $readable FROM {$refTable}";
                $opt_stmnt = $pdo->prepare($opt_sql);
                $opt_stmnt->execute();
                $options = $opt_stmnt->fetchAll(PDO::FETCH_ASSOC);
                $formfields[] = [
                    "name" => $name,
                    "type" => "select",
                    "options" => $options
                ];
            }
            else {
            // fall back to normal input
            preg_match("/^(\w+)/",$field["Type"],$matches);
            $formfields[] = [
                "name"=>$name,
                "type"=>$typeMap[$matches[1]] ?? "text"
            ];
            }
        } 
    }

    return $formfields;
}
?>