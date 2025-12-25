document.addEventListener("DOMContentLoaded", () => {
    const searchToggle = document.getElementById("searchToggle");
    const searchBox = document.getElementById("searchBox");
    const searchInput = searchBox.querySelector("input");

    searchToggle.addEventListener("click", () => {
        searchBox.classList.toggle("show");
        if (searchBox.classList.contains("show")) {
            searchInput.focus();
        } else {
            searchInput.value = "";
        }
    });

    // Tự động ẩn nếu click ra ngoài
    document.addEventListener("click", (event) => {
        if (!searchBox.contains(event.target)) {
            searchBox.classList.remove("show");
            searchInput.value = "";
        }
    });
});
