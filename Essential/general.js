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
