document.addEventListener("DOMContentLoaded", function() {
    let toggleBtn = document.getElementById("toggleBtn");
    let sidebar = document.querySelector(".sidebar");
    let header = document.querySelector(".header");
    let content = document.querySelector(".content");

    toggleBtn.addEventListener("click", function() {
        let isOpen = sidebar.classList.toggle("open");

        if (isOpen) {
            header.classList.add("sidebar-open");
            content.classList.add("sidebar-open");
        } else {
            header.classList.remove("sidebar-open");
            content.classList.remove("sidebar-open");
        }
    })
})