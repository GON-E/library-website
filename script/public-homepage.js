// script/public-homepage.js - Modal functionality for guest users

function showLoginModal() {
  document.getElementById('loginModal').style.display = 'flex';
}

function closeLoginModal() {
  document.getElementById('loginModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
  const loginModal = document.getElementById('loginModal');
  if (event.target === loginModal) {
    closeLoginModal();
  }
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeLoginModal();
  }
});