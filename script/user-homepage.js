// script/user-homepage.js - Borrow modal functionality

function openBorrowModal(bookData) {
  // Calculate dates
  const today = new Date();
  const dueDate = new Date();
  dueDate.setDate(today.getDate() + 7); // 7 days from today

  // Format dates
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  const todayFormatted = today.toLocaleDateString('en-US', options);
  const dueDateFormatted = dueDate.toLocaleDateString('en-US', options);

  // Fill modal with book data
  document.getElementById('modal-book-title').textContent = bookData.title;
  document.getElementById('modal-author').textContent = bookData.author;
  document.getElementById('modal-category').textContent = bookData.category;
  document.getElementById('modal-isbn').textContent = bookData.isbn;
  document.getElementById('modal-date-borrowed').textContent = todayFormatted;
  document.getElementById('modal-due-date').textContent = dueDateFormatted;
  document.getElementById('confirm-book-isbn').value = bookData.isbn;

  // Show modal
  document.getElementById('borrowModal').style.display = 'flex';
}

function closeBorrowModal() {
  document.getElementById('borrowModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
  const borrowModal = document.getElementById('borrowModal');

  if (event.target === borrowModal) {
    closeBorrowModal();
  }
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeBorrowModal();
  }
});

// Auto-hide success/error messages after 5 seconds
window.addEventListener('DOMContentLoaded', function() {
  const successMsg = document.querySelector('.success-message');
  const errorMsg = document.querySelector('.error-message');
  
  if (successMsg) {
    setTimeout(function() {
      successMsg.style.display = 'none';
    }, 5000);
  }
  
  if (errorMsg) {
    setTimeout(function() {
      errorMsg.style.display = 'none';
    }, 5000);
  }
});