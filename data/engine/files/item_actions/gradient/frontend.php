<?
set_variable("start_color");
set_variable("end_color");
$start_color = intval($start_color);
$end_color = intval($end_color);
?>
<script>
function AnimationIterator(strElementName , fForvard , r1 , g1 , b1 , r2, g2, b2 , nMilliSecondsTimeout) 
{
	var currentElement = null;
	var nAnimationCounter = 0;
	var nFinalElement = arrayColorsHolder[strElementName].length - 1;
	var fInnerDirection = null;
	
	//Define the moving direction
	if(fForvard)
	{
		fInnerDirection = !arrayAnimationDirectionHolder[strElementName];
	}
	else
	{
		fInnerDirection = arrayAnimationDirectionHolder[strElementName];
	}
	
	nAnimationCounter = arrayAnimationCountersHolder[strElementName];
	for(var j = 0; j <= nFinalElement ; j++ )
	{
		//Get element within the text container
		currentElement = document.getElementById(strElementName + '_animated_child_' + j);
		
		
		if(currentElement) // if current element exists
		{
			//set span's color 
			currentElement.style.color = 'rgb(' + 
				arrayColorsHolder[strElementName][nAnimationCounter]['red']   + ','+ 
				arrayColorsHolder[strElementName][nAnimationCounter]['green'] + ','+
				arrayColorsHolder[strElementName][nAnimationCounter]['blue']  +
				')';
			
			 // Rule of painting.  -- BEGIN 
			if(fInnerDirection)
			{
				if(nAnimationCounter < nFinalElement )
				{
					nAnimationCounter ++ ;
				}
				else
				{
					fInnerDirection = ! fInnerDirection;
				}
			}
			else
			{
				if(nAnimationCounter > 0 )
				{
					nAnimationCounter --;
				}
				else
				{
					fInnerDirection = ! fInnerDirection;
				}
			} 
			// Rule of painting.  -- END 
		}
		else
		{
			window.status = 'Text container "' + strElementName + '" is modified or not initialized';
			return;
		}
	}		
	
	// Rule of painting.  -- BEGIN 
	if(arrayAnimationDirectionHolder[strElementName])
	{
		if(arrayAnimationCountersHolder[strElementName] < nFinalElement )
		{
			arrayAnimationCountersHolder[strElementName] ++ ;
		}
		else
		{
			arrayAnimationDirectionHolder[strElementName] = ! arrayAnimationDirectionHolder[strElementName];
		}
	}
	else
	{
		if(arrayAnimationCountersHolder[strElementName] > 0 )
		{
			arrayAnimationCountersHolder[strElementName] --;
		}
		else
		{
			arrayAnimationDirectionHolder[strElementName] = ! arrayAnimationDirectionHolder[strElementName];
		}
	}
	// Rule of painting.  -- END 
	
	//Launch the next iteration 
	if(nMilliSecondsTimeout > 0 )
	{
		window.setTimeout('AnimationIterator("' + 
		strElementName + 
		'", '+ fForvard.toString() + 
		', ' + r1.toString() + 
		', ' + g1.toString() + 
		', ' + b1.toString() + 
		', ' + r2.toString() + 
		', ' + g2.toString() + 
		', ' + b2.toString() + 	
		', ' + nMilliSecondsTimeout.toString() + 	 
		' )', nMilliSecondsTimeout);
	}
}

function parse_rgb(rgb){
	var r=0;
	var g=0;
	var b=0;
//	alert(rgb.charAt(1));
	r=hexToDec(rgb.charAt(1)+rgb.charAt(2));
	g=hexToDec(rgb.charAt(2)+rgb.charAt(3));
	b=hexToDec(rgb.charAt(4)+rgb.charAt(5));
}
var convert = new Array();
var hexbase = new Array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F");
var value=0; 
for (x=0; x<16; x++) 
for (y=0; y<16; y++) {
    convert[value] = hexbase[x] + hexbase[y];
    value++;
}
function hexToDec(hex){ 
	value=0;
	while (true){ 
		if (convert[value].toUpperCase()== hex.toUpperCase()) break; 
		value++; 
	} 
	return value; 
}
function redraw(start_c,end_c){
	ColorGradientText('tested_disp',hexToDec(start_c.charAt(1)+start_c.charAt(2)),hexToDec(start_c.charAt(3)+start_c.charAt(4)),hexToDec(start_c.charAt(5)+start_c.charAt(6)),hexToDec(end_c.charAt(1)+end_c.charAt(2)),hexToDec(end_c.charAt(3)+end_c.charAt(4)),hexToDec(end_c.charAt(5)+end_c.charAt(6)));
}
var arrayColorsHolder             = new Array();  
var arrayAnimationDirectionHolder = new Array();  
var arrayAnimationCountersHolder  = new Array();  

