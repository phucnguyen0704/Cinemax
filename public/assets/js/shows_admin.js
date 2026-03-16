let viewMode = "day";
let currentDate = new Date();

const rooms = ["Phòng 1", "Phòng 2", "Phòng 3"];

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

let shows = [
  {
    movie: "Avengers",
    room: "Phòng 1",
    date: "2026-03-16",
    start: "10:00",
    color: "movie-a",
  },
  {
    movie: "Batman",
    room: "Phòng 1",
    date: "2026-03-16",
    start: "14:00",
    color: "movie-b",
  },
  {
    movie: "Avatar",
    room: "Phòng 2",
    date: "2026-03-16",
    start: "12:00",
    color: "movie-c",
  },
];

function formatDate(d) {
  return d.toISOString().split("T")[0];
}

function selectTheater(name) {
  document.getElementById("theaterSelectView").style.display = "none";
  document.getElementById("calendarView").style.display = "block";

  document.getElementById("theaterName").innerText = name;

  render();
}

function backToTheater() {
  document.getElementById("calendarView").style.display = "none";
  document.getElementById("theaterSelectView").style.display = "block";
}

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

function render() {
  document.getElementById("currentDate").innerText = currentDate.toDateString();

  if (viewMode === "day") renderDay();
  else renderWeek();
}

function renderDay() {
  let html = `<div class="timeline">`;

  html += `<div class="time-header"><div></div>`;

  times.forEach((t) => {
    html += `<div class="time">${t}</div>`;
  });

  html += `</div>`;

  rooms.forEach((room) => {
    html += `<div class="timeline-row">`;

    html += `<div class="room-label">${room}</div>`;

    times.forEach((time) => {
      html += `<div class="cell"
data-room="${room}"
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

function renderEventsDay() {
  shows.forEach((show, i) => {
    if (show.date !== formatDate(currentDate)) return;

    const row = [...document.querySelectorAll(".timeline-row")].find(
      (r) => r.querySelector(".room-label").innerText === show.room,
    );

    if (!row) return;

    const index = times.indexOf(show.start);

    const cell = row.querySelectorAll(".cell")[index];

    const event = document.createElement("div");

    event.className = `event ${show.color}`;

    event.innerHTML = `

<div>${show.movie}</div>
<div>${show.start}</div>
<div class="event-delete" data-id="${i}">✕</div>

`;

    cell.appendChild(event);
  });

  attachDelete();
}

function attachDayEvents() {
  document.querySelectorAll(".cell").forEach((cell) => {
    cell.onclick = () => {
      document.getElementById("roomField").value = cell.dataset.room;
      document.getElementById("timeField").value = cell.dataset.time;

      document.getElementById("popup").style.display = "block";
    };
  });
}

function renderWeek() {
  let start = new Date(currentDate);

  start.setDate(start.getDate() - start.getDay() + 1);

  let html = `<div class="week-header"><div></div>`;

  for (let i = 0; i < 7; i++) {
    let d = new Date(start);
    d.setDate(start.getDate() + i);

    html += `
<div>
${d.toLocaleDateString("vi-VN", { weekday: "short" })}
<br>
${d.toLocaleDateString()}
</div>
`;
  }

  html += `</div>`;

  times.forEach((time) => {
    html += `<div class="week-row"><div class="time-label">${time}</div>`;

    for (let i = 0; i < 7; i++) {
      let d = new Date(start);
      d.setDate(start.getDate() + i);

      html += `
<div class="week-cell"
data-date="${formatDate(d)}"
data-time="${time}">
</div>
`;
    }

    html += `</div>`;
  });

  document.getElementById("calendar").innerHTML = html;

  attachWeekEvents();
}

function attachWeekEvents() {
  document.querySelectorAll(".week-cell").forEach((cell) => {
    cell.onclick = () => {
      document.getElementById("timeField").value = cell.dataset.time;

      document.getElementById("popup").style.display = "block";
    };
  });
}

function attachDelete() {
  document.querySelectorAll(".event-delete").forEach((btn) => {
    btn.onclick = (e) => {
      e.stopPropagation();

      const id = btn.dataset.id;

      shows.splice(id, 1);

      render();
    };
  });
}

function saveShow() {
  const movie = document.getElementById("movieSelect").value;
  const room = document.getElementById("roomField").value;
  const time = document.getElementById("timeField").value;

  shows.push({
    movie: movie,
    room: room,
    date: formatDate(currentDate),
    start: time,
    color: "movie-a",
  });

  closePopup();

  render();
}

function closePopup() {
  document.getElementById("popup").style.display = "none";
}
