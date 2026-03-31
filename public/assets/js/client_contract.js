document.addEventListener('DOMContentLoaded', function(){
  // future interactivity: filter servicios, export pagos, etc.
  // small UX: clickable status shows details
  document.querySelectorAll('.payments-table .status').forEach(function(el){
    el.style.cursor = 'default';
  });

  // Upload form toggle and validation
  const btnUpload = document.getElementById('btnUpload');
  const uploadContainer = document.getElementById('uploadFormContainer');
  const cancelUpload = document.getElementById('cancelUpload');
  const uploadForm = document.getElementById('uploadForm');
  const fileInput = document.getElementById('fileInput');
  const uploadMsg = document.getElementById('uploadMsg');

  if (btnUpload && uploadContainer) {
    btnUpload.addEventListener('click', function(){
      uploadContainer.style.display = uploadContainer.style.display === 'none' ? 'block' : 'none';
      window.scrollTo({top: uploadContainer.offsetTop - 80, behavior: 'smooth'});
    });
  }
  if (cancelUpload && uploadContainer) {
    cancelUpload.addEventListener('click', function(){
      uploadContainer.style.display = 'none';
    });
  }

  if (uploadForm) {
    uploadForm.addEventListener('submit', function(e){
      const f = fileInput.files[0];
      if (!f) { e.preventDefault(); uploadMsg.textContent = 'Seleccione un archivo.'; return; }
      const allowed = ['application/pdf','image/jpeg','image/png'];
      if (!allowed.includes(f.type)) { e.preventDefault(); uploadMsg.textContent = 'Tipo de archivo no permitido.'; return; }
      const max = 5 * 1024 * 1024;
      if (f.size > max) { e.preventDefault(); uploadMsg.textContent = 'Archivo demasiado grande. Máx 5MB.'; return; }
      // allow submit
    });
  }
});
