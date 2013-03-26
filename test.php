<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>电视节目预告</title>
<script type="text/javascript">
function loadXMLDoc()
{
    document.getElementById("myDiv").innerHTML = "Enter loadXMLDoc().<br />";
/*    
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","/ajax/demo_get.asp",true);
xmlhttp.send();
*/
}
</script>
</head>
<body>

<h2>AJAX</h2>
<button type="button" onclick="loadXMLDoc()">请求数据</button>
<div id="myDiv"></div>

</body>
</html>

