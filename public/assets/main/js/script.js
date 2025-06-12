document.addEventListener("DOMContentLoaded", function () {
  // Dark mode functionality
  const htmlElement = document.documentElement;
  const switchElement = document.getElementById("darkModeSwitch");

  // Set the default theme to dark if no setting is found in local storage
  const currentTheme = localStorage.getItem("bsTheme") || "dark";
  htmlElement.setAttribute("data-bs-theme", currentTheme);
  switchElement.checked = currentTheme === "dark";

  switchElement.addEventListener("change", function () {
    if (this.checked) {
      htmlElement.setAttribute("data-bs-theme", "dark");
      localStorage.setItem("bsTheme", "dark");
    } else {
      htmlElement.setAttribute("data-bs-theme", "light");
      localStorage.setItem("bsTheme", "light");
    }
  });

  // Auto-close sidebar on mobile when clicking links
  const sidebarLinks = document.querySelectorAll(
    "#sidebar .list-group-item-action"
  );
  const sidebar = document.getElementById("sidebar");

  if (window.innerWidth < 992) {
    sidebarLinks.forEach((link) => {
      link.addEventListener("click", function () {
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebar);
        if (bsOffcanvas) {
          bsOffcanvas.hide();
        }
      });
    });
  }

  // Handle window resize for responsive behavior
  window.addEventListener("resize", function () {
    const sidebar = document.getElementById("sidebar");
    if (window.innerWidth >= 992) {
      const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebar);
      if (bsOffcanvas) {
        bsOffcanvas.hide();
      }
    }
  });
});
