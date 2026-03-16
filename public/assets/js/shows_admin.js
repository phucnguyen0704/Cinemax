/* =============================
DATA
============================= */
console.log(initialShows);
console.log(initialHalls);

let viewMode = "day";
let currentDate = new Date();
let selectedCinema = null;

const times = [
  "08:00",
  "10:00",
  "12:00",
  "14:00",
  "16:00",
  "18:00",
  "20:00",
  "22:00",
];

let shows = [];
let halls = [];
let cinemas = [];
let movies = [];
let rooms = [];

/* =============================
LOAD DATA FROM PHP
============================= */

if (window.initialShows) {
  shows = window.initialShows.map((s) => ({
    show_id: s.show_id,
    movie: s.movie_title,
    room: s.hall_name,
    cinema_id: s.cinema_id,
    cinema: s.cinema_name,
    show_date: s.show_date,
    start_time: s.start_time,
    end_time: s.end_time,
    base_price: s.base_price,
    movie_id: s.movie_id,
    hall_id: s.hall_id,
    status: s.status,
  }));
}

if (window.initialHalls) {
  halls = window.initialHalls.map((h) => ({
    hall_id: h.hall_id,
    hall_name: h.name,
    cinema_id: h.cinema_id,
  }));
}

if (window.initialCinemas) {
  cinemas = window.initialCinemas;
}

if (window.initialMovies) {
  movies = window.initialMovies;
}

/* =============================
UTILS
============================= */

function formatDate(d) {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  return `${y}-${m}-${day}`;
}

function getShowColor(status) {
  switch (Number(status)) {
    case 0:
      return "#facc15";
    case 1:
      return "#22c55e";
    case -1:
      return "#e5e7eb";
    default:
      return "#e5e7eb";
  }
}

/* =============================
THEATER SELECT
============================= */

function selectTheater(cinema_id) {
  selectedCinema = cinema_id;
  sessionStorage.setItem("selectedCinema", cinema_id);

  document.getElementById("theaterSelectView").style.display = "none";
  document.getElementById("calendarView").style.display = "block";

  const cinema = cinemas.find((c) => c.cinema_id == cinema_id);

  document.getElementById("theaterName").innerText = cinema?.cinema_name || "";

  rooms = halls.filter((h) => h.cinema_id == cinema_id);

  render();
}

function backToTheater() {
  sessionStorage.removeItem("selectedCinema");
  document.getElementById("calendarView").style.display = "none";
  document.getElementById("theaterSelectView").style.display = "block";
}

/* =============================
VIEW CONTROL
============================= */

function setView(mode, e) {
  viewMode = mode;

  document
    .querySelectorAll(".view-btn")
    .forEach((b) => b.classList.remove("active"));

  e.target.classList.add("active");

  render();
}

function prev() {
  if (viewMode === "day") currentDate.setDate(currentDate.getDate() - 1);
  else currentDate.setDate(currentDate.getDate() - 7);

  render();
}

function next() {
  if (viewMode === "day") currentDate.setDate(currentDate.getDate() + 1);
  else currentDate.setDate(currentDate.getDate() + 7);

  render();
}

/* =============================
RENDER
============================= */

function render() {
  document.getElementById("currentDate").innerText = currentDate.toDateString();

  if (viewMode === "day") renderDay();
  else renderWeek();
}

/* =============================
DAY VIEW
============================= */

function renderDay() {
  let html = `<div class="timeline">`;

  html += `<div class="time-header"><div></div>`;

  times.forEach((t) => {
    html += `<div class="time">${t}</div>`;
  });

  html += `</div>`;

  rooms.forEach((room) => {
    html += `<div class="timeline-row">`;

    html += `<div class="room-label">${room.hall_name}</div>`;

    times.forEach((time) => {
      html += `
      <div class="cell"
      data-hall-id="${room.hall_id}"
      data-room="${room.hall_name}"
      data-time="${time}">
      </div>`;
    });

    html += `</div>`;
  });

  html += `</div>`;

  document.getElementById("calendar").innerHTML = html;

  renderEventsDay();
  attachDayEvents();
}

/* =============================
DAY EVENTS
============================= */

