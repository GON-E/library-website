// script/user-dashboard.js - Cancel borrow modal functionality

// ===== CANCEL BORROW MODAL =====
function openCancelModal(borrowData) {
  // Fill modal with book data
  document.getElementById('cancel-book-title').textContent = borrowData.title;
  document.getElementById('cancel-book-author').textContent = borrowData.author;
  document.getElementById('cancel-date-borrowed').textContent = borrowData.dateBorrowed;
  document.getElementById('cancel-borrow-id').value = borrowData.borrowId;

  // Show modal
  document.getElementById('cancelModal').style.display = 'flex';
}

function closeCancelModal() {
  document.getElementById('cancelModal').style.display = 'none';
}

// ===== CLOSE MODAL WHEN CLICKING OUTSIDE =====
window.onclick = function(event) {
  const cancelModal = document.getElementById('cancelModal');

  if (event.target === cancelModal) {
    closeCancelModal();
  }
}

// ===== CLOSE MODAL ON ESC KEY =====
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeCancelModal();
  }
});

// ===== AUTO-HIDE SUCCESS/ERROR MESSAGES =====
window.addEventListener('DOMContentLoaded', function() {
  const successMsg = document.querySelector('.success-message');
  const errorMsg = document.querySelector('.error-message');
  
  if (successMsg) {
    setTimeout(function() {
      successMsg.style.opacity = '0';
      setTimeout(function() {
        successMsg.style.display = 'none';
      }, 300);
    }, 5000);
  }
  
  if (errorMsg) {
    setTimeout(function() {
      errorMsg.style.opacity = '0';
      setTimeout(function() {
        errorMsg.style.display = 'none';
      }, 300);
    }, 5000);
  }
});

// ===== CONFIRM BEFORE CLOSING PAGE IF MODAL IS OPEN =====
window.addEventListener('beforeunload', function(e) {
  const modal = document.getElementById('cancelModal');
  if (modal && modal.style.display === 'flex') {
    e.preventDefault();
    e.returnValue = '';
  }
});