<br>
<br>
<footer>
<?php
if ($_REQUEST['Function'] == "public") {
    require($PAGESDIR.'/base/disclaimer.inc');
} else {
?>
    <div class="UI-footer-div">
    <div class='UI-footer'>
    <img class='UI-footer-icon' src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' alt='Event Icon'>
    <button class='UI-footer-issues-bar-item' onclick='window.open("mailto: <?php echo getFeedbackEmail(); ?>?subject=Con-in-a-Box Feedback");'><i class="fas fa-comments" aria-hidden="true"></i>&nbsp;Feedback</button>
    </div>
    </div>
<?php
}
?>
</footer>
