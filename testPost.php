<?php
    echo "test post"."<br />";        

    if(!isset($_POST["channel"]))
    {
        echo "!isset channel"."<br />";
        return;
    }
    
    if (htmlspecialchars($_POST["channel"]) === '')
    {
        echo "post channel is null"."<br />";
        return;
    }
    
    $id = htmlspecialchars($_POST["channel"]);
    echo "Post channel=$id"."<br />";
    
?>