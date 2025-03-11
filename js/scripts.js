document.addEventListener("DOMContentLoaded", function() {
    const alertBoxes = document.querySelectorAll(".alert");
    alertBoxes.forEach(alert => {
        setTimeout(() => {
            alert.style.display = "none";
        }, 3000);
    });
});
