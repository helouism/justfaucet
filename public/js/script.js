// Sidebar toggle for mobile
function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  if (sidebar.style.transform === "translateX(0px)") {
    sidebar.style.transform = "translateX(-100%)";
  } else {
    sidebar.style.transform = "translateX(0px)";
  }
}

// Close sidebar when clicking outside on mobile
document.addEventListener("click", function (e) {
  const sidebar = document.getElementById("sidebar");
  const sidebarToggle = document.querySelector(".sidebar-toggle");

  if (
    window.innerWidth <= 768 &&
    !sidebar.contains(e.target) &&
    !sidebarToggle.contains(e.target)
  ) {
    sidebar.style.transform = "translateX(-100%)";
  }
});
