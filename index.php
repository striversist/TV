<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>电视节目预告</title>
        <script type="text/javascript"> 
            /*
             * Use Ajax to quote TV program list
             */
            function quote(value)
            {   
                var xhr;
                try
                {
                    xhr = new XMLHttpRequest();
                }
                catch (e)
                {
                    xhr = new ActiveXObject("Microsoft.XMLHTTP");
                }
                // handle old browsers
                if(xhr == null)
                {
                    alert("Ajax is not supported by your browser!")
                    return;
                }
                
                var url = "choose.php?channel=" + value;
                //document.getElementById("div").innerHTML += "url=" + url;
                xhr.onreadystatechange = function()
                {
                    // only handle loaded requests
                    if(xhr.readyState == 4)
                    {
                        if(xhr.status == 200)
                        {
                            document.getElementById("div").innerHTML = xhr.responseText;
                        }
                        else
                        {
                            alert("Error with Ajax call!");
                        }
                    }
                };
                xhr.open("GET", url, true);
                xhr.send();
            }
        </script>
    </head>
    <body>
        <select id="select" onchange="quote(this.value)" style="font-family:Verdana, Arial, Helvetica, sans-serif;">
            <optgroup label="中央台">
                <option value="cctv1">CCTV-1（综合）</option>
                <option value="cctv2">CCTV-2（财经）</option>
                <option value="cctv3">CCTV-3（综艺）</option>
                <option value="cctv4">CCTV-4 (中文国际）</option>
                <option value="cctv5">CCTV-5（体育）</option>
                <option value="cctv6">CCTV-6（电影）</option>
                <option value="cctv7">CCTV-7（军事 农业）</option>
                <option value="cctv8">CCTV-8（电视剧）</option>
                <option value="cctv9">CCTV-9（纪录）</option>
                <option value="cctv10">CCTV-10（科教）</option>
                <option value="cctv11">CCTV-11（戏曲）</option>
                <option value="cctv12">CCTV-12（社会与法）</option>
                <option value="cctv13">CCTV-13（新闻）</option>
                <option value="cctv14">CCTV-14（少儿）</option>
                <option value="cctv15">CCTV-15（音乐）</option>
            </optgroup>
        </select>
        <br /><br />
        <div id="div"></div>
    </body>
</html>
