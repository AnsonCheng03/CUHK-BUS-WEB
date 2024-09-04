function append_query(query, value) {
  const urlSearchParams = new URLSearchParams(window.location.search);
  const params = Object.fromEntries(urlSearchParams.entries());
  query_arr = Object.keys(params).map((key) => [key.toString()]);
  value_arr = Object.keys(params).map((key) => [params[key]]);
  index = query_arr.findIndex((element) => element == query);
  if (index < 0) {
    var loc = location.href;
    loc += location.href.indexOf("?") === -1 ? "?" : "&";
    location.href = loc + query + "=" + value;
  } else {
    value_arr[index] = value;
    var querypath = "";
    query_arr.forEach((element, index) => {
      querypath = querypath + "&" + element + "=" + value_arr[index];
    });
    var finalurl = location.href.split("?")[0] + "?" + querypath;
    location.href = finalurl;
  }
}

// Home Screen Prompt
function platformCheck() {
  const isiOS = /iphone|ipad|ipod/.test(
    window.navigator.userAgent.toLowerCase()
  );
  const isiPadOS =
    navigator.platform === "MacIntel" && navigator.maxTouchPoints > 1;
  return isiOS || isiPadOS;
}

function comparetime() {
  if (localStorage.getItem("dismisshomescreen") == null) return false;
  const savedtime = localStorage.getItem("dismisshomescreen");
  var dissmisstime = Date.parse(savedtime),
    currenttime = new Date();
  currenttime.setHours(currenttime.getHours() - 24);
  return dissmisstime - currenttime > 0;
}

if (window.matchMedia("(display-mode: standalone)").matches) {
  document.getElementById("refresh-btn").style.display = "inline-block";
} else {
  if (!comparetime())
    if (platformCheck()) {
      document.getElementById("HomeScreenPrompt").style.display = "block";
    } else {
      window.addEventListener("beforeinstallprompt", (ev) => {
        ev.preventDefault();
        document.querySelector("#AndroidHomeScreenPrompt").style.display =
          "unset";
        document
          .querySelector("#AndroidHomeScreenPrompt .Addtohomebtn")
          .addEventListener("click", () => {
            ev.prompt();
            document.querySelector("#AndroidHomeScreenPrompt").remove();
          });
      });
    }
}
