function updateScreens() {
  const theaterId = document.getElementById("modalTheater").value;
  const screenSelect = document.getElementById("modalScreen");

  screenSelect.innerHTML = '<option value="">-- Chọn phòng --</option>';

  if (theaterId && screensData[theaterId]) {
    screenSelect.disabled = false;
    screensData[theaterId].forEach((screen) => {
      const opt = document.createElement("option");
      opt.value = screen.ScreenID;
      opt.textContent = `${screen.Name} (${screen.Capacity} ghế)`;
      screenSelect.appendChild(opt);
    });
  } else {
    screenSelect.disabled = true;
    screenSelect.innerHTML =
      '<option value="">-- Rạp này chưa có phòng --</option>';
  }
}
