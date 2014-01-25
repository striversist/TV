<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    $db = Database::getInstance();
    $profiles = $db->getProfiles();    
    foreach ($profiles as $key => $profile) 
    {
        $lastLogin[$key] = $profile["LastLogin"];
        $guid[$key] = $profile["GUID"];
    }
    array_multisort($lastLogin, SORT_STRING, SORT_DESC, $guid, SORT_STRING, SORT_ASC, $profiles);
//    foreach ($profiles as $guid => $profile) 
//    {
//          echo "<pre>";
//          var_dump($profile);
//          echo "</pre>";
//    }
//    echo "Total count: ".  count($profiles)."<br />";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="refresh" content="120">
    <title>后台统计监控系统</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<table id="user_info_table" style="border:solid 1px #ffffff">
    <p><h1>总用户数：<?php echo count($profiles); ?></h1></p>
    <?php
        $today = date("Y/m/d");
        $login_record = $db->getLoginRecordByDate("$today");
        $daily_activity = count($login_record["NewUsers"]) + count($login_record["LoyalUsers"]);
        $daily_new_user = count($login_record["NewUsers"]);
        
        $visit_record = $db->getChannelVisitRecordByDate($today);
        $daily_channel_visit_count = 0;
        foreach ($visit_record["VisitRecord"] as $channel_id => $channel)
        {
//            echo "channel: ".$channel_id." VisitTimes=".$channel["VisitTimes"]."<br />";
            $daily_channel_visit_count += $channel["VisitTimes"];
        }
    ?>
    <a href="../activities/index.htm"/><p><h1>日活跃度：<?php echo $daily_activity; ?> = <?php echo $daily_new_user; ?>（日新增）+ <?php echo $daily_activity - $daily_new_user; ?>（老用户）</h1></p>
    <a href="../visits/index.htm"/><p><h1>今日节目访问量：<?php echo $daily_channel_visit_count; ?></h1></p>
    <p>
        <a href="../searches/index.php"/><font size="5px">搜索记录</font>&nbsp;&nbsp;
        <a href="../feedbacks/index.php"/><font size="5px">反馈记录</font>
    </p>
    <tbody>
        <tr>
            <th width="50">用户</th>
            <th width="250">GUID</th>
            <th width="80">版本</th>
            <th width="150">首次启动</th>
            <th width="150">最后登录</th>
            <th width="100">用户IP</th>
            <th width="180">位置</th>
            <th width="100">RemoteIP</th>
            <th width="80">渠道</th>
            <th width="100">操作</th>
        </tr>
        <?php 
            $show_limit = 500;
            $index = 0;
            foreach ($profiles as $guid => $profile) 
            {
        ?>
        <tr>
                <td style="border:solid 1px #799AE1"></td>
                <td style="border:solid 1px #799AE1"><?php echo @$profile["GUID"] ?></td>
                <td style="border:solid 1px #799AE1"><?php echo @$profile["Version"] ?></td>
                <td style="border:solid 1px #799AE1"><?php echo @$profile["FirstUse"] ?></td>
                <td style="border:solid 1px #799AE1"><?php echo @$profile["LastLogin"] ?></td>
                <td style="border:solid 1px #799AE1"><?php echo @$profile["UIP"] ?></td>
                <td style="border:solid 1px #799AE1"><?php echo @$profile["UL"] ?></td>
                <td style="border:solid 1px #799AE1"><?php echo @$profile["RemoteIP"] ?></td>
                <td style="border:solid 1px #799AE1"><?php echo @$profile["APP_CHANNEL"] ?></td>
                <td style="border:solid 1px #799AE1"><a href="profile_detail.php?guid=<?php echo $profile["GUID"] ?>" style="cursor:pointer">更多</a></td>
        </tr>
        <?php
                if ($index++ > $show_limit)
                    break;
            }
        ?>
    </tbody>
</table>

</body>
</html>
