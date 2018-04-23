/**
* Author:	Omar Muhammad
* Email:	admin@omar84.com
* Website:	http://omar84.com
* Module:	Simple Image Holder
* Version:	1.5.12
* Date:		18/12/2009
**/

function SIH_AddExtension(src, ext)
{
if (src.indexOf('?') != -1)
	return src.replace(/\?/, ext+'?');
else
	return src+ext;
}

function SIH_Generateobj(objAttrs, params, embedAttrs)
{
var str='<object ';
for (var i in objAttrs)
	str+=i+'="'+objAttrs[i]+'" ';
str+='>';
for (var i in params)
	str+='<param name="'+i+'" value="'+params[i]+'" /> ';
str+='<embed ';
for (var i in embedAttrs)
	str+=i+'="'+embedAttrs[i]+'" ';
str+=' ></embed></object>';

document.write(str);
}

function SIH_FL_RunContent()
{
var ret=SIH_GetArgs(arguments, ".swf", "movie", "clsid:d27cdb6e-ae6d-11cf-96b8-444553540000", "application/x-shockwave-flash");
SIH_Generateobj(ret.objAttrs, ret.params, ret.embedAttrs);
}

function SIH_SW_RunContent()
{
var ret=SIH_GetArgs(arguments, ".dcr", "src", "clsid:166B1BCA-3F9C-11CF-8075-444553540000", null);
SIH_Generateobj(ret.objAttrs, ret.params, ret.embedAttrs);
}

function SIH_GetArgs(args, ext, srcParamName, classid, mimeType)
{
var ret=new Object();
ret.embedAttrs=new Object();
ret.params=new Object();
ret.objAttrs=new Object();
for (var i=0; i < args.length; i=i+2)
	{
	var currArg=args[i].toLowerCase();

	switch (currArg)
		{
		case "classid":
			break;
		case "pluginspage":
			ret.embedAttrs[args[i]]=args[i+1];
			break;
		case "src":
		case "movie":
			ret.embedAttrs["src"]=args[i+1];
			ret.params[srcParamName]=args[i+1];
			break;
		case "onafterupdate":
		case "onbeforeupdate":
		case "onblur":
		case "oncellchange":
		case "onclick":
		case "ondblClick":
		case "ondrag":
		case "ondragend":
		case "ondragenter":
		case "ondragleave":
		case "ondragover":
		case "ondrop":
		case "onfinish":
		case "onfocus":
		case "onhelp":
		case "onmousedown":
		case "onmouseup":
		case "onmouseover":
		case "onmousemove":
		case "onmouseout":
		case "onkeypress":
		case "onkeydown":
		case "onkeyup":
		case "onload":
		case "onlosecapture":
		case "onpropertychange":
		case "onreadystatechange":
		case "onrowsdelete":
		case "onrowenter":
		case "onrowexit":
		case "onrowsinserted":
		case "onstart":
		case "onscroll":
		case "onbeforeeditfocus":
		case "onactivate":
		case "onbeforedeactivate":
		case "ondeactivate":
		case "type":
		case "codebase":
			ret.objAttrs[args[i]]=args[i+1];
			break;
		case "width":
		case "height":
		case "align":
		case "vspace":
		case "hspace":
		case "class":
		case "title":
		case "accesskey":
		case "name":
		case "id":
		case "tabindex":
			ret.embedAttrs[args[i]]=ret.objAttrs[args[i]]=args[i+1];
			break;
		default:
			ret.embedAttrs[args[i]]=ret.params[args[i]]=args[i+1];
		}
	}
ret.objAttrs["classid"]=classid;
if (mimeType)
	ret.embedAttrs["type"]=mimeType;
return ret;
}