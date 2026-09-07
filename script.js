document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.querySelector(".menu-toggle");
    const nav = document.querySelector("#mainNav");

    toggle?.addEventListener("click", () => {
        const open = nav.classList.toggle("open");
        toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });

    document.querySelectorAll(".main-nav a").forEach(link => {
        link.addEventListener("click", () => nav.classList.remove("open"));
    });

    document.querySelector("#newsletterForm")?.addEventListener("submit", (e) => {
        e.preventDefault();
        const email = document.querySelector("#email").value.trim();
        if (!email) return;
        alert("Thank you! Your email has been added to our list.");
        e.target.reset();
    });
});
