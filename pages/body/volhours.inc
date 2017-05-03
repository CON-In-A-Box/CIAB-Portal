<script>
function lookupId(id) {
  document.getElementById('volunteername').innerHTML = id
}

function calculateHours() {
  document.getElementById('totalhours').innerHTML = document.getElementById('hours').value * document.getElementById('modifier').value
}
</script>

<div id="main_content" class="w3-cell w3-cell-top w3-mobile">

<form class="w3-container" method="post" action="index.php?Function=volhours">

<label>Volunteer Badge Number</label>
<input class="w3-input w3-border" type=text name="volunteer" id="volunteer" onchange="lookupId(this.value);" placeholder="Volunteer Badge Number">

<label>Actual Hours Worked</label>
<input class="w3-input w3-border" type=text name="hours" id="hours" onchange="calculateHours();" placeholder="Actual Hours Worked">

<label>Volunteer Time Modifier - Must be per policy</label>
<select class="w3-select w3-border" name="modifier" id="modifier" onchange="calculateHours();">
  <option value=1 selected>Normal - 1 hour = 1 hour</option>
  <option value=0.5>Half Time - 1 hour = 0.5 hours credit</option>
  <option value=1.5>Time Plus Half - 1 hour = 1.5 hours credit</option>
  <option value=2>Double - 1 hour = 2 hours credit</option>
</select>

<div class="w3-container">
  <p name=summery>
    Crediting
    <span id="totalhours">0</span>
    hours to
    <span id="volunteername">a Volunteer</span>
  </p>
</div>

<button class="w3-button w3-block w3-brown">Submit Hours</button>
</form>
</div><!--close main_content div-->
