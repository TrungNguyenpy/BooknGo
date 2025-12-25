// 🟢 Chờ DOM sẵn sàng để tránh lỗi getElementById null
document.addEventListener("DOMContentLoaded", function () {
    // 1) LẤY GIÁ VÉ TỪ data-attribute
    const ticketData = document.getElementById("ticketData");
    const prices = {
      adult: parseInt(ticketData.dataset.priceAdult) || 0,
      child: parseInt(ticketData.dataset.priceChild) || 0
    };
    // 2) HÀM MỞ / ĐÓNG MODAL
    function showModal() {
      document.getElementById("successModal").style.display = "flex";
    }
    function closeModal() {
      document.getElementById("successModal").style.display = "none";
    }
    window.closeModal = closeModal;
    // 3) TĂNG / GIẢM SỐ LƯỢNG VÉ
    window.changeQty = function(type, delta) {
      const input = document.getElementById(type + "Qty");
      let value = parseInt(input.value) + delta;
      if (value < 0) value = 0;
      input.value = value;
      updateSummary();
    };
    // 4) CẬP NHẬT TÓM TẮT VÉ
    function updateSummary() {
      const adult = parseInt(document.getElementById("adultQty").value);
      const child = parseInt(document.getElementById("childQty").value);
      const total = adult * prices.adult + child * prices.child;
  
      document.getElementById("adultCount").innerText = adult;
      document.getElementById("childCount").innerText = child;
      document.getElementById("totalPrice").innerText = total.toLocaleString("vi-VN") + " VND";
      document.getElementById("footerTotal").innerText = total.toLocaleString("vi-VN") + " VND";
  
      // Gán giá trị vào các input ẩn
      document.getElementById("adultQtyInput").value = adult;
      document.getElementById("childQtyInput").value = child;
      document.getElementById("totalPriceInput").value = total;
    }
    // Gọi ban đầu để set mặc định (ví dụ 0 vé)
    updateSummary();
    // 5) XỬ LÝ SUBMIT FORM (AJAX)
    const bookingForm = document.getElementById("bookingForm");
    bookingForm.addEventListener("submit", function(e) {
      e.preventDefault();
  
      const formData = new FormData(bookingForm);
  
      fetch(bookingForm.action, {
        method: "POST",
        body: formData
      })
      .then(res => res.text())
      .then(data => {
        try {
          const json = JSON.parse(data);
          if (json.success) {
            showModal();
            bookingForm.reset();
            updateSummary();
          } else {
            alert(json.message || "❌ Có lỗi xảy ra khi đặt vé.");
          }
        } catch {
          alert("❌ Lỗi máy chủ: " + data);
        }
      })
      .catch(err => {
        alert("❌ Lỗi kết nối: " + err);
      });
    });
  

      
  
  });
  