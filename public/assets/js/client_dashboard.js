document.addEventListener('DOMContentLoaded', function(){
  const openBtn = document.getElementById('open-itinerary-edit');
  const modal = document.getElementById('modal-itinerary');
  const cancel = document.getElementById('cancel-it');
  const form = document.getElementById('form-itinerary');
  const mobileBtn = document.getElementById('mobileMenuBtn');
  const mobileOverlay = document.getElementById('mobileNavOverlay');

  if (openBtn && modal) {
    openBtn.addEventListener('click', ()=> modal.style.display = 'flex');
    cancel.addEventListener('click', ()=> modal.style.display = 'none');
  }

  // Mobile menu toggle (safe guards)
  if (mobileBtn && mobileOverlay) {
    mobileBtn.addEventListener('click', ()=> {
      if (mobileOverlay.style.display === 'flex') mobileOverlay.style.display = 'none';
      else mobileOverlay.style.display = 'flex';
    });
    // close when clicking outside nav
    mobileOverlay.addEventListener('click', (e)=>{ if (e.target === mobileOverlay) mobileOverlay.style.display = 'none'; });
  }

  if (form) {
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      const data = new FormData(form);
      const payload = {
        grupo_id: data.get('grupo_id'),
        detalle_json: data.get('detalle_json'),
        csrf_token: data.get('csrf_token')
      };

      try {
        const res = await fetch('/leader/group/' + encodeURIComponent(payload.grupo_id) + '/itinerary/save', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify(payload)
        });
        const js = await res.json();
        if (js.success) {
          alert('Itinerario guardado. Refresca la página para ver los cambios.');
          modal.style.display = 'none';
        } else {
          alert('Error: ' + (js.message || 'no se pudo guardar'));
        }
      } catch (err) {
        alert('Error de red: ' + err.message);
      }
    });
  }
});
