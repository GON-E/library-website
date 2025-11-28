function updateClock() {
    const timePH = new Date();

    const options = {
        weekday: "long",
        month: "long",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
        timeZone: "Asia/Manila"
    };

    document.getElementById("liveClock").innerHTML =
        timePH.toLocaleString("en-US", options);
}

updateClock();
setInterval(updateClock, 1000);
