function getContentList(int)
{
    if (window.XMLHttpRequest) {
        xmlhttp=new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById("testAjax").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","/source/admin/articles",true);
    xmlhttp.send();
}