function renderEventsDay() {
  shows.forEach((show, i) => {
    if (show.show_date !== formatDate(currentDate)) return;

    if (selectedCinema && show.cinema_id != selectedCinema) return;

    const row = [...document.querySelectorAll(".timeline-row")].find(
      (r) => r.querySelector(".room-label").innerText === show.room,
    );

    if (!row) return;

    const start = show.start_time.substring(0, 5);
    const end = show.end_time.substring(0, 5);

    const index = times.indexOf(start);

    if (index === -1) return;

    const cell = row.querySelectorAll(".cell")[index];

    const event = document.createElement("div");

    event.className = "event";
    event.style.background = getShowColor(show.status);

    event.innerHTML = `
<div class="event-title">${show.movie}</div>
<div class="event-time">${start}-${end}</div>
<div class="event-delete" data-id="${i}">✕</div>
`;

    event.onclick = (e) => {
      e.stopPropagation();
      openShowDetail(show);
    };

    cell.appendChild(event);

    // 🔴 đánh dấu cell đã có show
    cell.classList.add("has-show");
  });

  attachDelete();
}

function openShowDetail(show) {
  document.getElementById("popupTitle").innerText = "Chi tiết suất chiếu";

  showId.value = show.show_id;

  movieSelect.value = show.movie_id;
  roomField.value = show.room;

  startTimeField.value = show.start_time.substring(0, 5);
  endTimeField.value = show.end_time.substring(0, 5);

  priceField.value = show.base_price;
  statusField.value = show.status;

  statusWrapper.style.display = "block";

  btnSave.style.display = "none";
  btnUpdate.style.display = "inline-block";

  if (show.status == -1) {
    btnUpdate.style.display = "none";

    movieSelect.disabled = true;
    startTimeField.disabled = true;
    priceField.disabled = true;
    statusField.disabled = true;
  }

  document.getElementById("popup").style.display = "block";
}

/* =============================
CELL CLICK
============================= */

function attachDayEvents() {
  document.querySelectorAll(".cell").forEach((cell) => {
    cell.onclick = () => {
      // 🔴 nếu có show thì không add
      if (cell.classList.contains("has-show")) return;

      const hallId = cell.dataset.hallId;
      const roomName = cell.dataset.room;
      const time = cell.dataset.time;

      openAddPopup(hallId, roomName, time);
    };
  });
}

/* =============================
DELETE
============================= */

function attachDelete() {
  document.querySelectorAll(".event-delete").forEach((btn) => {
    btn.onclick = (e) => {
      e.stopPropagation();

      const id = btn.dataset.id;

      shows = shows.filter((_, i) => i != id);

      render();
    };
  });
}

/* =============================
POPUP
============================= */

function openAddPopup(hallId, roomName, startTime) {
  const popup = document.getElementById("popup");

  currentMode = "add";
  currentShow = null;

  // reset form
  document.getElementById("showId").value = "";
  document.getElementById("showDateField").value = formatDate(currentDate);
  document.getElementById("movieSelect").selectedIndex = 0;

  document.getElementById("hallIdField").value = hallId;
  document.getElementById("roomField").value = roomName;

  document.getElementById("startTimeField").value = startTime;
  document.getElementById("endTimeField").value = "";

  document.getElementById("priceField").value = "";

  document.getElementById("statusField").value = "0";
  document.getElementById("statusWrapper").style.display = "block";

  document.getElementById("btnSave").style.display = "inline-block";
  document.getElementById("btnUpdate").style.display = "none";

  document.getElementById("popupTitle").innerText = "Thêm suất chiếu";

  popup.style.display = "block";
}

function closePopup() {
  document.getElementById("popup").style.display = "none";
}

/* =============================
TIME CALC
============================= */

function calculateEndTime() {
  const movie = document.getElementById("movieSelect");
  const duration = movie.selectedOptions[0].dataset.duration;

  const start = document.getElementById("startTimeField").value;

  if (!start || !duration) return;

  let [h, m] = start.split(":").map(Number);

  let total = h * 60 + m + parseInt(duration);

  let endH = Math.floor(total / 60);
  let endM = total % 60;

  endH = String(endH).padStart(2, "0");
  endM = String(endM).padStart(2, "0");

  document.getElementById("endTimeField").value = `${endH}:${endM}`;
}

document.getElementById("movieSelect").onchange = calculateEndTime;
document.getElementById("startTimeField").onchange = calculateEndTime;

/* =============================
AUTO RESTORE STATE
============================= */

(function restoreState() {
  const saved = sessionStorage.getItem("selectedCinema");
  const params = new URLSearchParams(window.location.search);
  const isRedirect =
    params.has("add") || params.has("update") || params.has("delete");

  if (saved && isRedirect) {
    selectTheater(saved);
  }
})();
