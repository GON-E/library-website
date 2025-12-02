// Auto-hide any visible .page-message elements after a short delay
(function(){
  function hideMessage(el, delay){
    if(!el) return;
    setTimeout(function(){
      el.classList.add('hide');
      setTimeout(function(){ if(el && el.parentNode) el.parentNode.removeChild(el); }, 400);
    }, delay || 3500);
  }

  // On DOM ready, find message elements and schedule hide
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', function(){
      document.querySelectorAll('.page-message').forEach(function(el){ hideMessage(el); });
    });
  } else {
    document.querySelectorAll('.page-message').forEach(function(el){ hideMessage(el); });
  }
})();
