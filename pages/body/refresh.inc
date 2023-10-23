
<script>
apiRefresh()
.then(() => {
    var json = JSON.parse(localStorage.getItem('ciab_apiAuthorization'));
    var token = json.refresh_token;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4) {
        var data = new URLSearchParams(window.location.search);
        var target = decodeURI(data.get('return'));
        if (target) {
            window.location = target;
        }
      }
    };
    xhttp.open('POST', '/index.php?Function=update_token', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('new_token='+json.access_token);
})
.catch(() => {
    // Force Logout
    localStorage.clear();
    window.location = 'index.php?Function=public&signout=1';
});
</script>

<div id="page" class="UI-maincontent">
<h2>Renewing Authentication Tokens... Please Wait...</h2>
</div>
