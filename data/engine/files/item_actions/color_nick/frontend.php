<b><?=$w_color_nick?></b><br>
<small><?=$w_color_nick_note?></small>

<form action="act_submit.php" method="post" name="actions">
        <input type="Hidden" name="action_name" value="color_nick">
        <input type="Hidden" name="param[set]" value="1">
        <input type="Hidden" name="session" value="<?=$session?>">
<table>
        <tr>
               <td><?=$w_color_nick_current?>: <?php if($cu_array[USER_HTMLNICK] != "") echo $cu_array[USER_HTMLNICK];
                                                 else echo $cu_array[USER_NICKNAME]; ?>
               </TD>
        </tr>
        <tr>
               <td>
                   <table>
                   <tr><?php
                        for($i = 0; $i < strlen($cu_array[USER_NICKNAME]); $i++) {
                          $l = substr($cu_array[USER_NICKNAME], $i, 1);
                          echo "<td align=center><b>".$l."</b></td>";
                    } ?>
                   </tr>
                   <tr><?php
                        for($j = 0; $j < strlen($cu_array[USER_NICKNAME]); $j++) {
                          $l = substr($cu_array[USER_NICKNAME], $j, 1);
                          echo "<td>";
                          ?>
                          <select name="letter_color_<?=$j?>" style="{width:70px;height: 25px;}"  OnChange="drawNick()">
<?php for($i=0;$i<count($registered_colors);$i++)
{
        echo "<option value=\"$i\"";
        if ($i == $default_color) echo " selected";
        echo " style=\"background:".$registered_colors[$i][1]."; color:".$registered_colors[$i][1]."\">".$registered_colors[$i][0]."</option>\n";
}?>
</select></td>
                          <?php
                    } ?>
                   </tr>

                   </table>
               </TD>
        </tr>
        <tr>
               <td><div name="nickDiv" id="nickDiv"><?=$w_color_nick_sample?>: <font color="black"><?=$cu_array[USER_NICKNAME]?></font></div></TD>
        </tr>
        <tr>
               <td><input type="Submit" class="input_button" value="OK"></TD>
        </tr>
</table>
</form>
<script language="JavaScript">
 var arrNick = new Array(<?php
                     for($i = 0; $i < strlen($cu_array[USER_NICKNAME]); $i++) {
                          $l = substr($cu_array[USER_NICKNAME], $i, 1);
                          echo "'".$l."'";
                          if( $i < strlen($cu_array[USER_NICKNAME]) - 1) echo ", ";
                    }
 ?>);

 var nickLen = <?=strlen($cu_array[USER_NICKNAME])?>;

 var arrColor = new Array(<?php
                     for($i = 0; $i < count($registered_colors); $i++) {
                          echo "'".$registered_colors[$i][1]."'";
                          if( $i < count($registered_colors) - 1) echo ", ";
                    }
 ?>);


 function drawNick() {
     var nickHtml = '';
     var i = 0;
     var re = /[^a-z]/gi;

     for(i = 0; i < nickLen; i++) {
        selName = 'letter_color_' + i;
        sel         =  null;
        sel         = this.document.getElementById(selName);
        if(!sel)    sel = parent.document.all(selName);

        if(sel) {
              elStyle = arrColor[sel.selectedIndex];
              elStyle = elStyle.replace("#","");
              if(elStyle.match(re)) nickHtml = nickHtml + '<font color="#' + elStyle + '">'+ arrNick[i]+'</font>';
              else nickHtml = nickHtml + '<font color="' + elStyle + '">'+ arrNick[i]+'</font>';
        }
     }
     nd = null;
     nd = this.document.getElementById('nickDiv');
     if(!nd) nd = parent.document.all('nickDiv');
     if(nd) nd.innerHTML = '<?=$w_color_nick_sample?>: '+ nickHtml;
 }
</script>
