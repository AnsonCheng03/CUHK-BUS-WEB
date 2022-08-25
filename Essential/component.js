//After load
if (document.getElementById("deptnow").checked) {
  if(document.getElementById("time-now")) document.getElementById("time-now").style.display = "block";
} else {
  if(document.getElementById("time-now")) document.getElementById("time-schedule").style.display = "block";
}

if(StandaloneCheck()){
  if(platformCheck() && !comparetime()){
    document.getElementById("HomeScreenPrompt").style.display = "block";
  }
}
else {
  document.getElementById("refresh-btn").style.display = "";
}

if(document.getElementById("routeresult") && document.getElementById("routesubmitbtn")){
  document.getElementById("routesubmitbtn").scrollIntoView();
}

date_change();

function modechange() {
  const mode = document.querySelector('input[name="mode"]:checked').value;
  document.querySelectorAll('*[mode]:not([mode="'+ mode +'"])').forEach(element => {
    element.style.display = 'none';
  });
  document.querySelectorAll('*[mode="'+ mode +'"]').forEach(element => {
    element.style.display = 'unset';
  });
}


window.addEventListener('load', modechange);
document.querySelectorAll('input[name="mode"]').forEach(element => {
  element.addEventListener('change', modechange);
});