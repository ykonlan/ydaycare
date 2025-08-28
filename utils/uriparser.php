<?php

function get_page_and_search($uri){
        $query = parse_url($uri, PHP_URL_QUERY);
        $page = 1;
        $search = NULL;
        if($query){
            parse_str($query,$qparams);
            $page = isset($qparams["page"]) ? (int)$qparams["page"] : 1;
            $search = isset($qparams["search"]) ? trim($qparams["search"]) : NULL;
        }
        return [(int)$page, $search];
}

function get_id_from_path($uri){
    preg_match("#/\w+/(\d+)#",$uri, $matches);
    return $matches[1];
}

?>