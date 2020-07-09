// <!--
function klappe(id)
{
	var klappText = document.getElementById('k' + id);
	var klappBild = document.getElementById('pic' + id); 

	if (klappText.style.display == 'none') {
  		klappText.style.display = 'block';
	}
	else {
  		klappText.style.display = 'none';
	}
}

function klappe_news(id)
{
	var klappText = document.getElementById('k' + id);
	var klappBild = document.getElementById('pic' + id); 

	if (klappText.style.display == 'none') {
  		klappText.style.display = 'block';
  		klappBild.src = 'images/minus.gif';
	}
	else {
  		klappText.style.display = 'none';
  		klappBild.src = 'images/plus.gif';
	}
}

function klappe_torrent(id)
{
	var klappText = document.getElementById('k' + id);
	var klappBild = document.getElementById('pic' + id); 

	if (klappText.style.display == 'none') {
  		klappText.style.display = 'block';
  		klappBild.src = 'images/minus.gif';
	}
	else {
  		klappText.style.display = 'none';
  		klappBild.src = 'images/plus.gif';
	}
}

  function getCookie(name)
  {
      var i, x, y, cookies = document.cookie.split(';');
      
      for (i = 0; i < cookies.length; i++)
      {
          x = cookies[i].substr(0, cookies[i].indexOf('='));
          y = cookies[i].substr(cookies[i].indexOf('='));
          x = x.replace(/^\s+|\s+$/g, '');
          
          if (x == name)
          {
              return unescape(y.substr(1,5));
          }
      }
      
      return null;
  }

  function setCookie(name, value, expire)
  {
     var expiry = new Date();
     expiry.setDate(expiry.getDate() + expire);
     var values = escape(value) + ((expiry == null) ? '' : '; expires=' + expiry.toUTCString());
     document.cookie = name + '=' + values;
  }
  
  var checked = false;
  function checkAll(form)
  {
      if (checked == false)
          checked = true;
      else
          checked = false;

      var length = document.getElementById(form).elements.length; 
      
      for ( i = 0; i < length; i++ )
      {
          document.getElementById(form).elements[i].checked = checked;
      }
  } 
  
  function toggleChecked(state)
  {
      var x = document.getElementsByTagName('input');
      
      for ( i = 0; i < x.length; i++ )
      {
          if ( x[i].type == 'checkbox' )
          {
               x[i].checked = state;
          }
      }
  }
  
  function toggleDisplay(id)
  {
      var x = document.getElementById(id);
      
      if ( x.style.display == '' ) 
           x.style.display = 'none';
      else
           x.style.display = '';
  }
  
  function toggleTemplate(x)
  {
      var y = true;
      
      if ( x.form.usetemplate.selectedIndex == 0 ) 
           y = false;
           
      x.form.subject.disabled = y;
      x.form.msg.disabled = y;
      x.form.draft.disabled = y;
      x.form.template.disabled = y;
  }
  
  function read(id)
  {
      var x = document.getElementById('msg_' + id);
      var y = document.getElementById('img_' + id);
      
      if ( x.style.display == '' )
      {
           x.style.display = 'none';
           y.src = 'images/plus.gif';
      }
      else
      {
           x.style.display = '';
           y.src = 'images/minus.gif';
      }
  }

  function SmileIT(smile,form,text)
  {
      document.forms[form].elements[text].value = document.forms[form].elements[text].value+" "+smile+" ";
      document.forms[form].elements[text].focus();
  }

  function PopMoreSmiles(form,name) 
  {
      link = 'backend/smilies.php?action=display&form='+form+'&text='+name
      newWin = window.open(link,'moresmile','height=500,width=450,resizable=no,scrollbars=yes,location=no');
      if (window.focus) {newWin.focus()}
  }
  
  function PopMoreTags() 
  {
      link = 'tags.php';
      newWin = window.open(link,'moresmile','height=500,width=775,resizable=no,scrollbars=yes,location=no');
      if (window.focus) {newWin.focus()}
  }          
  
  jQuery(document).ready(function()
  {
      var items = jQuery('.showHide');
      
      for ( i = 0; i <= items.length; i++ )
      {
          if ( getCookie('slidingDiv'+items[i].id) == 'hide' )
          {
              jQuery('.showHide[id='+items[i].id+']').html('+');
              jQuery('.slidingDiv'+items[i].id).hide();
          }
          else
          {
              jQuery('.showHide[id='+items[i].id+']').html('-');
              jQuery('.slidingDiv'+items[i].id).show(); 
          }
          
          jQuery('.showHide[id='+items[i].id+']').click(function()
          {
              var id = jQuery(this).attr('id');
              var type = jQuery('.slidingDiv' + id).is(':hidden');
              
              setCookie('slidingDiv' + id, (type == false ? 'hide' : 'show'), 86400);
              
              jQuery('.showHide[id='+id+']').html((type == false ? '+' : '-'));
              jQuery('.slidingDiv' + id).slideToggle(); 
          });
      } 
  });

// -->