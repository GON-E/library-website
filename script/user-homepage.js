// script/user-homepage.js - Borrow modal functionality

function calculateDueDate(days) {
  const dueDate = new Date();
  // Handle fractional days (e.g., 0.0007 for 1 minute)
  const milliseconds = parseFloat(days) * 24 * 60 * 60 * 1000;
  dueDate.setTime(dueDate.getTime() + milliseconds);
  return dueDate;
}

function updateDueDate() {
  const selectedDays = document.querySelector('input[name="duration"]:checked').value;
  const dueDate = calculateDueDate(selectedDays);
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  const dueDateFormatted = dueDate.toLocaleDateString('en-US', options);
  document.getElementById('modal-due-date').textContent = dueDateFormatted;
  document.getElementById('confirm-duration').value = selectedDays;
}

function openBorrowModal(bookData) {
  // Calculate dates (default 7 days)
  const today = new Date();
  const dueDate = calculateDueDate(7);

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
  document.getElementById('confirm-duration').value = '7';
  
  // Reset duration radio buttons to 7 days
  document.querySelector('input[name="duration"][value="7"]').checked = true;

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