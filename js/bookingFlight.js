

(function(){
    const adultSel = document.getElementById('adult_count');
    const childSel = document.getElementById('child_count');
    const infantSel = document.getElementById('infant_count');
    const passengerList = document.getElementById('passenger_list');
    const basePrice = parseFloat(document.getElementById('base_price').value || 0);
    const totalPriceEl = document.getElementById('total_price');
  
    // coefficients
    const coeff = { adult: 1.0, child: 0.75, infant: 0.10 };
  
    function renderPassengers() {
      const adults = parseInt(adultSel.value) || 0;
      const childs = parseInt(childSel.value) || 0;
      const infants = parseInt(infantSel.value) || 0;
  
      // validate: at least 1 adult
      if (adults < 1) {
        adultSel.value = 1;
      }
  
      passengerList.innerHTML = ''; // clear
  
      let idx = 1;
      for (let i = 0; i < adults; i++, idx++) {
        passengerList.innerHTML += passengerBlockHtml(idx, 'adult');
      }
      for (let i = 0; i < childs; i++, idx++) {
        passengerList.innerHTML += passengerBlockHtml(idx, 'child');
      }
      for (let i = 0; i < infants; i++, idx++) {
        passengerList.innerHTML += passengerBlockHtml(idx, 'infant');
      }
  
      updateTotal();
    }
  
    function passengerBlockHtml(index, type) {
      const title = type === 'adult' ? 'Người lớn' : (type === 'child' ? 'Trẻ em' : 'Em bé');
      // infant may require fewer fields; but we'll keep same inputs for simplicity
      return `
      <div class="border rounded p-3 mb-3 passenger-block">
        <h6>Hành khách ${index} (${title})</h6>
        <input type="hidden" name="passenger_type[]" value="${type}">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Danh xưng*</label>
            <select class="form-select" name="passenger_title[]" required>
              <option>Ông</option><option>Bà</option><option>Cô</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Họ*</label>
            <input type="text" class="form-control" name="passenger_last_name[]" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Tên đệm và tên*</label>
            <input type="text" class="form-control" name="passenger_first_name[]" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">CCCD / CMND / Passport*</label>
            <input type="text" class="form-control" name="passenger_identity[]" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Ngày sinh*</label>
            <input type="date" class="form-control" name="passenger_dob[]" required>
          </div>
        </div>
      </div>`;
    }
  
    function updateTotal() {
      const adults = parseInt(adultSel.value) || 0;
      const childs = parseInt(childSel.value) || 0;
      const infants = parseInt(infantSel.value) || 0;
  
      const total = adults * basePrice * coeff.adult
                  + childs * basePrice * coeff.child
                  + infants * basePrice * coeff.infant;
  
      // format vietnamese
      totalPriceEl.innerText = total.toLocaleString('vi-VN') + ' VND';
    }
  
    // events
    adultSel.addEventListener('change', renderPassengers);
    childSel.addEventListener('change', renderPassengers);
    infantSel.addEventListener('change', renderPassengers);
  
    // initial render
    renderPassengers();
  
    // final validation before submit: ensure passenger counts match arrays
    document.getElementById('bookingForm').addEventListener('submit', function(e){
      // simple check: number of passenger blocks > = adultCount + childCount + infantCount
      const expected = (parseInt(adultSel.value)||0) + (parseInt(childSel.value)||0) + (parseInt(infantSel.value)||0);
      const blocks = document.querySelectorAll('.passenger-block').length;
      if (blocks !== expected) {
        e.preventDefault();
        alert('Lỗi: số lượng form hành khách không khớp. Vui lòng thử lại.');
        return false;
      }
      // allow submit
    });
  
  })();