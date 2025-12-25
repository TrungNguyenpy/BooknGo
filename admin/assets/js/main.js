
document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById('sidebar');
  const menuList = document.getElementById('menuList');
  const pageTitle = document.getElementById('pageTitle');
  const views = document.querySelectorAll('[id^="view-"]');
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');

  // Hiển thị view (tham số là tên view, ví dụ "users")
  function showView(view) {
    views.forEach(v => v.classList.add('d-none'));
    const el = document.getElementById('view-' + view);
    if (el) el.classList.remove('d-none');

    // set active class trên menu
    document.querySelectorAll('#menuList .nav-link').forEach(a => a.classList.remove('active'));
    const active = document.querySelector(`#menuList .nav-link[data-view='${view}']`);
    if (active) active.classList.add('active');

    pageTitle.textContent = active ? active.textContent.trim() : "Dashboard";
  }

  // Chuẩn hóa hash: chấp nhận "users" hoặc "view-users" -> trả về "users"
  function normalizeHash(rawHash) {
    if (!rawHash) return '';
    let h = rawHash.replace('#','');
    if (h.startsWith('view-')) h = h.slice(5); // remove 'view-' prefix
    return h;
  }

  // Menu click: ngăn default để không tạo hash dạng khác, sau đó set chuẩn
  if (menuList) {
    menuList.addEventListener('click', e => {
      const a = e.target.closest('.nav-link');
      if (!a) return;
      e.preventDefault(); // ngăn anchor mặc định (tránh #view-xxx nếu href khác)
      const view = a.dataset.view;
      if (!view) return;
      showView(view);
      // set hash thành dạng "users" (không có "view-") để script dễ xử lý
      if (window.location.hash.replace('#','') !== view) {
        window.location.hash = view;
      }
      // đóng sidebar mobile nếu mở
      sidebar.classList.remove('show');
    });
  }

  if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  }

  // Khi hash thay đổi (back/forward hoặc người paste url), load view tương ứng
  window.addEventListener("hashchange", () => {
    const raw = window.location.hash;
    const view = normalizeHash(raw);
    if (view && document.getElementById(`view-${view}`)) {
      showView(view);
    }
  });

  // Lần đầu load: lấy hash hiện tại (normalize) rồi show
  const initialRaw = window.location.hash;
  const initialView = normalizeHash(initialRaw);
  if (initialView && document.getElementById(`view-${initialView}`)) {
    showView(initialView);
  } else {
    showView('dashboard');
  }

  console.log("✅ Admin panel loaded successfully");
});

