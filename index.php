<?php
    
    require_once(__DIR__."/app/app.php");

    if(isset($_GET["bench"])){
        $bench = new \Raww\Bench();
        $bench->start("rawwapp");
    }

    $app->handle( isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : "/" );

    if(isset($bench)){

        $data = $bench->get("rawwapp");

        switch ($_GET["bench"]) {
            case 'json':
            case 'js':
            case 'css':
                echo '/**'.print_r($data, true).' **/';
                break;
            
            default:
                echo '<!-- '.print_r($data, true).' -->';
                break;
        }
    }