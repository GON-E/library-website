

let timePH = new Date(initialTime);

setInterval(() => {
    timePH.setSeconds(timePH.getSeconds() + 1);
    document.getElementById("liveClock").innerHTML =
        timePH.toLocaleString("en-US", { timeZone: "Asia/Manila" });

           const options = {
        month: "long",   // ← This makes it show the FULL month name
        day: "numeric",
        year: "numeric",
        hour: "numeric",
        minute: "numeric",
        second: "numeric"
    };

    document.getElementById("liveClock").innerHTML =
        timePH.toLocaleString("en-US", options);
}, 1000);

