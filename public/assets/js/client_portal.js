document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('btnToggleUpload');
    const panel = document.getElementById('uploadPanel');
    const btnCancel = document.getElementById('btnCancelUpload');
    const btnContact = document.getElementById('btnContact');

    if(btn && panel){
        btn.addEventListener('click', ()=>{ panel.style.display = panel.style.display === 'none' ? 'block' : 'none'; panel.scrollIntoView({behavior:'smooth',block:'center'}); });
    }
    if(btnCancel && panel){
        btnCancel.addEventListener('click', ()=>{ panel.style.display = 'none'; });
    }
    if(btnContact){
        btnContact.addEventListener('click', ()=>{ window.location.href = '/aventuras/client/support'; });
    }

    // microinteraction: progress bars animate if any
    document.querySelectorAll('.progress-inner').forEach(el=>{ const w = el.style.width || el.getAttribute('data-width'); if(w){ setTimeout(()=> el.style.width = w,100); } });
});
