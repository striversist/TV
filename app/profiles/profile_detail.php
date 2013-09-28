<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';

    if (!isset($_GET["guid"]))
        echo "Error: No GUID"."<br />";

    $guid = $_GET["guid"];
?>
    GUID is <?php echo $guid ?>
    <a href="javascript: history.back(-1);">返回</a>

<?php
    $db = Database::getInstance();
    $profiles = $db->getProfiles();
    $profile = $profiles["$guid"];

    echo "<pre>";
    var_dump($profile);
    echo "</pre>";
?>
