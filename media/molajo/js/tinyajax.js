TINY={};

function T$(id){return document.getElementById(id)}
 
TINY.ajax=function(){
    return{
        call:function(u,d,f,p){
            var x=window.XMLHttpRequest?new XMLHttpRequest():new ActiveXObject('Microsoft.XMLHTTP');
            x.onreadystatechange=function(){
                if(x.readyState==4&&x.status==200){
                    if(d){
                        var t=T$(d);
                        t.innerHTML=x.responseText
                    }
                    if(f){
                        var c=new Function(f); c()
                    }
                }
            };
            if(p){
                x.open('POST',u,true);
                x.setRequestHeader('Content-type','application/x-www-form-urlencoded');
                x.send(p)
            }else{
                x.open('GET',u,true);
                x.send(null)
            }
        }
    };
}();

function display(name){
	var output=T$('output');
	output.style.display='block';
	output.className=name;
}

function hide(){
	var output=T$('output');
	output.style.display='none';
}