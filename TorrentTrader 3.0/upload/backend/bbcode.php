<?php

function textbbcode($form,$name,$content="") {
	//$form = form name
	//$name = textarea name
	//$content = textarea content (only for edit pages etc)
?>
<script type="text/javascript">

function BBTag(tag,s,text,form){
switch(tag)
    {
    case '[quote]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[quote]" + body.substring(start, end) + "[/quote]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[quote][/quote]";
	}
        break;
    case '[img]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[img]" + body.substring(start, end) + "[/img]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[img][/img]";
	}
        break;
    case '[url]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[url]" + body.substring(start, end) + "[/url]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[url][/url]";
	}
        break;
    case '[*]':
        document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[*]";
        break;
    case '[b]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[b]" + body.substring(start, end) + "[/b]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[b][/b]";
	}
        break;
    case '[i]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[i]" + body.substring(start, end) + "[/i]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[i][/i]";
	}
        break;
    case '[u]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substr(body, start);
		var middle = "[u]" + body.substring(start, end) + "[/u]";
		var right = body.substr(end, body.length);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[u][/u]";
	}
        break;
    }
    document.forms[form].elements[text].focus();
}

</script>
<br />
<div class='b-border' style="margin-left:auto; margin-right:auto;">
<table align='center' border='0' cellpadding='6' cellspacing='0'>
  <tr class='b-title'>
    <th colspan="2" align='center' valign="middle"><table border="0" align="center" cellpadding="4" cellspacing="0">
        <tr>
          <td align="center"><input style="font-weight: bold;" type="button" name="bold" value="B " onclick="javascript: BBTag('[b]','bold','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
          <td align="center"><input style="font-style: italic;" type="button" name="italic" value="I " onclick="javascript: BBTag('[i]','italic','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
          <td align="center"><input style="text-decoration: underline;" type="button" name="underline" value="U " onclick="javascript: BBTag('[u]','underline','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
          <td align="center"><input type="button" name="li" value="List " onclick="javascript: BBTag('[*]','li','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
          <td align="center"><input type="button" name="quote" value="QUOTE " onclick="javascript: BBTag('[quote]','quote','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
          <td align="center"><input type="button" name="url" value="URL " onclick="javascript: BBTag('[url]','url','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
          <td align="center"><input type="button" name="img" value="IMG " onclick="javascript: BBTag('[img]','img','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
        </tr>
    </table>
    </th>  </tr>
  <tr class='b-row'>
    <td class='bb-comment' align='center' valign='top'><textarea name="<?php echo $name; ?>" rows="10" cols="50"><?php echo $content; ?></textarea></td>
    <td class='bb-btn' width='130' align="center" valign='top'>
      <table border="0" cellpadding="3" cellspacing="3" align="center">
      <tr>
          <td width="26"><a href="javascript:SmileIT(':)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/smile.png" border="0" alt=':)' title=':)' /></a></td>
          <td width="26"><a href="javascript:SmileIT(':(','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/sad.png" border="0" alt=':(' title=':(' /></a></td>
          <td width="26"><a href="javascript:SmileIT(':D','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/grin.png" border="0" alt=':D' title=':D' /></a></td>
          <td width="26"><a href="javascript:SmileIT(':P','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/razz.png" border="0" alt=':P' title=':P' /></a></td>  
      </tr>
      <tr>
          <td width="26"><a href="javascript:SmileIT(':-)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/smile-big.png" border="0" alt=':-)' title=':-)' /></a></td>
          <td width="26"><a href="javascript:SmileIT('B)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/cool.png" border="0" alt='B)' title='B)' /></a></td>
          <td width="26"><a href="javascript:SmileIT('8o','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/eek.png" border="0" alt='8o' title='8o' /></a></td>
          <td width="26"><a href="javascript:SmileIT(':?','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/confused.png" border="0" alt=':?' title=':?' /></a></td>    
      </tr>
      <tr>
          <td width="26"><a href="javascript:SmileIT('8)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/glasses.png" border="0" alt='8)' title='8)' /></a></td>
          <td width="26"><a href="javascript:SmileIT(';)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/wink.png" border="0" alt=';)' title=';)' /></a></td>
          <td width="26"><a href="javascript:SmileIT(':-*','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/kiss.png" border="0" alt=':-*' title=':-*' /></a></td>
          <td width="26"><a href="javascript:SmileIT(':-(','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/crying.png" border="0" alt=':-(' title=':-(' /></a></td>
      </tr>
      <tr>
          <td width="26"><a href="javascript:SmileIT(':|','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/plain.png" border="0" alt=':|' title=':|' /></a></td>
          <td width="26"><a href="javascript:SmileIT('O:-D','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/angel.png" border="0" alt='O:-D' title='0:-D' /></a></td>
          <td width="26"><a href="javascript:SmileIT(':-@','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/devilish.png" border="0" alt=':-@' title=':-@' /></a></td>
          <td width="26"><a href="javascript:SmileIT(':o)','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/monkey.png" border="0" alt=':o)' title=':o)' /></a></td>
      </tr>
      <tr>
           <td width="26"><a href="javascript:SmileIT('brb','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/brb.png" border="0" alt='brb' title='brb' /></a></td>
           <td width="26"><a href="javascript:SmileIT(':warn','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/warn.png" border="0" alt=':warn' title=':warn' /></a></td>
           <td width="26"><a href="javascript:SmileIT(':help','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/help.png" border="0" alt=':help' title=':help' /></a></td> 
           <td width="26"><a href="javascript:SmileIT(':bad','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/bad.png" border="0" alt=':bad' title=':bad' /></a></td> 
      </tr>
      <tr>
          <td width="26"><a href="javascript:SmileIT(':love','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/love.png" border="0" alt=':love' title=':love' /></a></td>
          <td width="26"><a href="javascript:SmileIT(':idea','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/idea.png" border="0" alt=':idea' title=':idea' /></a></td> 
          <td width="26"><a href="javascript:SmileIT(':bomb','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/bomb.png" border="0" alt=':bomb' title=':bomb' /></a></td> 
          <td width="26"><a href="javascript:SmileIT(':!','<?php echo $form; ?>','<?php echo $name; ?>')"><img src="images/smilies/important.png" border="0" alt=':!' title=':!' /></a></td> 
      </tr>
      </table>
      <br />
      <a href="javascript:PopMoreSmiles('<?php echo $form; ?>','<?php echo $name; ?>');"><?php echo "[".T_("MORE_SMILIES")."]";?></a><br />
      <a href="javascript:PopMoreTags();"><?php echo "[".T_("MORE_TAGS")."]";?></a><br />    </td>
  </tr>
</table>
</div>
<br />
<?php
}
?>
