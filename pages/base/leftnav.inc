<?php
echo '<div id="left_nav" class="w3-cell UI-border w3-hide-small">';
echo '<img src="/resources/images/members/'.$_SESSION['accountId'].'.jpg" onerror='."'this.classList.add(".'"event-default-user-icon")'."'>";

echo "<p style=\"font-weight:bold;\">".$_SESSION['preferredName']."</p>\n";

if (isset($_SESSION['addr1']) && !empty($_SESSION['addr1'])) {
    echo "<p><span style=\"font-weight:bold;\">Address:</span><br />\n";
    echo $_SESSION['addr1']."<br />\n";
    
    if (isset($_SESSION['addr2']) && !empty($_SESSION['addr2'])) {
        echo $_SESSION['addr2']."<br />\n";
    }
    if (isset($_SESSION['city']) && !empty($_SESSION['city'])) {
        echo $_SESSION['city'].", ";
    }
    if (isset($_SESSION['state']) && !empty($_SESSION['state'])) {
        echo $_SESSION['state']." ";
    } elseif (isset($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['province']) && !empty($_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['province'])) {
        echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['province'];
    }
    if (isset($_SESSION['zip']) && !empty($_SESSION['zip'])) {
        echo '<br />'.$_SESSION['zip']."</p>\n";
    }
}

if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
    echo "<p><b>Email:</b><br />";
    echo '<span class="w3-tiny">'.$_SESSION['email']."</span></p>\n";
}

if (isset($_SESSION['phone']) && !empty($_SESSION['phone'])) {
    echo "<p><b>Telephone Number:</b><br />";
    echo $_SESSION['phone'].'</p>';
}

echo "<p><b>Badge Number:</b><br />";
echo $_SESSION['accountId'].'</p>';
echo '</div><!--left_nav-->';
