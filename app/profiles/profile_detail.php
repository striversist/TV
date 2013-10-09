<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';

    if (!isset($_GET["guid"]))
        echo "Error: No GUID"."<br />";

    $guid = $_GET["guid"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="refresh" content="60">
    <title>后台统计监控系统</title>
</head>
    GUID is <?php echo $guid ?>
    <a href="javascript: history.back(-1);">返回</a>
<body>

<?php
    $db = Database::getInstance();
    $profile = $db->getProfile("$guid");

    echo "<pre>";
    var_dump($profile);
    echo "</pre>";
?>

</body>
</html>