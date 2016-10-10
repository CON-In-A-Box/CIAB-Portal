<!--Log Head Info-->
<script src="sitesupport/speller/spellChecker.js"></script>
<script src="sitesupport/validation.js"></script>
<script src="sitesupport/lockedtag.js"></script>

<script type="text/javascript">
function openSpellChecker() {
        var textarea1 = document.log_entry_form.logentry_textarea;
        var speller = new spellChecker( textarea1 );
        speller.openChecker();
}

function setFocus() {
document.getElementById("logentry_textarea").focus()
}

function validate_form(thisform) {
  with (thisform)
  {
    if (validate_required(logentry_textarea,"Log Entry can not be blank!")==false)
    {
      setFocus();
      return false;
    }
  }
}

function toggle() {
	var ele = document.getElementById("toggleOtherDepartments");
	var text = document.getElementById("displayOtherDepartments");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Select Other Departments";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "Hide Other Departments";
	}
}
</script>