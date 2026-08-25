// Small interactivity for the homepage category cards
document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".category-card");

    cards.forEach(card => {
        card.addEventListener("click", function (e) {
            // Only navigate if the click wasn't already on the button
            if (e.target.tagName !== "A") {
                const link = card.querySelector(".donate-btn");
                if (link) window.location.href = link.href;
            }
        });
    });
});