// Login
function togglePassword() {
    var input = document.getElementById('password');
    var eyeOpen = document.getElementById('icon-eye-open');
    var eyeClosed = document.getElementById('icon-eye-closed');

    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        input.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}

// SideBar
var sidebar = document.getElementById('sidebar');
var overlay = document.getElementById('sidebar-overlay');

function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
}

function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
}

// Brands
function previewImage(input) {
    var container = document.getElementById('preview-container');
    var image = document.getElementById('preview-image');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            image.src = e.target.result;
            container.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        container.classList.add('hidden');
    }
}

// Add Brands
// Show image preview when file is selected
function previewImage(input) {
    var container = document.getElementById('preview-container');
    var image = document.getElementById('preview-image');

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            image.src = e.target.result;
            container.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        container.classList.add('hidden');
    }
}