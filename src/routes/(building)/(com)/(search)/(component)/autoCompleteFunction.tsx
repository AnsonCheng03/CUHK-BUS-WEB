export function autoComplete(inp: HTMLInputElement, arr: string[]) {
  let currentFocus: number;

  inp.addEventListener("input", function (this: HTMLInputElement) {
    const val = this.value;
    closeAllLists();
    if (!val) {
      return false;
    }
    currentFocus = -1;
    const a = document.createElement("DIV");
    a.setAttribute("id", this.id + "autoComplete-list");
    a.setAttribute("class", "autoComplete-items");
    this.parentNode?.appendChild(a);

    for (let i = 0; i < arr.length; i++) {
      if (arr[i].toUpperCase().includes(val.toUpperCase())) {
        const b = document.createElement("DIV");
        b.innerHTML = arr[i].substr(0, val.length);
        b.innerHTML += arr[i].substr(val.length);
        b.innerHTML += `<input type='hidden' value='${arr[i]}'>`;
        b.addEventListener("click", function (this: HTMLElement) {
          inp.value = this.getElementsByTagName("input")[0].value;
          closeAllLists();
        });
        a.appendChild(b);
      }
    }
  });

  inp.addEventListener("keydown", function (e) {
    const x = document.getElementById(this.id + "autoComplete-list");
    if (x) {
      const divElements = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        currentFocus++;
        addActive(divElements);
      } else if (e.keyCode == 38) {
        currentFocus--;
        addActive(divElements);
      } else if (e.keyCode == 13) {
        if (currentFocus > -1) {
          if (divElements) divElements[currentFocus]?.click();
        }
      }
    }
  });

  function addActive(x: HTMLCollectionOf<HTMLDivElement>) {
    if (!x) return false;
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = x.length - 1;
    x[currentFocus]?.classList.add("autoComplete-active");
  }

  function removeActive(x: HTMLCollectionOf<HTMLDivElement>) {
    for (let i = 0; i < x.length; i++) {
      x[i]?.classList.remove("autoComplete-active");
    }
  }

  function closeAllLists(elmnt?: HTMLElement) {
    const x = document.getElementsByClassName("autoComplete-items");
    for (let i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i]?.parentNode?.removeChild(x[i]);
      }
    }
  }
}
