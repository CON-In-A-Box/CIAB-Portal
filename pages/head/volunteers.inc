<script src="sitesupport/barcode/quagga.min.js"></script>
<script src="sitesupport/barcode/barcode.js"></script>
<link rel="stylesheet" type="text/css" href="sitesupport/barcode/barcode.css" />
<script>
    function showHideSoldOut() {
        unhide = (document.getElementById("soldoutcheck").checked);
        var table = document.getElementById("prizes");
        for (var i = 0, row; row = table.rows[i]; i++) {
            if (unhide && row.className.includes(" hiddenrow")) {
                row.className = row.className.replace(" hiddenrow", "");
            } else if (!unhide && row.className.includes("soldoutrow")) {
                if (row.className.indexOf(" hiddenRow") == -1)
                    row.className += " hiddenrow";
            }
        }
    }
</script>
<style>
    .hiddenrow {
        display:none;
    };
</style>
