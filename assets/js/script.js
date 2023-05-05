const sideBarBtn = document.getElementById("sidebarBtn");
const sideBar = document.getElementById('sidebar');

sideBarBtn.addEventListener('click', function() {
    sideBar.classList.toggle('active');
});