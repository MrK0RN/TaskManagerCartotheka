(function() {
    'use strict';

    // Доступ к микрофону в браузере разрешён только по HTTPS (или localhost)
    function isSecureContext() {
        return typeof window !== 'undefined' && (window.isSecureContext === true || window.location.protocol === 'https:' || /^localhost$|^127\.\d+\.\d+\.\d+$/i.test(window.location.hostname));
    }

    function isMediaSupported() {
        return typeof navigator !== 'undefined' && navigator.mediaDevices && typeof navigator.mediaDevices.getUserMedia === 'function' && typeof MediaRecorder !== 'undefined';
    }

    // Yandex SpeechKit принимает только Ogg Opus — ставим его в приоритет
    function getMediaRecorderOpts() {
        var types = ['audio/ogg;codecs=opus', 'audio/ogg', 'audio/webm;codecs=opus', 'audio/webm'];
        for (var i = 0; i < types.length; i++) {
            if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported(types[i])) {
                return { mimeType: types[i], audioBitsPerSecond: 128000 };
            }
        }
        return { audioBitsPerSecond: 128000 };
    }
    var mediaRecorder = null;
    var chunks = [];

    function micIconSvg() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>';
    }

    function micStopSvg() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>';
    }

    function wrapTextarea(textarea) {
        if (textarea.closest('.textarea-voice-wrapper')) return;
        var wrapper = document.createElement('div');
        wrapper.className = 'textarea-voice-wrapper';
        textarea.parentNode.insertBefore(wrapper, textarea);
        wrapper.appendChild(textarea);
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-voice';
        btn.title = 'Записать голосом (расшифровка в текст)';
        btn.innerHTML = micIconSvg();
        btn.setAttribute('aria-label', 'Записать голосом');
        wrapper.appendChild(btn);

        btn.addEventListener('click', function() {
            if (btn.classList.contains('btn-voice-recording')) {
                stopRecording(btn, textarea);
            } else {
                startRecording(btn, textarea);
            }
        });
    }

    function startRecording(btn, textarea) {
        chunks = [];
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(function(stream) {
                try {
                    mediaRecorder = new MediaRecorder(stream, getMediaRecorderOpts());
                } catch (e) {
                    mediaRecorder = new MediaRecorder(stream);
                }
                mediaRecorder.ondataavailable = function(e) {
                    if (e.data && e.data.size) chunks.push(e.data);
                };
                mediaRecorder.onstop = function() {
                    stream.getTracks().forEach(function(t) { t.stop(); });
                    var mime = (mediaRecorder.mimeType || 'audio/ogg').split(';')[0];
                    var ext = mime === 'audio/ogg' ? 'ogg' : 'webm';
                    var blob = new Blob(chunks, { type: mime });
                    if (blob.size === 0) {
                        alert('Запись пуста. Разрешите доступ к микрофону и попробуйте снова.');
                        btn.disabled = false;
                        return;
                    }
                    sendToTranscribe(blob, btn, textarea, ext);
                };
                mediaRecorder.onerror = function(e) {
                    alert('Ошибка записи: ' + (e.error ? e.error.message : 'неизвестная'));
                    btn.classList.remove('btn-voice-recording');
                    btn.innerHTML = micIconSvg();
                    btn.disabled = false;
                };
                mediaRecorder.start(500);
                btn.classList.add('btn-voice-recording');
                btn.innerHTML = micStopSvg();
            })
            .catch(function(err) {
                var msg = err.message || String(err);
                if (err.name === 'NotAllowedError' || msg.indexOf('Permission') !== -1) {
                    msg = 'Доступ к микрофону запрещён. Разрешите доступ в настройках браузера для этого сайта.';
                } else if (err.name === 'NotFoundError') {
                    msg = 'Микрофон не найден. Подключите устройство и обновите страницу.';
                } else if (!isSecureContext()) {
                    msg = 'Микрофон доступен только по HTTPS или с localhost. Сейчас сайт открыт по HTTP.';
                } else {
                    msg = 'Не удалось получить доступ к микрофону: ' + msg;
                }
                alert(msg);
            });
    }

    function stopRecording(btn, textarea) {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        btn.classList.remove('btn-voice-recording');
        btn.innerHTML = micIconSvg();
        btn.disabled = true;
    }

    function sendToTranscribe(blob, btn, textarea, ext) {
        ext = ext || 'ogg';
        var formData = new FormData();
        formData.append('audio', blob, 'recording.' + ext);
        fetch('api/transcribe.php', {
            method: 'POST',
            body: formData
        })
            .then(function(res) {
                return res.json().then(function(data) {
                    if (!res.ok) throw new Error(data.error || 'Ошибка транскрибации');
                    return data;
                });
            })
            .then(function(data) {
                var existing = (textarea.value || '').trim();
                var toAppend = (data.text || '').trim();
                if (toAppend) {
                    textarea.value = existing ? existing + ' ' + toAppend : toAppend;
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                }
            })
            .catch(function(err) {
                alert(err.message || 'Ошибка при расшифровке');
            })
            .finally(function() {
                btn.disabled = false;
            });
    }

    function initVoiceTextareas() {
        var form = document.getElementById('personalityForm');
        if (!form) return;
        form.querySelectorAll('textarea').forEach(wrapTextarea);
    }

    function onDomReady() {
        initVoiceTextareas();
        var form = document.getElementById('personalityForm');
        if (form && typeof MutationObserver !== 'undefined') {
            var observer = new MutationObserver(function() {
                initVoiceTextareas();
            });
            observer.observe(form, { childList: true, subtree: true });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onDomReady);
    } else {
        onDomReady();
    }
})();
