ToolTip.offsetX = 15; //смещения подсказки от курсора
ToolTip.offsetY = 15; //
function ToolTip(obj, text) {
  if(!obj||obj.nodeType!=1) throw "Illigal argument exception"; //обьект к кому привязываем подсказку
  //-- Разметка подсказки ---
  var tip=document.createElement("DIV");
  tip.className="tool_tip";
  tip.innerHTML=text;
  document.body.appendChild(tip);
  //-- события --
  // old = obj.onmouseout;
   obj.onmouseout=function (ev) {
   tip.style.visibility="hidden";
 //  old();
  };

  obj.onmousemove=function(ev) { //если не нужно что бы подскасзка бегала, то onmouseover
      tip.style.visibility="visible";
      if(window.event) ev=window.event;

if(tip.offsetWidth+ev.clientX+10>document.body.clientWidth) //если подсказка выходит за видимую
        ToolTip.offsetX=-tip.offsetWidth; //облать, то поворачиваем её
else
        ToolTip.offsetX=20;
if(tip.offsetHeight+ev.clientY+15>document.body.clientHeight)// тоже самое, но по вертикали
       ToolTip.offsetY=-tip.offsetHeight;
else
      ToolTip.offsetY=10;

      tip.style.left=ev.clientX + document.body.scrollLeft + ToolTip.offsetX;
      tip.style.top=ev.clientY + document.body.scrollTop + ToolTip.offsetY;
  };
}
//переберем заданные элементы, дадим подказку тем у кого есть аттрибут tooltip
//В аргументах передаем имена рассматриваемых тегов, * все теги
function initToolTips() {
//   return;
    var tags, tooltext;
    for(var i=0; i<arguments.length; i++) {
       tags=document.body.getElementsByTagName(arguments[i]);
       for(var j=0; j<tags.length; j++)
            if((tooltext=tags[j].getAttribute("help"))) ToolTip(tags[j], tooltext);
   }
}
