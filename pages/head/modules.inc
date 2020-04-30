<script type="text/javascript">
  function updateModules()
  {
    var result = null;
    var table = document.getElementById('module_table');
    var rows = table.getElementsByClassName('module-table-row');
    for (var i = 0; i < rows.length; i++) {
        var id = rows[i].id;
        var checked = document.getElementById('check_' + id).checked;
        if (!checked) {
            if (result == null) {
                result = id;
            } else {
                result += ',' + id;
            }
        }
    }
    if (result == null) {
        result = "";
    }
    window.location.assign('index.php?Function=modules&disabledModules='+result);
  }
</script>
