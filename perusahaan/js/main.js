// js/main.js
$(document).ready(function () {
  // Sidebar toggle untuk mobile
  $("#menuToggle").click(function () {
    $(".sidebar").toggleClass("active");
  });

  // Avatar dropdown
  $("#avatarDropdownBtn").click(function (e) {
    e.stopPropagation();
    $("#dropdownMenu").toggle();
  });

  $(document).click(function () {
    $("#dropdownMenu").hide();
  });

  // Logout confirmation - disabled karena sudah ada modal custom di sidebar.php
  // $('#logoutBtn, #logoutDropdown').click(function(e) {
  //     e.preventDefault();
  //     if (confirm('Anda yakin ingin logout?')) {
  //         window.location.href = 'logout.php';
  //     }
  // });

  // Auto close alert setelah 3 detik
  setTimeout(function () {
    $(".alert").fadeOut();
  }, 3000);
});
