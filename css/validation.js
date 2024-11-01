
<script type="text/javascript">

function validateForm()
{
alert("manju");
var x=document.forms["addnew"]["names"].value;
var y=document.forms["addnew"]["companyname"].value;
var p=document.forms["addnew"]["phoneno"].value;
var mail=document.forms["addnew"]["email"].value;
var country=document.forms["addnew"]["country"].value;
if (x==null || x=="")
  {
  alert(" Name is not Entered");
  return false;
  }

 if (y==null || y=="")
  {
  alert(" CompanyName is not Entered");
  return false;
  }

  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))
{
//return true;
}
else if(mail!="")
{
alert("Invalid E-mail Address! Please re-enter!");
return false
}
else
{
alert("email is empty");
return false;
}

if (p==null || p=="")
  {
  alert(" PhoneNo is not Entered");
  return false;
  }

if (country==null || country=="")
  {
  alert(" Country is not Entered");
  return false;
  }
}
</script>

