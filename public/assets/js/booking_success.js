const btnSave = document.getElementById("btnSaveImage");
if (btnSave) {
  btnSave.addEventListener("click", function () {
    const ticketElement = document.getElementById("ticketCapture");
    html2canvas(ticketElement, {
      scale: 2,
      useCORS: true,
    }).then((canvas) => {
      const link = document.createElement("a");
      link.download = "CinemaHub-Ticket-<?php echo $booking_id; ?>.png";
      link.href = canvas.toDataURL();
      link.click();
    });
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const btn_reload = document.querySelector(".btn-reload");
  const ticket_wrapper = document.querySelector(".ticket-wrapper");
  const pending_card = document.querySelector(".pending-card");

  if (btn_reload && ticket_wrapper && pending_card) {
    btn_reload.addEventListener("click", function () {
      ticket_wrapper.style.display = "block";
      pending_card.style.display = "none";
    });
  }
});
