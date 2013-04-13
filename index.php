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
                if(xhr === null)
                {
                    alert("Ajax is not supported by your browser!");
                    return;
                }
                return xhr;
            }
            
            function updateProgramSelect()
            {
                var programSelect = document.getElementById("programSelect");
                var length = programSelect.length;
                for(var i=length-1; i>=0; i--)
                {
                    programSelect.remove(i);
                }
                
                var programJsonObject;
                var xhrProgram = createXHR();
                var programUrl = "json/channels.php?category=" + categorySelect.options[document.getElementById("categorySelect").options.selectedIndex].value;;
                xhrProgram.onreadystatechange = function()
                {
                    // only handle loaded requests
                    if(xhrProgram.readyState === 4)
                    {
                        if(xhrProgram.status === 200)
                        {
                            programJsonObject = eval("(" + xhrProgram.responseText + ")");
                            //document.getElementById("div").innerHTML = xhr.responseText;
                            for(var i=0; i<programJsonObject.channel_list.length; i++)
                            {
                                //document.getElementById("div").innerHTML += jsonObject.channel_list[i].id + ": " + jsonObject.channel_list[i].name + "<br />";
                                programSelect.options[programSelect.length] = new Option(programJsonObject.channel_list[i].name, programJsonObject.channel_list[i].id);
                            }
                            quote();
                        }
                        else
                        {
                            alert("Error with Ajax call!");
                        }
                    }
                };
                xhrProgram.open("GET", programUrl, false);
                xhrProgram.send();
            }
            
            /*
             * Using Ajax sync request to init select options from json.php
             */
            function initSelect()
            {              
                var categorySelect = document.getElementById("categorySelect");
                var categoryJsonObject;
                var xhrCategory = createXHR();
                var categoryUrl = "json/categories.php";
                xhrCategory.onreadystatechange = function()
                {
                    //document.getElementById("div").innerHTML += xhrCategory.readyState;
                    // only handle loaded requests
                    if(xhrCategory.readyState === 4)
                    {
                        if(xhrCategory.status === 200)
                        {
                            categoryJsonObject = eval("(" + xhrCategory.responseText + ")");
                            //document.getElementById("div").innerHTML = xhrCategory.responseText;
                            for(var i=0; i<categoryJsonObject.categories.length; i++)
                            {
                                //document.getElementById("div").innerHTML += jsonObject.channel_list[i].id + ": " + jsonObject.channel_list[i].name + "<br />";
                                categorySelect.options[categorySelect.length] = new Option(categoryJsonObject.categories[i].name, categoryJsonObject.categories[i].id);
                            }
                            updateProgramSelect();
                        }
                        else
                        {
                            alert("Error with Ajax call!");
                        }
                    }
                };
                xhrCategory.open("GET", categoryUrl, false);
                xhrCategory.send();
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
                    if(xhr.readyState === 4)
                    {
                        if(xhr.status === 200)
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
                    if(xhr.readyState === 4)
                    {
                        if(xhr.status === 200)
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
    <body onload="initSelect();">
        <select id="categorySelect" onchange="updateProgramSelect();" style="font-family:Verdana, Arial, Helvetica, sans-serif;">
        </select>
        <select id="programSelect" onchange="quote();" style="font-family:Verdana, Arial, Helvetica, sans-serif;">
        </select>
        <select id="daySelect" onchange="quote();" style="font-family:Verdana, Arial, Helvetica, sans-serif;">
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