
document.addEventListener("DOMContentLoaded", function() {
    const payNowRadio = document.getElementById("pay_now");
    const modalEl = document.getElementById("paymentModal");
    
    // Tạo instance modal (nếu chưa có)
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    // Khi chọn "Thanh toán ngay" → show modal
    payNowRadio.addEventListener("change", function() {
        if (this.checked) {
            modal.show();
        }
    });

    // Khi click vào bất kỳ phương thức thanh toán nào → tạm coi là thành công
    const paymentButtons = document.querySelectorAll(".payment-option");
    paymentButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            // Tạm thời coi như thanh toán thành công
            alert("✅ Thanh toán thành công (tạm thời)!");
            modal.hide(); // Đóng modal

            // TODO: sau này gọi API hoặc submit form để lưu booking
        });
    });
    
    // 2. Tính tổng tiền
    function tinhTongTien() {
        const checkin = document.getElementById("checkin").value;
        const checkout = document.getElementById("checkout").value;
        const rooms = parseInt(document.getElementById("rooms").value) || 1;
        const pricePerNight = parseFloat(document.getElementById("price_per_night").value);

        if (!checkin || !checkout) {
            document.getElementById("price-info").innerHTML = "Tổng tiền: <strong>0 VND</strong>";
            return;
        }

        const checkinDate = new Date(checkin);
        const checkoutDate = new Date(checkout);

        let days = (checkoutDate - checkinDate) / (1000 * 60 * 60 * 24);
        if (days <= 0) days = 1;

        const total = days * rooms * pricePerNight;

        document.getElementById("price-info").innerHTML =
            `Số đêm: <strong>${days}</strong> | Số phòng: <strong>${rooms}</strong><br>` +
            `Tổng tiền: <strong>${total.toLocaleString("vi-VN")} VND</strong>`;
    }

    // Gọi khi thay đổi input
    const checkinInput = document.getElementById("checkin");
    const checkoutInput = document.getElementById("checkout");
    const roomsInput = document.getElementById("rooms");

    if(checkinInput) checkinInput.addEventListener("change", tinhTongTien);
    if(checkoutInput) checkoutInput.addEventListener("change", tinhTongTien);
    if(roomsInput) roomsInput.addEventListener("input", tinhTongTien);

    // 3. Modal kết quả booking chỉ hiển thị khi chọn "Thanh toán khi nhận phòng"
    const payLaterRadio = document.getElementById("pay_later");
   

    if(showResultModal) {
        const resultModalEl = document.getElementById("resultModal");
        if(resultModalEl) {
            const resultModal = new bootstrap.Modal(resultModalEl);
            resultModal.show();
        }
    }
});