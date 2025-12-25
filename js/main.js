document.addEventListener("DOMContentLoaded", function () {
    function setupPagination(cardSelector, nextBtnSelector, itemsPerPage = 4) {
        const cards = document.querySelectorAll(cardSelector);
        const nextBtn = document.querySelector(nextBtnSelector);
        if (!cards.length || !nextBtn) return;

        let currentPage = 0;
        const totalPages = Math.ceil(cards.length / itemsPerPage);

        function showPage(page) {
            cards.forEach((card, index) => {
                const isVisible = index >= page * itemsPerPage && index < (page + 1) * itemsPerPage;
                card.classList.toggle("d-none", !isVisible);
            });
        }

        // Hiển thị trang đầu
        showPage(currentPage);

        // Xử lý nút "Xem thêm"
        nextBtn.addEventListener("click", () => {
            currentPage = (currentPage + 1) % totalPages;
            showPage(currentPage);
        });
    }

    // Gọi cho từng phần
    setupPagination(".hotel-card", "#hotel-next-btn");
    setupPagination(".tour-card", "#tour-next-btn");
    setupPagination(".flight-card", "#flight-next-btn"); // vé máy bay
});