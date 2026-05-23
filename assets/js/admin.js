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

// Add Products
// Preview multiple images before upload
function previewImages(input) {
    var container = document.getElementById('preview-container');
    container.innerHTML = '';

    if (input.files && input.files.length > 0) {
        container.classList.remove('hidden');

        for (var i = 0; i < input.files.length; i++) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full h-20 rounded object-cover';
                img.style.border = '1px solid #E0E2E7';
                container.appendChild(img);
            };
            reader.readAsDataURL(input.files[i]);
        }
    } else {
        container.classList.add('hidden');
    }
}
document.addEventListener('DOMContentLoaded', function () {

    const adjustableCheckbox = document.getElementById('strap_adjustable');
    const strapLengthInput = document.getElementById('strap_length_mm');
    const strapSizeInput = document.getElementById('strap_size_options');

    function toggleStrapFields() {

        if (adjustableCheckbox.checked) {
            strapSizeInput.disabled = false;
            strapLengthInput.disabled = true;

            strapLengthInput.value = '';

            strapLengthInput.style.backgroundColor = '#F3F4F6';

        } else {
            strapLengthInput.disabled = false;
            strapSizeInput.disabled = true;

            strapSizeInput.value = '';

            strapSizeInput.style.backgroundColor = '#F3F4F6';
        }
    }

    toggleStrapFields();

    adjustableCheckbox.addEventListener('change', toggleStrapFields);
});