function ColorGradientText(strElementName , r1 , g1 , b1 , r2, g2, b2)
{
	 
	var textContainer = document.getElementById(strElementName);
	if(textContainer === null)
	{
		window.status += 'Text container "' + strElementName + '" is not defined.';
		return;
	}

	var strText  = textContainer.innerText || textContainer.textContent ;
	if(!strText)
	{
		window.status += 'Text container "' + strElementName + '" is empty.';
		return;
	}

	arrayColorsHolder            [strElementName] = new Array();
	
	var nLetersCount = strText.length;
	
	// Define steps for creating color gradient
	var steps = new Array ();
	steps['red']   = (r2 - r1) / nLetersCount ;
	steps['green'] = (g2 - g1) / nLetersCount ;
	steps['blue']  = (b2 - b1) / nLetersCount ;

	// Generate array of color values
	for( var  i = 0; i < strText.length ; i++ )
	{
		arrayColorsHolder[strElementName][i]          = new Array();
		arrayColorsHolder[strElementName][i]['red']   = Math.round(r1 + steps['red']*i)  ;
		arrayColorsHolder[strElementName][i]['green'] = Math.round(g1 + steps['green']*i);  
		arrayColorsHolder[strElementName][i]['blue']  = Math.round(b1 + steps['blue']*i) ; 			
	}
	
	// Set initial values for harmonic oscillations emulator
	arrayAnimationDirectionHolder[strElementName] = true;
	arrayAnimationCountersHolder [strElementName] = 0;
	
	//Split original string. Result of this operation is the chain of span elements.
	var strResultString = '';
	for(var j = 0; j < strText.length ; j++ )
	{
		
		strResultString += '<span id="'+
				strElementName + '_animated_child_' + j +
				'"  >' + strText.charAt(j)  +'</span>';
				
	}			
	
	//Fill container by <span>s 
	document.getElementById(strElementName).innerHTML = "<b>"+strResultString+"</b>";
	
	AnimationIterator(strElementName ,  0, r1 , g1 , b1 , r2, g2, b2 , 0);
}
</script>
<form action="act_submit.php" method="post" name="actions">
        <input type="Hidden" name="action_name" value="gradient">
        <input type="Hidden" name="param[set]" value="1">
        <input type="Hidden" name="session" value="<?=$session?>">
<table>
        <tr>
            <td colspan="2"><b>Градиентный цвет текста</b></td>
        </tr>
        <tr>
            <td colspan="2">(задается 1 раз)</td>
        </tr>

        <tr>
                <td>Стартовый цвет:</td>
                <td><select name="start_color" style="{width:70px;height: 25px;}" onchange="redraw(this.options[this.selectedIndex].style.backgroundColor,end_color.options[end_color.selectedIndex].style.backgroundColor);">
<?php for($i=0;$i<count($registered_colors);$i++)
{
        echo "<option value=\"$i\"";
        if ($i == $start_color) echo " selected";
        echo " style=\"background:".$registered_colors[$i][1]."; color:".$registered_colors[$i][1]."\">".$registered_colors[$i][0]."</option>\n";
}?></select>
                </td>
<td>Конечный цвет:</td>
                <td><select name="end_color" style="{width:70px;height: 25px;}" onchange="redraw(start_color.options[start_color.selectedIndex].style.backgroundColor,this.options[this.selectedIndex].style.backgroundColor);">
<?php for($i=0;$i<count($registered_colors);$i++)
{
        echo "<option value=\"$i\"";
        if ($i == $end_color) echo " selected";
        echo " style=\"background:".$registered_colors[$i][1]."; color:".$registered_colors[$i][1]."\">".$registered_colors[$i][0]."</option>\n";
}?></select>
                </td>
                <Td id="tested_disp"><strong>Съешь еще этих мягких французских булочек</strong></TD>
                <Td><input type="Submit" class="input_button" value="OK"></TD>
        </tr>
</table>
</form>
