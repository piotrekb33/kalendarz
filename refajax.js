
// AJAX: Edycja zdarzenia
function saveEditAjax(form, eid) {
    var formData = new FormData(form);
    var statusSpan = document.getElementById('edit-status-' + eid);
    statusSpan.textContent = 'Zapisywanie...';
    fetch('edit_event.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.text())
    .then(msg => {
        statusSpan.textContent = msg;
        if (msg.includes('✅')) {
            setTimeout(() => {
                statusSpan.textContent = '';
                // Usuń ?edit=... z URL i przeładuj
                const url = new URL(window.location.href);
                url.searchParams.delete('edit');
                window.location.href = url.toString();
            }, 3000);
        }
    })
    .catch(() => {
        statusSpan.textContent = '❌ Błąd sieci';
    });
    return false;
}

// AJAX: Dodawanie zdarzenia
function addEventAjax(form) {
    var formData = new FormData(form);
    var statusSpan = document.getElementById('add-status');
    statusSpan.textContent = 'Dodawanie...';
    fetch('calendar.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.text())
    .then(msg => {
        // Spróbuj wyciągnąć komunikat z odpowiedzi (jeśli jest)
        var m = msg.match(/✅|❌.*/);
        statusSpan.textContent = m ? m[0] : '✅ Dodano';
        if (msg.includes('✅')) {
            setTimeout(() => {
                statusSpan.textContent = '';
                // Usuń ?add=... z URL i przeładuj
                const url = new URL(window.location.href);
                url.searchParams.delete('add');
                window.location.href = url.toString();
            }, 3000);
        }
    })
    .catch(() => {
        statusSpan.textContent = '❌ Błąd sieci';
    });
    return false;
}
