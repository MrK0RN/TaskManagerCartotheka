// Страница задач в стиле Todoist: плоский список с отступами, быстрый ввод подзадач

(function () {
    const API = 'api/tasks.php';
    const INDENT_PX = 24;

    const addRootBtn = document.getElementById('taskAddRootBtn');
    const loadingEl = document.getElementById('tasks-loading');
    const emptyEl = document.getElementById('tasks-empty');
    const treeEl = document.getElementById('tasks-tree');
    const modalEl = document.getElementById('taskModal');
    const modalTitle = document.getElementById('taskModalTitle');
    const formEl = document.getElementById('taskForm');
    const taskIdEl = document.getElementById('task_id');
    const taskParentIdEl = document.getElementById('task_parent_id');
    const taskTitleEl = document.getElementById('task_title');
    const taskDueDateEl = document.getElementById('task_due_date');
    const taskAssigneeEl = document.getElementById('task_assignee');
    const taskPortraitIdEl = document.getElementById('task_portrait_id');
    const submitBtn = document.getElementById('taskSubmitBtn');
    const cancelBtn = document.getElementById('taskModalCancel');
    const backdrop = modalEl ? modalEl.querySelector('.task-modal-backdrop') : null;

    var assigneeAutocomplete = null;
    var currentFlat = [];

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

    function flattenWithDepth(list, depth, rootTask, out) {
        out = out || [];
        (list || []).forEach(function (t) {
            var root = rootTask || t;
            out.push({ task: t, depth: depth, rootTask: root });
            flattenWithDepth(t.children, depth + 1, root, out);
        });
        return out;
    }

    function flattenTasks(list) {
        return flattenWithDepth(list, 0, null, []).map(function (x) { return x.task; });
    }

    function findTaskById(tree, id) {
        var flat = flattenWithDepth(tree, 0, null, []);
        for (var i = 0; i < flat.length; i++) {
            if (flat[i].task.id === id) return flat[i].task;
        }
        return null;
    }

    function renderFlatList(itemsWithDepth) {
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        var tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        function sectionKey(task) {
            var d = task && task.due_date ? new Date(task.due_date) : null;
            if (!d) return '3_other';
            d.setHours(0, 0, 0, 0);
            if (d.getTime() < today.getTime()) return '0_overdue';
            if (d.getTime() === today.getTime()) return '1_today';
            if (d.getTime() === tomorrow.getTime()) return '2_tomorrow';
            return '3_other';
        }

        function sectionTitle(key) {
            if (key === '0_overdue') return 'Просрочено';
            if (key === '1_today') return 'Сегодня';
            if (key === '2_tomorrow') return 'Завтра';
            if (key === '3_other') return 'Без срока и позже';
            return '';
        }

        var bySection = {};
        itemsWithDepth.forEach(function (x) {
            var root = x.rootTask || x.task;
            var key = sectionKey(root);
            if (!bySection[key]) bySection[key] = [];
            bySection[key].push(x);
        });
        var order = ['0_overdue', '1_today', '2_tomorrow', '3_other'];
        var html = '';
        order.forEach(function (key) {
            var list = bySection[key];
            if (!list || list.length === 0) return;
            html += '<section class="task-section"><h2 class="task-section-title">' + escapeHtml(sectionTitle(key)) + '</h2><ul class="task-list">';
            list.forEach(function (x) {
                var t = x.task;
                var depth = x.depth;
                var title = escapeHtml(t.title || '');
                var dateStr = formatDate(t.due_date);
                var assignee = t.assignee_fio ? escapeHtml(t.assignee_fio) : '';
                var indent = depth * INDENT_PX;
                html += '<li class="task-list-item" data-id="' + t.id + '" data-depth="' + depth + '" style="padding-left:' + indent + 'px">' +
                    '<div class="task-tree-item-inner">' +
                    '<span class="task-tree-title">' + title + '</span>' +
                    '<span class="task-tree-item-date">' + dateStr + '</span>' +
                    (assignee ? '<span class="task-tree-item-assignee">' + assignee + '</span>' : '') +
                    '<div class="task-tree-item-actions">' +
                    '<button type="button" class="btn btn-outline btn-small task-add-sub-btn" data-id="' + t.id + '" title="Добавить подзадачу">+</button>' +
                    '<button type="button" class="btn btn-outline btn-small task-edit-btn" data-id="' + t.id + '">Изменить</button>' +
                    '<button type="button" class="btn btn-outline btn-small task-delete-btn" data-id="' + t.id + '">Удалить</button>' +
                    '</div></div></li>';
            });
            html += '</ul></section>';
        });
        return html;
    }

    function setTree(html) {
        if (loadingEl) loadingEl.style.display = 'none';
        if (!treeEl) return;
        treeEl.innerHTML = html;
        treeEl.style.display = html ? 'block' : 'none';
        if (emptyEl) emptyEl.style.display = html ? 'none' : 'block';

        treeEl.querySelectorAll('.task-add-sub-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var id = parseInt(btn.getAttribute('data-id'), 10);
                openInlineSubtask(id, btn.closest('.task-list-item'));
            });
        });
        treeEl.querySelectorAll('.task-edit-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = parseInt(btn.getAttribute('data-id'), 10);
                var task = findTaskById(currentTree, id);
                if (task) openModal(task, null);
            });
        });
        treeEl.querySelectorAll('.task-delete-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = parseInt(btn.getAttribute('data-id'), 10);
                if (confirm('Удалить задачу и все подзадачи?')) deleteTask(id);
            });
        });
    }

    function openInlineSubtask(parentId, afterLi) {
        closeInlineSubtask();
        var depth = 0;
        if (afterLi) {
            var d = afterLi.getAttribute('data-depth');
            if (d !== null && d !== '') depth = parseInt(d, 10) + 1;
        }
        var wrap = document.createElement('li');
        wrap.className = 'task-inline-add';
        wrap.setAttribute('data-parent-id', String(parentId));
        wrap.style.paddingLeft = (depth * INDENT_PX) + 'px';
        var inner = document.createElement('div');
        inner.className = 'task-inline-add-inner';
        var input = document.createElement('input');
        input.type = 'text';
        input.placeholder = 'Название подзадачи…';
        input.className = 'task-inline-add-input';
        var addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.className = 'btn btn-primary btn-small';
        addBtn.textContent = 'Добавить';
        var cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'btn btn-outline btn-small';
        cancelBtn.textContent = 'Отмена';
        inner.appendChild(input);
        inner.appendChild(addBtn);
        inner.appendChild(cancelBtn);
        wrap.appendChild(inner);
        var list = afterLi ? afterLi.closest('ul') : treeEl.querySelector('.task-list');
        if (list) {
            if (afterLi && afterLi.nextSibling) {
                list.insertBefore(wrap, afterLi.nextSibling);
            } else {
                list.appendChild(wrap);
            }
            input.focus();
        }
        function submit() {
            var title = (input.value || '').trim();
            if (!title) return;
            addBtn.disabled = true;
            fetch(API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ parent_id: parentId, title: title })
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        closeInlineSubtask();
                        // Оптимистично добавляем подзадачу в дерево из ответа и перерисовываем
                        if (data.task && data.task.id && parentId) {
                            var parentTask = findTaskById(currentTree, parentId);
                            if (parentTask) {
                                var newTask = {
                                    id: data.task.id,
                                    parent_id: data.task.parent_id,
                                    title: data.task.title || title,
                                    due_date: data.task.due_date || null,
                                    portrait_id: data.task.portrait_id || null,
                                    assignee_fio: data.task.assignee_fio || null,
                                    children: []
                                };
                                if (!parentTask.children) parentTask.children = [];
                                parentTask.children.push(newTask);
                                currentFlat = flattenWithDepth(currentTree, 0, null, []);
                                var html = renderFlatList(currentFlat);
                                setTree(html);
                                loadTasks(true);
                            } else {
                                loadTasks();
                            }
                        } else {
                            loadTasks();
                        }
                    } else {
                        addBtn.disabled = false;
                        alert(data.message || 'Ошибка');
                    }
                })
                .catch(function (err) {
                    addBtn.disabled = false;
                    alert('Ошибка: ' + (err.message || ''));
                });
        }
        addBtn.addEventListener('click', submit);
        cancelBtn.addEventListener('click', closeInlineSubtask);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); submit(); }
            if (e.key === 'Escape') closeInlineSubtask();
        });
        wrap._inlineInput = input;
    }

    function closeInlineSubtask() {
        var el = treeEl.querySelector('.task-inline-add');
        if (el && el.parentNode) el.parentNode.removeChild(el);
    }

    var currentTree = [];

    function loadTasks(silent) {
        closeInlineSubtask();
        if (!silent) {
            if (loadingEl) loadingEl.style.display = 'block';
            if (emptyEl) emptyEl.style.display = 'none';
            if (treeEl) treeEl.style.display = 'none';
        }

        fetch(API + (API.indexOf('?') >= 0 ? '&' : '?') + '_=' + Date.now())
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    currentTree = data.tasks || [];
                    var flatCount = currentTree.length ? flattenWithDepth(currentTree, 0, null, []).length : 0;
                    if (typeof console !== 'undefined' && console.log) {
                        console.log('Tasks loaded: roots=' + currentTree.length + ', flat items=' + flatCount);
                    }
                    if (currentTree.length === 0) {
                        setTree('');
                        return;
                    }
                    currentFlat = flattenWithDepth(currentTree, 0, null, []);
                    var html = renderFlatList(currentFlat);
                    setTree(html);
                } else {
                    if (loadingEl) loadingEl.style.display = 'none';
                    if (emptyEl) {
                        emptyEl.textContent = data.message || 'Ошибка загрузки';
                        emptyEl.style.display = 'block';
                    }
                }
            })
            .catch(function (err) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (emptyEl) {
                    emptyEl.textContent = 'Ошибка сети: ' + (err.message || 'неизвестная ошибка');
                    emptyEl.style.display = 'block';
                }
            });
    }

    function openModal(task, parentId) {
        if (task) {
            modalTitle.textContent = 'Редактировать задачу';
            taskIdEl.value = String(task.id);
            taskParentIdEl.value = '';
            taskTitleEl.value = task.title || '';
            taskDueDateEl.value = task.due_date || '';
            taskAssigneeEl.value = task.assignee_fio || '';
            taskPortraitIdEl.value = task.portrait_id ? String(task.portrait_id) : '';
        } else {
            modalTitle.textContent = parentId ? 'Добавить подзадачу' : 'Добавить задачу';
            taskIdEl.value = '';
            taskParentIdEl.value = parentId ? String(parentId) : '';
            taskTitleEl.value = '';
            taskDueDateEl.value = '';
            taskAssigneeEl.value = '';
            taskPortraitIdEl.value = '';
        }
        if (modalEl) modalEl.style.display = 'flex';
    }

    function closeModal() {
        if (modalEl) modalEl.style.display = 'none';
    }

    function submitForm(e) {
        e.preventDefault();
        var id = taskIdEl.value ? taskIdEl.value.trim() : '';
        var parentId = taskParentIdEl.value ? taskParentIdEl.value.trim() : '';
        var title = taskTitleEl.value ? taskTitleEl.value.trim() : '';
        var dueDate = taskDueDateEl.value ? taskDueDateEl.value.trim() : null;
        var portraitId = taskPortraitIdEl.value ? parseInt(taskPortraitIdEl.value, 10) : null;
        if (!title) {
            alert('Введите название задачи');
            return;
        }

        if (submitBtn) submitBtn.disabled = true;

        var isEdit = id !== '';
        var url = API;
        var method = isEdit ? 'PUT' : 'POST';
        var payload = {
            title: title,
            due_date: dueDate || null,
            portrait_id: portraitId || null
        };
        if (isEdit) {
            payload.id = parseInt(id, 10);
        } else {
            if (parentId) payload.parent_id = parseInt(parentId, 10);
        }

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (submitBtn) submitBtn.disabled = false;
                if (data.success) {
                    closeModal();
                    loadTasks();
                } else {
                    alert(data.message || 'Ошибка сохранения');
                }
            })
            .catch(function (err) {
                if (submitBtn) submitBtn.disabled = false;
                alert('Ошибка: ' + (err.message || 'неизвестная ошибка'));
            });
    }

    function deleteTask(id) {
        fetch(API + '?id=' + encodeURIComponent(id), { method: 'DELETE' })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    loadTasks();
                } else {
                    alert(data.message || 'Не удалось удалить');
                }
            })
            .catch(function (err) {
                alert('Ошибка: ' + (err.message || 'неизвестная ошибка'));
            });
    }

    function init() {
        loadTasks();

        if (addRootBtn) {
            addRootBtn.addEventListener('click', function () {
                openModal(null, null);
            });
        }
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        if (backdrop) backdrop.addEventListener('click', closeModal);
        if (formEl) formEl.addEventListener('submit', submitForm);

        if (taskAssigneeEl && typeof Autocomplete !== 'undefined') {
            assigneeAutocomplete = new Autocomplete(taskAssigneeEl, {
                type: 'portrait',
                minLength: 1,
                delay: 250,
                onSelect: function (item) {
                    if (taskPortraitIdEl) taskPortraitIdEl.value = item.id ? String(item.id) : '';
                }
            });
            taskAssigneeEl.addEventListener('input', function () {
                if (!taskAssigneeEl.value.trim() && taskPortraitIdEl) {
                    taskPortraitIdEl.value = '';
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
