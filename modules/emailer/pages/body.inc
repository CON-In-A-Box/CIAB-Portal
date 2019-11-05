<?php
/*.
    require_module 'standard';
.*/

require_once __DIR__."/../functions/emailer.inc";
require_once($BACKEND."/RBAC.inc");

$emailer = new \mailer\Email();
?>

<div id="page"  class="UI-container">
    <div id="main_content"  class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Sending Email</span>
        </div>
        <div id="body" class="UI-padding">
            <div class="UI-rest">
                <label class='UI-label' for="email_from">
                From:</label>
                <select class="UI-input" id="email_from">
<?php
$value = getNoReplyAddress();
print "<option selected>".$value."</option>\n";
$address = $emailer->buildFrom();
foreach ($address as $addr) {
    print "<option>".$addr."</option>\n";
}
?>
                </select>
                <label class='UI-label' for="email_reply">
                Reply-To:</label>
                <select class="UI-input" id="email_reply">
<?php
$value = getNoReplyAddress();
print "<option selected>".$value."</option>\n";
$address = $emailer->buildFrom();
foreach ($address as $addr) {
    print "<option>".$addr."</option>\n";
}
?>
                </select>
                <label class='UI-label' for="email_subject">
                Subject:</label>
                <input class="UI-input" id="email_subject" placeholder="<subject>">
                <label class='UI-label' for="email_to">
                To (<span id='to_count'>0</span>) :</label>
                <div class="UI-border">
                <select class="UI-input" onchange="toChanged()" id="email_to">
                <option value="None" selected>-- No One --</option>
<?php
$who = $emailer->buildTo();
foreach ($who as $addr) {
    print "<option value=".$addr['EmailListID'].">".$addr['Name']."</option>\n";
}
?>
                </select>
                </div>
                <label class='UI-label' for="email_body">
                Body:</label>
                <div class="UI-input" id="email_body" style="line-height: 2.5ex; height: 50ex;"> </div>
            </div>
            <div class="UI-margin">
            <button onclick='addList();' id='edit_button'
               class="UI-redbutton UI-left UI-hide">
                Edit Lists
            </button>
            <button class='UI-right UI-eventbutton'
                onclick='sendEmail();'>
              Send
            </button>
            <button class='UI-right UI-redbutton' onclick='cancelEmail();'>
              Cancel
            </button>
            </div>
        </div>
    </div>

<div class='UI-sidebar-hidden' id='update_lists_div'>
    <div class='UI-center'>
        <h2 class='UI-red'>Update E-mail Lists</h2>
    </div>
    <div class='UI-center'>
        <label class='UI-label' for='list_name'>
        Lists :</label>
        <div class='UI-border' id="email_lists">
        </div>
    </div>
    <div class='UI-center UI-padding'>
<?php
if ($emailer->canAdd()) {
?>
        <button id='update_list' class='UI-eventbutton'
            onclick='newList();'>
          New
<?php
}
?>
        </button>
        <button class='UI-redbutton'
            onclick='hideSidebar();'>
          Close
        </button>
    </div>
</div>

<div class='UI-sidebar-hidden' id='edit_email'>
    <div class='UI-center'>
        <h2 class='UI-red'>Edit Lists</h2>
    </div>
    <div class='UI-center'>
        <input class="UI-hide" id="email_id" readonly>
        <label class='UI-label' for='email_name'>Name:</label>
        <input class="UI-input" id="email_name">
        <label class='UI-label' for='email_description'>Description:</label>
        <input class="UI-input" id="email_description">
        <label class='UI-label' for='email_code'>SELECT Email FROM `Members` WHERE</label>
        <textarea class="UI-input" id="email_code" rows="5"></textarea>
    </div>
    <div class='UI-center UI-padding'>
        <button id='update_list' class='UI-eventbutton'
            onclick='updateList();'>
          Update
        </button>
        <button id='test_list' class='UI-yellowbutton'
            onclick='testList();'>
          Test
        </button>
        <button id='access_list' class='UI-orangebutton'
            onclick='accessList();'>
          Access
        </button>
        <button class='UI-redbutton'
            onclick='backFromEmail();'>
          Close
        </button>
    </div>
</div>

<div class='UI-sidebar-hidden' id='edit_access'>
    <div class='UI-center'>
        <h2 class='UI-red'>List Access Control<br>'<span id='edit_name'></span>'</h2>
    </div>
    <div class='UI-center' id="access_list_data">
    </div>
    <div class='UI-center UI-padding'>
        <button class='UI-eventbutton'
            onclick='backFromAccess();'>
          Close
        </button>
    </div>
</div>

<!-- Initialize Quill editor -->
<script>
  var quill = new Quill('#email_body', {
    theme: 'snow',
  });

  var lists = [
<?php
$lists = $emailer->buildEdit();
foreach ($lists as $entry) {
    echo "'".base64_encode(json_encode($entry))."'".',';
}
?> ];

<?php
if (\ciab\RBAC::havePermission("site.email_lists")) {
?>
    document.getElementById('edit_button').classList.remove('UI-hide');
<?php
} else {
?>
if (lists.length == 0) {
    document.getElementById('edit_button').classList.add('UI-hide');
} else {
    document.getElementById('edit_button').classList.remove('UI-hide');
}
<?php
}
?>

</script>
