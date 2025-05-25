// ACTIVE PAGE ON SIDEBAR
document.addEventListener("DOMContentLoaded", function () {
  // Get current path from URL
  const currentPath = window.location.pathname.split("/").pop() || "dashboard";

  // Find all sidebar links
  const sidebarLinks = document.querySelectorAll(".sidebar-menu a[data-page]");

  // Remove any existing active classes
  sidebarLinks.forEach((link) => link.classList.remove("active"));

  // Add active class to current page link
  const activeLink = document.querySelector(
    `.sidebar-menu a[data-page="${currentPath}"]`
  );
  if (activeLink) {
    activeLink.classList.add("active");
  }
});
