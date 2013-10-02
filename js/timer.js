<!-- STOPWATCH -->

<!-- This goes in the HEAD of the html file -->

<script language="JavaScript" type="text/javascript">
<!-- Copyright 2001, Sandeep Gangadharan -->
<!-- For more free scripts go to http://www.sivamdesign.com/scripts/ -->

<!--
var sec = 0;
var min = 0;
var hour = 0;
function stopwatch(text) {
   sec++;
  if (sec == 60) {
   sec = 0;
   min = min + 1; }
  else {
   min = min; }
  if (min == 60) {
   min = 0; 
   hour += 1; }

if (sec<=9) { sec = "0" + sec; }
   document.clock.stwa.value = ((hour<=9) ? "0"+hour : hour) + " : " + ((min<=9) ? "0" + min : min) + " : " + sec;

  if (text == "Start") { document.clock.theButton.value = "Stop "; }
  if (text == "Stop ") { document.clock.theButton.value = "Start"; }

  if (document.clock.theButton.value == "Start") {
   window.clearTimeout(SD);
   return true; }
SD=window.setTimeout("stopwatch();", 1000);
}

function resetIt() {
  sec = -1;
  min = 0;
  hour = 0;
  if (document.clock.theButton.value == "Stop ") {
  document.clock.theButton.value = "Start"; }
  window.clearTimeout(SD);
 }
// -->
</script>
