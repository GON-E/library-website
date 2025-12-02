    
function openBorrowModal(bookData) {
  // Check login status first
  if (!isLoggedIn) {
    document.getElementById('loginModal').style.display = 'flex';
    return;
  }

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

function closeLoginModal() {
  document.getElementById('loginModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
  const borrowModal = document.getElementById('borrowModal');
  const loginModal = document.getElementById('loginModal');

  if (event.target === borrowModal) {
    closeBorrowModal();
  }
  if (event.target === loginModal) {
    closeLoginModal();
  }
}