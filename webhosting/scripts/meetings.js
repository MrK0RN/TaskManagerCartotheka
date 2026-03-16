// Страница встреч: загрузка списка, добавление встречи

(function () {
    const API = 'api/meetings.php';

    const formEl = document.getElementById('meetingForm');
    const portraitIdEl = document.getElementById('meeting_portrait_id');
    const editIdEl = document.getElementById('meeting_edit_id');
    const dateEl = document.getElementById('meeting_date');
    const withWhomEl = document.getElementById('meeting_with_whom');
    const descriptionEl = document.getElementById('meeting_description');
    const submitBtn = document.getElementById('meetingSubmitBtn');
    const cancelEditBtn = document.getElementById('meetingCancelEditBtn');
    const loadingEl = document.getElementById('meetings-loading');
    const emptyEl = document.getElementById('meetings-empty');
    const listEl = document.getElementById('meetings-list');

    var meetingsList = [];

    function getPortraitId() {
        return portraitIdEl ? parseInt(portraitIdEl.value, 10) : 0;
    }

    function formatDate(iso) {
        if (!iso) return '—';
        var d = new Date(iso);
        if (isNaN(d.getTime())) return iso;
        var y = d.getFullYear();
        var m = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return day + '.' + m + '.' + y;
    }

    function escapeHtml(s) {
        if (s == null) return '';
        var div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    function renderMeeting(meeting) {
        var dateStr = formatDate(meeting.meeting_date);
        var withWhom = escapeHtml(meeting.with_whom || '—');
        var desc = escapeHtml(meeting.description || '');
        return (
            '<li class="meeting-item" data-id="' + meeting.id + '">' +
            '<div class="meeting-item-main">' +
            '<div class="meeting-item-date">' + dateStr + '</div>' +
            '<div class="meeting-item-with">С кем: ' + withWhom + '</div>' +
            (desc ? '<div class="meeting-item-desc">' + desc + '</div>' : '') +
            '</div>' +
            '<div class="meeting-item-actions">' +
            '<button type="button" class="btn btn-outline btn-small meeting-edit-btn" data-id="' + meeting.id + '">Редактировать</button>' +
            '</div>' +
            '</li>'
        );
    }

    function setLoading(show) {
        if (loadingEl) loadingEl.style.display = show ? 'block' : 'none';
        if (emptyEl) emptyEl.style.display = 'none';
        if (listEl) listEl.style.display = show ? 'none' : 'none';
    }

    function setList(meetings) {
        if (loadingEl) loadingEl.style.display = 'none';
        if (!listEl) return;
        meetingsList = meetings || [];
        if (!meetings || meetings.length === 0) {
            listEl.style.display = 'none';
            listEl.innerHTML = '';
            if (emptyEl) emptyEl.style.display = 'block';
            return;
        }
        if (emptyEl) emptyEl.style.display = 'none';
        listEl.style.display = 'block';
        listEl.innerHTML = meetings.map(renderMeeting).join('');
        listEl.querySelectorAll('.meeting-edit-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = parseInt(btn.getAttribute('data-id'), 10);
                var meeting = meetingsList.find(function (m) { return m.id === id; });
                if (meeting) startEdit(meeting);
            });
        });
    }

    function startEdit(meeting) {
        if (dateEl) dateEl.value = meeting.meeting_date || '';
        if (withWhomEl) withWhomEl.value = meeting.with_whom || '';
        if (descriptionEl) descriptionEl.value = meeting.description || '';
        if (editIdEl) editIdEl.value = String(meeting.id);
        if (submitBtn) submitBtn.textContent = 'Сохранить';
        if (cancelEditBtn) cancelEditBtn.style.display = 'inline-block';
    }

    function cancelEdit() {
        if (dateEl) dateEl.value = '';
        if (withWhomEl) withWhomEl.value = '';
        if (descriptionEl) descriptionEl.value = '';
        if (editIdEl) editIdEl.value = '';
        if (submitBtn) submitBtn.textContent = 'Добавить встречу';
        if (cancelEditBtn) cancelEditBtn.style.display = 'none';
    }

    function setError(message) {
        if (loadingEl) loadingEl.style.display = 'none';
        if (emptyEl) {
            emptyEl.textContent = message || 'Ошибка загрузки';
            emptyEl.style.display = 'block';
        }
        if (listEl) listEl.style.display = 'none';
    }

    function loadMeetings() {
        var portraitId = getPortraitId();
        if (!portraitId) {
            setList([]);
            return;
        }
        setLoading(true);
        fetch(API + '?portrait_id=' + encodeURIComponent(portraitId))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    setList(data.meetings || []);
                } else {
                    setError(data.message || 'Ошибка загрузки');
                }
            })
            .catch(function (err) {
                setError('Ошибка сети: ' + (err.message || 'неизвестная ошибка'));
            });
    }

    function resetForm() {
        cancelEdit();
    }

    function submitForm(e) {
        e.preventDefault();
        var portraitId = getPortraitId();
        if (!portraitId) return;
        var meetingDate = dateEl ? dateEl.value.trim() : '';
        if (!meetingDate) return;
        var withWhom = withWhomEl ? withWhomEl.value.trim() : '';
        var description = descriptionEl ? descriptionEl.value.trim() : '';
        var editId = editIdEl && editIdEl.value ? editIdEl.value.trim() : '';
        var isEdit = editId !== '';

        var btn = formEl.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;

        var payload = {
            portrait_id: portraitId,
            meeting_date: meetingDate,
            with_whom: withWhom,
            description: description
        };
        if (isEdit) payload.id = parseInt(editId, 10);

        fetch(API, {
            method: isEdit ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (btn) btn.disabled = false;
                if (data.success) {
                    resetForm();
                    loadMeetings();
                } else {
                    alert(data.message || (isEdit ? 'Не удалось сохранить встречу' : 'Не удалось добавить встречу'));
                }
            })
            .catch(function (err) {
                if (btn) btn.disabled = false;
                alert('Ошибка: ' + (err.message || 'неизвестная ошибка'));
            });
    }

    function init() {
        loadMeetings();
        if (formEl) formEl.addEventListener('submit', submitForm);
        if (cancelEditBtn) cancelEditBtn.addEventListener('click', cancelEdit);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
