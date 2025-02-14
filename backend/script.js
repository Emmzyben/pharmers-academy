function closeNotification() {
    const notificationBar = document.getElementById("notificationBar");
    if (notificationBar) {
        notificationBar.style.right = "-100%"; // Slide out to the right
        setTimeout(() => {
            notificationBar.style.display = "none";
        }, 500);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const notificationBar = document.getElementById("notificationBar");
    if (notificationBar) {
        notificationBar.style.display = "block";
        setTimeout(closeNotification, 5000);
    }
});
