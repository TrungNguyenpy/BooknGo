
const dateItems = document.querySelectorAll(".date-item");
function showTickets(date) {
    ticketsContainer.innerHTML = "";
    const tickets = ticketsByDate[date] || [];
    if (tickets.length === 0) {
        ticketsContainer.innerHTML = "<p>Chưa có vé cho ngày này.</p>";
        return;
    }
    tickets.forEach(t => {
        const div = document.createElement("div");
        div.classList.add("ticket-card");
        div.innerHTML = `
            <div class="ticket-title">${t.title}</div>
            <div class="ticket-desc">${t.description}</div>
            ${t.extra ? `<div class="ticket-extra">${t.extra}</div>` : ""}
            ${t.top_pick ? `<span class="top-pick">${t.top_pick}</span>` : ""}
            <div>
                <span class="ticket-price">${Number(t.price).toLocaleString('vi-VN')} VND</span>
                ${t.old_price ? `<span class="ticket-old-price">${Number(t.old_price).toLocaleString('vi-VN')} VND</span>` : ""}
                ${t.discount_percent ? `<span class="discount">-${t.discount_percent}%</span>` : ""}
                
                <!-- Đây là chỗ đổi button thành link -->
                <a href="../services/booking-form.php?type=tour&ticket_id=${t.id}" 
                   class="btn btn-success">
                   Chọn vé
                </a>
            </div>
        `;
        ticketsContainer.appendChild(div);
    });
}


// Hiển thị vé của ngày đầu tiên
showTickets(dateItems[0].dataset.date);

// Click chọn ngày
dateItems.forEach(item => {
    item.addEventListener("click", () => {
        dateItems.forEach(i => i.classList.remove("active"));
        item.classList.add("active");
        showTickets(item.dataset.date);
    });
});