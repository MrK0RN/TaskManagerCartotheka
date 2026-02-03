// Список портретов: загрузка, поиск, отображение

(function () {
    const LIST_API = 'api/list.php';
    const DEBOUNCE_MS = 300;

    const searchEl = document.getElementById('list-search');
    const statusEl = document.getElementById('list-status');
    const dateFromEl = document.getElementById('list-date-from');
    const dateToEl = document.getElementById('list-date-to');
    const loadingEl = document.getElementById('list-loading');
    const errorEl = document.getElementById('list-error');
    const tableWrapEl = document.getElementById('list-table-wrap');
    const tbodyEl = document.getElementById('list-tbody');
    const emptyEl = document.getElementById('list-empty');

    let debounceTimer = null;

    function getQueryParams() {
        const params = new URLSearchParams();
        const search = searchEl && searchEl.value ? searchEl.value.trim() : '';
        const status = statusEl && statusEl.value ? statusEl.value : '';
        const dateFrom = dateFromEl && dateFromEl.value ? dateFromEl.value : '';
        const dateTo = dateToEl && dateToEl.value ? dateToEl.value : '';
        if (search) params.set('search', search);
        if (status) params.set('status', status);
        if (dateFrom) params.set('date_from', dateFrom);
        if (dateTo) params.set('date_to', dateTo);
        return params.toString();
    }

    function setLoading(show) {
        if (loadingEl) loadingEl.style.display = show ? 'block' : 'none';
        if (errorEl) errorEl.style.display = 'none';
        if (tableWrapEl) tableWrapEl.style.display = show ? 'none' : 'none';
        if (emptyEl) emptyEl.style.display = 'none';
    }

    function setError(message) {
        if (loadingEl) loadingEl.style.display = 'none';
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
        if (tableWrapEl) tableWrapEl.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'none';
    }

    function setResult(items) {
        if (loadingEl) loadingEl.style.display = 'none';
        if (errorEl) errorEl.style.display = 'none';
        if (!items.length) {
            if (tableWrapEl) tableWrapEl.style.display = 'none';
            if (emptyEl) emptyEl.style.display = 'block';
            return;
        }
        if (emptyEl) emptyEl.style.display = 'none';
        if (tableWrapEl) tableWrapEl.style.display = 'block';
        if (!tbodyEl) return;

        tbodyEl.innerHTML = items.map(function (item) {
            var created = item.created_at ? formatDate(item.created_at) : '—';
            var updated = item.updated_at ? formatDate(item.updated_at) : '—';
            var statusText = item.status === 'completed' ? 'Завершён' : 'Черновик';
            var fio = escapeHtml(item.fio || 'Без названия');
            return (
                '<tr>' +
                '<td class="list-cell-fio">' + fio + '</td>' +
                '<td>' + escapeHtml(created) + '</td>' +
                '<td>' + escapeHtml(updated) + '</td>' +
                '<td>' + escapeHtml(statusText) + '</td>' +
                '<td class="list-cell-action"><a href="index.php?id=' + item.id + '" class="btn btn-small">Редактировать</a></td>' +
                '</tr>'
            );
        }).join('');
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

    function loadList() {
        var qs = getQueryParams();
        var url = LIST_API + (qs ? '?' + qs : '');
        setLoading(true);

        fetch(url)
            .then(function (res) { return res.text(); })
            .then(function (text) {
                var data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    setError(text && text.length < 500 ? text : 'Ответ сервера не в формате JSON');
                    return;
                }
                if (data.success) {
                    setResult(data.items || []);
                } else {
                    setError(data.message || 'Ошибка загрузки списка');
                }
            })
            .catch(function (err) {
                setError('Ошибка сети: ' + (err.message || 'неизвестная ошибка'));
            });
    }

    function scheduleLoad() {
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(loadList, DEBOUNCE_MS);
    }

    function init() {
        if (searchEl) searchEl.addEventListener('input', scheduleLoad);
        if (searchEl) searchEl.addEventListener('change', scheduleLoad);
        if (statusEl) statusEl.addEventListener('change', loadList);
        if (dateFromEl) dateFromEl.addEventListener('change', loadList);
        if (dateToEl) dateToEl.addEventListener('change', loadList);
        loadList();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
