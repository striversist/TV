<?php
    header("Content-type: text/html; charset=utf8");

    $headers = apache_request_headers();
    @$version = @$headers["Version"];
    if ($version == "1.5.0")    // 1.5.0版本升级提示会造成crash，故不提示升级
        return;
    
echo <<< EOT
<Updateinfo>
    <VersionCode>20</VersionCode>
    <VersionName>1.5.2</VersionName>
    <Url>http://bigeyecow.oicp.net:52719/projects/TV/public/update/apk/TVGuide_1.5.2.apk</Url>
    <ChannelVersion>5</ChannelVersion>
</Updateinfo>
EOT;

?>