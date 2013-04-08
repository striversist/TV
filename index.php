<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>电视节目预告</title>
        <script type="text/javascript"> 
             /*
             * @returns XMLHttpRequest object
             */
            function createXHR()
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
                return xhr;
            }
            
            /*
             * Using Ajax sync request to init select options from json.php
             */
            function initSelect()
            {
                var programSelect = document.getElementById("programSelect");
                var jsonObject;
                var xhr = createXHR();
                var url = "json.php";
                xhr.onreadystatechange = function()
                {
                    // only handle loaded requests
                    if(xhr.readyState == 4)
                    {
                        if(xhr.status == 200)
                        {
                            jsonObject = eval("(" + xhr.responseText + ")");
                            //document.getElementById("div").innerHTML = xhr.responseText;
                            for(var i=0; i<jsonObject.channel_list.length; i++)
                            {
                                //document.getElementById("div").innerHTML += jsonObject.channel_list[i].id + ": " + jsonObject.channel_list[i].name + "<br />";
                                programSelect.options[programSelect.length] = new Option(jsonObject.channel_list[i].name, jsonObject.channel_list[i].id);
                            }
                        }
                        else
                        {
                            alert("Error with Ajax call!");
                        }
                    }
                };
                xhr.open("GET", url, false);
                xhr.send();               
            }
            
            /*
             * Use Ajax to quote TV program list
             */
            function quote()
            {   
                var xhr = createXHR();
                var channel = document.getElementById("programSelect").options[document.getElementById("programSelect").options.selectedIndex].value;
                var day = document.getElementById("daySelect").options[document.getElementById("daySelect").options.selectedIndex].value;
                var url = "choose.php?channel=" + channel + "&day=" + day;
                //document.getElementById("div").innerHTML = "url=" + url;
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
            
            /*
             * Use Ajax to search TV program
             */
            function search()
            {
                var xhr = createXHR();
                var keyword = document.getElementById("keytext").value;
                var url = "search.php?keyword=" + keyword;
                document.getElementById("div").innerHTML = "url = " + url;
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
    <body onload="initSelect()">
        <select id="programSelect" onchange="quote()" style="font-family:Verdana, Arial, Helvetica, sans-serif;">
        </select>
        <select id="daySelect" onchange="quote()" style="font-family:Verdana, Arial, Helvetica, sans-serif;">
            <option value="1">星期一</option>
            <option value="2">星期二</option>
            <option value="3">星期三</option>
            <option value="4">星期四</option>
            <option value="5">星期五</option>
            <option value="6">星期六</option>
            <option value="7">星期日</option>
        </select>
        <br /><br />
        <form name="searchForm" action="search.php" method="get" onsubmit="search(); return false;">
            <input id="keytext" type="text" name="keyword" />
            <input type="submit" value="搜索" />
        </form>
        <br /><br />
        <div id="div"></div>
    </body>
</html>
