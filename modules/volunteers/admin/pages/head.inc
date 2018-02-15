<script src="sitesupport/barcode/quagga.min.js"></script>
<script src="sitesupport/barcode/barcode.js"></script>
<script src="sitesupport/common.js"></script>
<script src="modules/volunteers/sitesupport/volunteers.js"></script>
<script src="sitesupport/authentication.js"></script>
<script src="sitesupport/lookupuser.js"></script>
<link rel="stylesheet" type="text/css" href="sitesupport/barcode/barcode.css" />
<style>
.hiddenrow {
    display:none;
};

#switch-table td {
    vertical-align: middle;
    text-align: right;
    padding-right: 10px;
    vertical-align: center;
    background-repeat: no-repeat;
    background-position: left center;
}

.switch {
  vertical-align: middle;
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide default HTML checkbox */
.switch input {display:none;}

/* The slider */
.slider {
  vertical-align: middle;
  position: absolute;
  cursor: pointer;
  width: 60px;
  height: 33px;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  border-style: solid;
  border-color: black;
  border-width: 1px;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 24px;
  left: 4px;
  bottom: 2px;
  background-color: white;
  border-style: solid;
  border-color: black;
  border-width: 1px;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: yellow;
}

input:focus + .slider {
  box-shadow: 0 0 1px yellow;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}
</style>
