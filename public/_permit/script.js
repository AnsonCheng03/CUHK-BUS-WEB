function submitform(form, replacecontent, target = "/") {
    const xhr = new XMLHttpRequest();
    const formData = new FormData(form);
    xhr.open("GET", target);
    xhr.onreadystatechange = function () {
        if (document.querySelector(replacecontent) && this.response !== "" && this.readyState == 4) {
            var elm = document.querySelector(replacecontent);
            elm.innerHTML = this.response;

            document.querySelector('.download').style.display = "unset";

            if (form.Type.value == "Transit") {
                document.querySelector('.card').setAttribute('Type', 'Transit')
                document.querySelector('.cardname h1').innerText = '穿梭校巴證'
                document.querySelector('.cardname h2').innerText = 'Shuttle Bus Permit'
                document.querySelector('.card').style.background = 'url("getcard/images/schbus_d.png")'
                for (const box of document.querySelectorAll('.routes .lesson')) {
                    box.style.display = 'none';
                }
            } else {
                document.querySelector('.card').setAttribute('Type', 'Lesson')
                document.querySelector('.cardname h1').innerText = '轉堂校巴證'
                document.querySelector('.cardname h2').innerText = 'Meet-Class Bus Permit'
                document.querySelector('.card').style.background = 'url("getcard/images/schbus_l.png")';
                for (const box of document.querySelectorAll('.routes .transit')) {
                    box.style.display = 'none';
                }
            }
            document.querySelector('.studatas .Name .value span').innerText = form.Name.value ? form.Name.value : 'CHAN Siu-ming （陳小明）'
            document.querySelector('.studatas .SID .value span').innerText = form.SID.value ? form.SID.value : '1155125528'
            document.querySelector('.studatas .Major .value span').innerText = form.Major.value ? form.Major.value : 'B.A. in Fine Arts'
            const today = new Date();
            document.querySelector('.studatas .Valid .value span').innerText = form.Valid.value ? form.Valid.value : today.getDate() + "/" + (today.getMonth() + 1) + "/" + (today.getFullYear() + 1)

        }
    }
    xhr.send(formData);
    return false;
}

function resizecard() {
    let size = window.innerWidth * 0.8 / 560;
    if (size * 356 > document.querySelector('.card').clientHeight) size = document.querySelector('.card').clientHeight / 400
    document.querySelector('.card').style.transform = "scale(" + size + ")"
}

window.addEventListener('load', resizecard);
if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))
    window.addEventListener('resize', resizecard);
document.querySelector('.download').addEventListener('click', () => {
    window.location.href = "savecard/?Type=" + document.querySelector('.card').getAttribute('Type') + "&Name=" + document.querySelector('.studatas .Name .value span').innerText + "&SID=" + document.querySelector('.studatas .SID .value span').innerText + "&Major=" + document.querySelector('.studatas .Major .value span').innerText + "&Valid=" + document.querySelector('.studatas .Valid .value span').innerText;
});