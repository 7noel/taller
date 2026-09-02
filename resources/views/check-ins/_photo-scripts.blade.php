@push('scripts')
<script>
(function () {
    'use strict';

    // =============================================================
    // Fotos del inventario: captura móvil, compresión y auto-subida
    // =============================================================

    var IS_EDIT = {{ $isEdit ? 'true' : 'false' }};
    var CHECK_IN_ID = {{ $checkIn->id ?? 'null' }};
    var MAX_SIDE = 1600;         // lado mayor (px) al comprimir subidas de galería/PC
    var JPEG_QUALITY = 0.85;     // calidad JPEG para subidas de galería/PC
    var KEEP_SIZE = 450 * 1024;  // JPEG/WEBP menores a 450 KB se suben tal cual (sin recorte)
    var RATIOS = [
        { key: '3:4', w: 3, h: 4 },
        { key: '1:1', w: 1, h: 1 },
        { key: '4:3', w: 4, h: 3 },
        { key: '16:9', w: 16, h: 9 }
    ];
    var RATIO_DEFAULT = '3:4';
    var ZOOM_STEPS = [1, 1.5, 2, 3, 4];
    var QUALITY_PRESETS = {
        estandar: { key: 'estandar', label: 'Estándar', maxSide: 1600, quality: 0.85 },
        alta: { key: 'alta', label: 'Alta', maxSide: 2048, quality: 0.9 },
        maxima: { key: 'maxima', label: 'Máxima', maxSide: 2560, quality: 0.92 }
    };
    var QUALITY_DEFAULT = 'estandar';

    var previewEl = document.getElementById('photo-preview');
    if (!previewEl) return;

    var formEl = previewEl.closest('form');
    var captureBtn = document.getElementById('btn-photo-capture');
    var uploadBtn = document.getElementById('btn-photo-upload');
    var captureInput = document.getElementById('photo-capture-input');
    var galleryInput = document.getElementById('photo-input');
    var countEl = document.getElementById('photo-count');
    var statusEl = document.getElementById('photo-upload-status');
    var statusTextEl = document.getElementById('photo-upload-status-text');
    var errorEl = document.getElementById('photo-form-error');
    var cameraModal = document.getElementById('photo-camera-modal');
    var cameraVideo = document.getElementById('photo-camera-video');
    var cameraStatusEl = document.getElementById('photo-camera-status');
    var cameraCloseBtn = document.getElementById('btn-camera-close');
    var cameraShutterBtn = document.getElementById('btn-camera-shutter');

    var photos = [];            // fotos gestionadas por este script
    var busy = false;           // cola de subida activa
    var keySeq = 0;
    var createdCheckInId = null;
    var createdRedirect = null;
    var creating = false;       // protege el flujo de creación con fotos

    // -------------------------------------------------------------
    // Iconos SVG (mismo trazo que el sistema de diseño)
    // -------------------------------------------------------------
    var ICONS = {
        spinner: '<svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>',
        check: '<svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>',
        retry: '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>',
        warning: '<svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path></svg>',
        x: '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>'
    };

    var DELETE_BTN = '<button type="button" class="photo-delete absolute top-1 right-1 flex items-center justify-center bg-red-600 text-white rounded-full w-6 h-6 shadow hover:bg-red-700" title="Eliminar foto">' + ICONS.x + '</button>';

    // -------------------------------------------------------------
    // Utilidades base
    // -------------------------------------------------------------
    function getCsrf() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function singular(n) {
        return n === 1 ? 'foto' : 'fotos';
    }

    function targetId() {
        return IS_EDIT ? CHECK_IN_ID : createdCheckInId;
    }

    function buildPhotoItem(extra) {
        extra = extra || {};
        var div = document.createElement('div');
        div.className = 'photo-item relative group';
        if (extra.key) div.dataset.key = extra.key;
        if (extra.id) div.dataset.id = extra.id;
        div.innerHTML = '<img alt="Foto del vehículo" class="photo-img w-full h-32 object-cover rounded-lg border border-gray-200">' +
            '<span class="photo-badge absolute inset-0 z-10 hidden items-center justify-center rounded-lg bg-black/40"></span>' +
            DELETE_BTN;
        return div;
    }

    function findItem(el) {
        if (!el) return null;
        var key = el.dataset.key;
        for (var i = 0; i < photos.length; i++) {
            if (photos[i].key === key) return photos[i];
        }
        return null;
    }

    function setBadge(item, kind, message) {
        var badge = item.el.querySelector('.photo-badge');
        if (!badge) return;
        badge.style.background = '';
        badge.style.pointerEvents = 'none';
        if (kind === 'none' || kind === 'queued') {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
            badge.innerHTML = '';
            return;
        }
        badge.classList.remove('hidden');
        badge.classList.add('flex');
        if (kind === 'uploading' || kind === 'preparing') {
            badge.innerHTML = ICONS.spinner;
        } else if (kind === 'done') {
            badge.style.background = 'rgba(16,185,129,0.5)';
            badge.innerHTML = ICONS.check;
        } else if (kind === 'error') {
            badge.style.background = 'rgba(185,28,28,0.6)';
            badge.innerHTML = '<div class="flex flex-col items-center gap-1 px-2 text-center">' +
                '<span class="inline-flex items-center gap-1 text-white text-xs font-semibold">' + ICONS.warning + ' No se subió</span>' +
                '<button type="button" class="photo-retry pointer-events-auto inline-flex items-center gap-1 rounded-md bg-white px-2 py-1 text-xs font-semibold text-red-600 shadow hover:bg-red-50" title="Reintentar subida">' + ICONS.retry + ' Reintentar</button>' +
                (message ? '<span class="text-white text-xs leading-tight">' + message + '</span>' : '') +
                '</div>';
        }
    }

    function setImg(itemOrEl, url) {
        var el = itemOrEl.el || itemOrEl;
        var img = el.querySelector('img.photo-img');
        if (img) img.src = url;
    }

    function updateCount() {
        var total = previewEl.querySelectorAll('.photo-item').length;
        if (!countEl) return;
        countEl.classList.toggle('hidden', total === 0);
        countEl.textContent = total + ' ' + singular(total);
    }

    function updateStatus() {
        if (!statusEl || !statusTextEl) return;
        var idx = -1;
        for (var i = 0; i < photos.length; i++) {
            if (photos[i].state === 'uploading') { idx = i; break; }
        }
        if (idx === -1) {
            statusEl.classList.add('hidden');
            return;
        }
        statusEl.classList.remove('hidden');
        statusEl.classList.add('flex');
        statusTextEl.textContent = 'Subiendo foto ' + (idx + 1) + ' de ' + photos.length + '…';
    }

    function showError(msg) {
        if (!errorEl) return;
        errorEl.textContent = msg || '';
        errorEl.classList.toggle('hidden', !msg);
    }

    function submitButton() {
        return formEl ? formEl.querySelector('button[type="submit"]') : null;
    }

    function disableSubmit(label) {
        var btn = submitButton();
        if (!btn) return;
        if (!btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="inline-flex items-center gap-2">' + ICONS.spinner + '<span>' + (label || 'Guardando…') + '</span></span>';
        btn.disabled = true;
    }

    function restoreSubmit() {
        var btn = submitButton();
        if (btn && btn.dataset.originalHtml) {
            btn.innerHTML = btn.dataset.originalHtml;
            delete btn.dataset.originalHtml;
            btn.disabled = false;
        }
    }

    // -------------------------------------------------------------
    // Compresión en el cliente (canvas) + orientación EXIF
    // -------------------------------------------------------------
    function loadImageElement(url) {
        return new Promise(function (resolve, reject) {
            var img = new Image();
            img.onload = function () { resolve(img); };
            img.onerror = function () { reject(new Error('No se pudo leer la imagen')); };
            img.src = url;
        });
    }

    function compressPhoto(file, opts) {
        opts = opts || {};
        var maxSide = opts.maxSide || MAX_SIDE;
        var quality = opts.quality || JPEG_QUALITY;
        var cropRatio = opts.cropRatio || null; // p. ej. '3:4' (solo fotos de cámara)

        return new Promise(function (resolve) {
            if (!file || !/^image\//.test(file.type)) { resolve(file); return; }
            if (!cropRatio && (file.type === 'image/jpeg' || file.type === 'image/webp') && file.size < KEEP_SIZE) { resolve(file); return; }

            var bitmap = null;
            var width = 0;
            var height = 0;
            var painter = null;

            function encode() {
                // 1) Canvas natural (ya orientado por createImageBitmap o <img>)
                var natural = document.createElement('canvas');
                natural.width = width;
                natural.height = height;
                var nctx = natural.getContext('2d');
                nctx.fillStyle = '#ffffff';
                nctx.fillRect(0, 0, width, height);
                if (painter) painter(nctx);
                if (bitmap && typeof bitmap.close === 'function') { bitmap.close(); bitmap = null; }

                // 2) Recorte centrado a la proporción elegida (solo cámara)
                var srcX = 0, srcY = 0, srcW = width, srcH = height;
                if (cropRatio) {
                    var parts = String(cropRatio).split(':');
                    var rw = parseFloat(parts[0]) || 1;
                    var rh = parseFloat(parts[1]) || 1;
                    var aspect = rw / rh;
                    if (width / height > aspect) { srcH = height; srcW = height * aspect; }
                    else { srcW = width; srcH = width / aspect; }
                    srcX = (width - srcW) / 2;
                    srcY = (height - srcH) / 2;
                }

                // 3) Escala de salida (lado mayor <= maxSide)
                var scale = Math.min(1, maxSide / Math.max(srcW, srcH));
                var outW = Math.max(1, Math.round(srcW * scale));
                var outH = Math.max(1, Math.round(srcH * scale));

                var canvas = document.createElement('canvas');
                canvas.width = outW;
                canvas.height = outH;
                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, outW, outH);
                ctx.drawImage(natural, srcX, srcY, srcW, srcH, 0, 0, outW, outH);

                if (typeof canvas.toBlob !== 'function') { resolve(file); return; }
                canvas.toBlob(function (blob) {
                    if (!blob) { resolve(file); return; }
                    var base = String(file.name || 'foto').replace(/\.[^.]+$/, '') || 'foto';
                    resolve(new File([blob], base + '.jpg', { type: 'image/jpeg' }));
                }, 'image/jpeg', quality);
            }

            function useBitmap(bmp) {
                bitmap = bmp;
                width = bmp.width;
                height = bmp.height;
                painter = function (ctx) { ctx.drawImage(bitmap, 0, 0); };
                encode();
            }

            function useImage() {
                var url = URL.createObjectURL(file);
                loadImageElement(url).then(function (img) {
                    width = img.naturalWidth;
                    height = img.naturalHeight;
                    painter = function (ctx) { ctx.drawImage(img, 0, 0); };
                    encode();
                }).catch(function () {
                    resolve(file);
                }).finally(function () {
                    URL.revokeObjectURL(url);
                });
            }

            if ('createImageBitmap' in window) {
                createImageBitmap(file, { imageOrientation: 'from-image' }).then(useBitmap).catch(useImage);
            } else {
                useImage();
            }
        });
    }

    // -------------------------------------------------------------
    // Subida de una foto
    // -------------------------------------------------------------
    function uploadItem(item) {
        var id = targetId();
        if (!id) return Promise.reject(new Error('El inventario aún no existe. Guarda primero el inventario.'));
        var fd = new FormData();
        fd.append('photo', item.file);
        return fetch('/api/check-ins/' + id + '/photos', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
            body: fd
        }).then(function (res) {
            if (!res.ok) {
                return res.json().catch(function () { return {}; }).then(function (j) {
                    var msg = (j && j.message) || 'No se pudo subir la foto.';
                    if (j && j.errors) {
                        var keys = Object.keys(j.errors);
                        if (keys.length && j.errors[keys[0]] && j.errors[keys[0]].length) msg = j.errors[keys[0]][0];
                    }
                    throw new Error(msg);
                });
            }
            return res.json();
        });
    }

    function completeUpload(item, data) {
        item.id = data && data.id ? data.id : null;
        item.state = 'done';
        item.el.dataset.state = 'done';
        if (item.id) item.el.dataset.id = item.id;
        if (item.localUrl) { URL.revokeObjectURL(item.localUrl); item.localUrl = null; }
        setImg(item, (data && data.url) || '');
        setBadge(item, 'done');
        setTimeout(function () {
            if (item.el && item.el.isConnected) setBadge(item, 'none');
        }, 1300);
    }

    function failUpload(item, err) {
        item.state = 'error';
        item.error = (err && err.message) ? err.message : 'Error de red al subir la foto.';
        item.el.dataset.state = 'error';
        setBadge(item, 'error', item.error);
    }

    // -------------------------------------------------------------
    // Cola de subida (secuencial, conserva el orden de las fotos)
    // -------------------------------------------------------------
    async function pumpUploads() {
        if (busy) return;
        busy = true;
        try {
            for (;;) {
                var item = null;
                for (var i = 0; i < photos.length; i++) {
                    if (photos[i].state === 'pending') { item = photos[i]; break; }
                }
                if (!item) break;
                item.state = 'uploading';
                item.el.dataset.state = 'uploading';
                setBadge(item, 'uploading');
                updateStatus();
                try {
                    var data = await uploadItem(item);
                    completeUpload(item, data);
                } catch (err) {
                    failUpload(item, err);
                }
            }
        } finally {
            busy = false;
            updateStatus();
        }
    }

    // -------------------------------------------------------------
    // Flujo de creación: guardar inventario y luego subir las fotos
    // -------------------------------------------------------------
    function refreshCsrf() {
        return fetch('/api/csrf-token', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            cache: 'no-store'
        }).then(function (r) { return r.json(); }).then(function (d) {
            var token = (d && d.csrf_token) ? d.csrf_token : getCsrf();
            var meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', token);
            if (formEl) {
                formEl.querySelectorAll('input[name="_token"]').forEach(function (inp) { inp.value = token; });
            }
            return token;
        });
    }

    function countCreateErrors() {
        var n = 0;
        for (var i = 0; i < photos.length; i++) {
            if (photos[i].mode === 'create' && photos[i].state === 'error') n++;
        }
        return n;
    }

    function activateCreateQueue() {
        for (var i = 0; i < photos.length; i++) {
            var p = photos[i];
            if (p.mode === 'create' && p.state === 'queued') {
                p.state = 'pending';
                p.el.dataset.state = 'pending';
                setBadge(p, 'none');
            }
        }
    }

    function redirectAfterCreate() {
        window.location.href = createdRedirect || '/check-ins';
    }

    async function createAndUpload() {
        if (creating) return;
        creating = true;
        disableSubmit('Guardando inventario…');
        showError(null);
        try {
            var token = await refreshCsrf();
            var fd = new FormData(formEl);
            var res = await fetch(formEl.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
                credentials: 'same-origin'
            });
            if (!res.ok) {
                var jErr = null;
                try { jErr = await res.json(); } catch (e2) { jErr = null; }
                var errMsg = 'No se pudo guardar el inventario. Revisa los campos obligatorios y vuelve a intentar.';
                if (jErr && jErr.errors) {
                    var errKeys = Object.keys(jErr.errors);
                    if (errKeys.length && jErr.errors[errKeys[0]] && jErr.errors[errKeys[0]].length) errMsg = jErr.errors[errKeys[0]][0];
                } else if (jErr && jErr.message) {
                    errMsg = jErr.message;
                }
                throw new Error(errMsg);
            }
            var data = await res.json();
            createdCheckInId = data.id;
            createdRedirect = data.redirect || null;
            activateCreateQueue();
            await pumpUploads();
            var failed = countCreateErrors();
            if (failed > 0) {
                showError('El inventario se guardó, pero ' + failed + ' ' + singular(failed) + ' no se ' + (failed === 1 ? 'subió' : 'subieron') + '. Pulsa Reintentar sobre cada foto para intentarlo de nuevo.');
                restoreSubmit();
                return;
            }
            redirectAfterCreate();
        } catch (err) {
            showError((err && err.message) ? err.message : 'Error de conexión. Reintenta.');
            restoreSubmit();
        } finally {
            creating = false;
        }
    }

    async function retryFailedAfterCreate() {
        if (!createdCheckInId || creating) return;
        creating = true;
        disableSubmit('Subiendo fotos…');
        showError(null);
        try {
            for (var i = 0; i < photos.length; i++) {
                var p = photos[i];
                if (p.mode === 'create' && p.state === 'error') {
                    p.state = 'pending';
                    p.el.dataset.state = 'pending';
                    setBadge(p, 'none');
                }
            }
            await pumpUploads();
            if (countCreateErrors() > 0) {
                showError('Aún hay fotos sin subir. Reintenta de nuevo o agrégalas luego desde Editar.');
                restoreSubmit();
            } else {
                redirectAfterCreate();
            }
        } finally {
            creating = false;
        }
    }
    // -------------------------------------------------------------
    // Delegación de eventos sobre el grid (retry / delete)
    // -------------------------------------------------------------
    function removeLocalItem(el) {
        var item = findItem(el);
        if (item) {
            var pos = photos.indexOf(item);
            if (pos > -1) photos.splice(pos, 1);
            if (item.localUrl) { URL.revokeObjectURL(item.localUrl); item.localUrl = null; }
        }
        if (el && el.remove) el.remove();
        updateCount();
        updateStatus();
    }

    function deleteServerPhoto(el) {
        var id = el ? el.dataset.id : null;
        if (!id) return Promise.resolve();
        return fetch('/api/check-ins/' + CHECK_IN_ID + '/photos/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' }
        }).then(function (res) {
            if (!res.ok) throw new Error('No se pudo eliminar la foto.');
            if (el && el.remove) el.remove();
            updateCount();
            updateStatus();
        }).catch(function (err) {
            showError((err && err.message) ? err.message : 'No se pudo eliminar la foto.');
        });
    }

    previewEl.addEventListener('click', function (e) {
        var retryBtn = e.target.closest('.photo-retry');
        if (retryBtn) {
            var rEl = retryBtn.closest('.photo-item');
            var rItem = findItem(rEl);
            if (!rItem) return;
            if (rItem.mode === 'create' && !createdCheckInId) {
                showError('Primero guarda el inventario para poder subir sus fotos.');
                return;
            }
            rItem.state = 'pending';
            rItem.el.dataset.state = 'pending';
            setBadge(rItem, 'none');
            if (rItem.mode === 'edit') {
                pumpUploads();
            } else {
                retryFailedAfterCreate();
            }
            return;
        }

        var delBtn = e.target.closest('.photo-delete');
        if (!delBtn) return;
        var el = delBtn.closest('.photo-item');
        if (!el) return;
        var st = el.dataset.state || '';
        if (st === 'uploading' || st === 'preparing') return;

        var id = el.dataset.id;
        if (id && IS_EDIT) {
            if (window.ConfirmModal) {
                window.ConfirmModal.open(null, {
                    message: '¿Eliminar esta foto del inventario?',
                    confirmLabel: 'Eliminar',
                    onConfirm: function () { deleteServerPhoto(el); }
                });
            } else {
                deleteServerPhoto(el);
            }
        } else {
            removeLocalItem(el);
        }
    });

    // -------------------------------------------------------------
    // Encolar fotos (galería o captura nativa)
    // -------------------------------------------------------------
    async function enqueueFile(file, keepOriginal, opts) {
        if (!file) return null;
        var key = 'k' + (++keySeq);
        var item = {
            key: key,
            id: null,
            file: null,
            localUrl: null,
            state: 'preparing',
            mode: IS_EDIT ? 'edit' : 'create',
            el: null
        };
        var div = buildPhotoItem({ key: key });
        item.el = div;
        div.dataset.key = key;
        div.dataset.state = 'preparing';
        previewEl.appendChild(div);
        photos.push(item);
        setBadge(item, 'preparing');
        updateCount();

        try {
            var out = keepOriginal ? file : await compressPhoto(file, opts);
            item.file = out;
            item.localUrl = URL.createObjectURL(out);
            setImg(item, item.localUrl);
        } catch (err) {
            item.file = file;
            item.localUrl = URL.createObjectURL(file);
            setImg(item, item.localUrl);
        }

        if (!IS_EDIT) {
            item.state = 'queued';
            div.dataset.state = 'queued';
            setBadge(item, 'queued');
            return item;
        }
        item.state = 'pending';
        div.dataset.state = 'pending';
        setBadge(item, 'queued');
        pumpUploads();
        return item;
    }

    async function enqueueFiles(fileList, keepOriginal, opts) {
        var files = Array.prototype.slice.call(fileList || []);
        for (var i = 0; i < files.length; i++) {
            await enqueueFile(files[i], keepOriginal, opts);
        }
    }

    if (galleryInput) {
        galleryInput.addEventListener('change', function () {
            var files = this.files;
            this.value = '';
            enqueueFiles(files, false);
        });
    }
    if (captureInput) {
        captureInput.addEventListener('change', function () {
            var files = this.files;
            this.value = '';
            readRatio();
            readQuality();
            var qp = currentQuality();
            enqueueFiles(files, false, { cropRatio: camRatioKey, maxSide: qp.maxSide, quality: qp.quality });
        });
    }
    if (uploadBtn) {
        uploadBtn.addEventListener('click', function () {
            if (galleryInput) { galleryInput.value = ''; galleryInput.click(); }
        });
    }

    // -------------------------------------------------------------
    // Cámara integrada: lente principal, vista previa WYSIWYG, calidad, proporción y zoom
    // -------------------------------------------------------------
    var camStream = null;
    var camOpen = false;
    var camRaf = 0;
    var camVideo = cameraVideo;
    var camCanvas = document.getElementById('photo-camera-canvas');
    var camStage = document.getElementById('photo-camera-stage');
    var camOriented = null;
    var camManualOffset = 0;
    var camRatioKey = RATIO_DEFAULT;
    var camSoftZoom = 1;
    var camNativeZoom = false;
    var camVideoTrack = null;
    var camZoomUi = 1;
    var sessionShots = 0;
    var qualityKey = QUALITY_DEFAULT;
    var backCameras = [];
    var backCameraIndex = -1;
    var camSwitchBtn = document.getElementById('btn-camera-switch');

    function canInlineCamera() {
        return window.isSecureContext && !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
    }

    // ---------- Calidad (presets Estándar / Alta / Máxima) ----------
    function readQuality() {
        var v = QUALITY_DEFAULT;
        try {
            var saved = localStorage.getItem('checkin-photo-quality');
            if (saved && QUALITY_PRESETS[saved]) v = saved;
        } catch (e) {}
        qualityKey = v;
    }

    function persistQuality() {
        try { localStorage.setItem('checkin-photo-quality', qualityKey); } catch (e) {}
    }

    function currentQuality() {
        return QUALITY_PRESETS[qualityKey] || QUALITY_PRESETS[QUALITY_DEFAULT];
    }

    function updateQualityUI() {
        var bar = document.getElementById('photo-quality-bar');
        if (!bar) return;
        var btns = bar.querySelectorAll('.quality-btn');
        for (var i = 0; i < btns.length; i++) {
            var b = btns[i];
            if (b.dataset.quality === qualityKey) {
                b.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                b.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
            } else {
                b.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
                b.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
            }
        }
    }

    // ---------- Proporción (3:4 por defecto) ----------
    function readRatio() {
        var v = RATIO_DEFAULT;
        try {
            var saved = localStorage.getItem('checkin-photo-ratio');
            if (saved) v = saved;
        } catch (e) {}
        camRatioKey = v;
    }

    function persistRatio() {
        try { localStorage.setItem('checkin-photo-ratio', camRatioKey); } catch (e) {}
    }

    function camRatioObj() {
        for (var i = 0; i < RATIOS.length; i++) {
            if (RATIOS[i].key === camRatioKey) return RATIOS[i];
        }
        return RATIOS[0];
    }

    function camAspect() {
        var r = camRatioObj();
        return r.w / r.h;
    }

    function updateRatioUI() {
        if (!cameraModal) return;
        var btns = cameraModal.querySelectorAll('.cam-ratio-btn');
        for (var i = 0; i < btns.length; i++) {
            var b = btns[i];
            if (b.dataset.ratio === camRatioKey) {
                b.classList.remove('bg-white/15');
                b.classList.add('bg-blue-600');
            } else {
                b.classList.remove('bg-blue-600');
                b.classList.add('bg-white/15');
            }
        }
    }

    // ---------- Zoom ----------
    function updateZoomUI() {
        var label = document.getElementById('camera-zoom-label');
        if (label) label.textContent = camZoomUi + '×';
    }

    function zoomSteps() {
        return ZOOM_STEPS;
    }

    function setCamZoom(z) {
        var steps = zoomSteps();
        if (z < steps[0]) z = steps[0];
        if (z > steps[steps.length - 1]) z = steps[steps.length - 1];
        camZoomUi = z;
        updateZoomUI();
        if (camNativeZoom && camVideoTrack && camVideoTrack.applyConstraints) {
            camSoftZoom = 1;
            camVideoTrack.applyConstraints({ advanced: [{ zoom: z }] }).catch(function () {
                camNativeZoom = false;
                camSoftZoom = z;
            });
        } else {
            camSoftZoom = z;
        }
    }

    function nextZoom(dir) {
        var steps = zoomSteps();
        var i = steps.indexOf(camZoomUi);
        if (i === -1) i = 0;
        var j = Math.min(steps.length - 1, Math.max(0, i + dir));
        return steps[j];
    }

    function camZoomEffective() {
        return camNativeZoom ? 1 : camSoftZoom;
    }

    function currentRotation() {
        if (!camVideo || !camVideo.videoWidth) return 0;
        var w = camVideo.videoWidth;
        var h = camVideo.videoHeight;
        var auto = 0;
        var portrait = window.innerHeight > window.innerWidth;
        if (portrait && w > h) auto = 90;
        else if (!portrait && h > w) auto = 270;
        return (auto + camManualOffset) % 360;
    }    function orientedCanvas() {
        if (!camVideo || !camVideo.videoWidth || !camVideo.videoHeight) return null;
        var w = camVideo.videoWidth;
        var h = camVideo.videoHeight;
        var rot = currentRotation();
        var needW = (rot === 90 || rot === 270) ? h : w;
        var needH = (rot === 90 || rot === 270) ? w : h;
        if (!camOriented) camOriented = document.createElement('canvas');
        if (camOriented.width !== needW || camOriented.height !== needH) {
            camOriented.width = needW;
            camOriented.height = needH;
        }
        var ctx = camOriented.getContext('2d');
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, needW, needH);
        if (rot === 0) {
            ctx.drawImage(camVideo, 0, 0, needW, needH);
        } else {
            ctx.translate(needW / 2, needH / 2);
            ctx.rotate((rot * Math.PI) / 180);
            ctx.drawImage(camVideo, -w / 2, -h / 2);
            ctx.setTransform(1, 0, 0, 1, 0, 0);
        }
        return camOriented;
    }

    function cameraCropRect(ow, oh) {
        var aspect = camAspect();
        var bw = ow;
        var bh = ow / aspect;
        if (bh > oh) {
            bh = oh;
            bw = oh * aspect;
        }
        var z = camZoomEffective();
        var sw = Math.max(1, bw / z);
        var sh = Math.max(1, bh / z);
        return { x: (ow - sw) / 2, y: (oh - sh) / 2, w: sw, h: sh };
    }

    function startCameraLoop() {
        cancelCameraLoop();
        camRaf = requestAnimationFrame(cameraTick);
    }

    function cancelCameraLoop() {
        if (camRaf) {
            cancelAnimationFrame(camRaf);
            camRaf = 0;
        }
    }

    function cameraTick() {
        if (!camOpen) { camRaf = 0; return; }
        if (!camVideo || !camVideo.videoWidth || !camVideo.videoHeight || !camCanvas) {
            camRaf = requestAnimationFrame(cameraTick);
            return;
        }
        var ori = orientedCanvas();
        if (!ori) { camRaf = requestAnimationFrame(cameraTick); return; }
        var crop = cameraCropRect(ori.width, ori.height);
        var outW = Math.max(1, Math.round(crop.w));
        var outH = Math.max(1, Math.round(crop.h));
        if (camCanvas.width !== outW || camCanvas.height !== outH) {
            camCanvas.width = outW;
            camCanvas.height = outH;
        }
        var ctx = camCanvas.getContext('2d');
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, outW, outH);
        ctx.drawImage(ori, crop.x, crop.y, crop.w, crop.h, 0, 0, outW, outH);
        camRaf = requestAnimationFrame(cameraTick);
    }

    // ---------- Detección del lente principal ----------
    function getUserMediaWrapper(c) {
        return navigator.mediaDevices.getUserMedia(c);
    }

    function detectBackCameras() {
        return navigator.mediaDevices.enumerateDevices().then(function (devices) {
            var cams = [];
            for (var i = 0; i < devices.length; i++) {
                if (devices[i].kind !== 'videoinput') continue;
                cams.push(devices[i]);
            }
            var backs = [];
            for (var j = 0; j < cams.length; j++) {
                var l = String(cams[j].label || '').toLowerCase();
                var isFront = l.indexOf('front') > -1 || l.indexOf('delantera') > -1;
                var isBack = l.indexOf('back') > -1 || l.indexOf('rear') > -1 || l.indexOf('trasera') > -1 || l.indexOf('facing back') > -1;
                if (isBack && !isFront) backs.push(cams[j]);
            }
            if (!backs.length) backs = cams;
            return backs;
        }).then(function (backs) {
            var probed = [];
            function next(i) {
                if (i >= backs.length) return probed;
                return getUserMediaWrapper({
                    video: { deviceId: { exact: backs[i].deviceId }, width: { ideal: 640 }, height: { ideal: 480 } },
                    audio: false
                }).then(function (s) {
                    var track = s.getVideoTracks()[0];
                    var caps = track.getCapabilities ? track.getCapabilities() : {};
                    probed.push({
                        deviceId: backs[i].deviceId,
                        label: backs[i].label || '',
                        widthMax: (caps && caps.width && caps.width.max) || 0,
                        focalMax: (caps && caps.focalLength && caps.focalLength.max) || 0
                    });
                    track.stop();
                    return next(i + 1);
                }).catch(function () {
                    return next(i + 1);
                });
            }
            return next(0);
        });
    }

    function bestMainIndex(list) {
        if (!list.length) return -1;
        var hasFocal = false;
        for (var i = 0; i < list.length; i++) {
            if (list[i].focalMax > 0) hasFocal = true;
        }
        list.sort(function (a, b) {
            if (hasFocal) return b.focalMax - a.focalMax;
            return b.widthMax - a.widthMax;
        });
        return 0;
    }

    function updateCameraLensUI() {
        if (camSwitchBtn) camSwitchBtn.classList.toggle('hidden', !(backCameras && backCameras.length > 1));
    }

    // ---------- Apertura de stream ----------
    function openStreamForDevice(devId) {
        var vc = { facingMode: { ideal: 'environment' }, width: { ideal: 2560 }, height: { ideal: 1920 } };
        if (devId) vc.deviceId = { exact: devId };
        return getUserMediaWrapper({ video: vc, audio: false }).then(startStreamAndLoop);
    }

    function startStreamAndLoop(stream) {
        camStream = stream;
        camOpen = true;
        sessionShots = 0;
        camSoftZoom = 1;
        camZoomUi = 1;
        camManualOffset = 0;
        camNativeZoom = false;
        camOriented = null;
        camVideoTrack = stream.getVideoTracks ? stream.getVideoTracks()[0] : null;
        if (camVideoTrack && camVideoTrack.getCapabilities) {
            var caps = camVideoTrack.getCapabilities();
            if (caps && caps.zoom && caps.zoom.max > 1) camNativeZoom = true;
        }
        readRatio();
        readQuality();
        updateQualityUI();
        if (camVideo) camVideo.srcObject = stream;
        if (cameraModal) {
            cameraModal.classList.remove('hidden');
            updateRatioUI();
            updateZoomUI();
            updateCameraLensUI();
        }
        if (cameraStatusEl) {
            cameraStatusEl.textContent = (backCameras && backCameras.length > 1)
                ? 'Lente principal en uso. Si no se ve nítido, toca el icono de cámaras para cambiar de lente.'
                : 'Toma las fotos: cada disparo se encola y se sube solo.';
        }
        if (camVideo) {
            return camVideo.play().catch(function () {}).then(function () {
                startCameraLoop();
                return null;
            });
        }
        return null;
    }

    function openInlineCamera() {
        // 1) Permiso + etiquetas de dispositivos
        return getUserMediaWrapper({ video: { facingMode: { ideal: 'environment' }, width: { ideal: 640 } }, audio: false })
            .then(function (probe) {
                probe.getTracks().forEach(function (t) { t.stop(); });
                return detectBackCameras();
            })
            .then(function (list) {
                backCameras = [];
                if (list && list.length) {
                    bestMainIndex(list);
                    backCameras = list;
                    backCameraIndex = 0;
                } else {
                    backCameraIndex = -1;
                }
                return openStreamForDevice(backCameras.length ? backCameras[0].deviceId : null);
            })
            .catch(function () {
                backCameras = [];
                backCameraIndex = -1;
                return openStreamForDevice(null);
            });
    }

    function stopCameraStream() {
        camOpen = false;
        cancelCameraLoop();
        camOriented = null;
        camVideoTrack = null;
        camNativeZoom = false;
        if (camStream) {
            camStream.getTracks().forEach(function (t) { t.stop(); });
            camStream = null;
        }
        if (camVideo) camVideo.srcObject = null;
    }

    function closeInlineCamera() {
        stopCameraStream();
        if (cameraModal) cameraModal.classList.add('hidden');
    }

    function switchCameraLens() {
        if (!backCameras || backCameras.length < 2 || !camStream) return;
        backCameraIndex = (backCameraIndex + 1) % backCameras.length;
        stopCameraStream();
        openStreamForDevice(backCameras[backCameraIndex].deviceId).catch(function () {
            if (cameraStatusEl) cameraStatusEl.textContent = 'No se pudo cambiar de lente.';
        });
    }

    function openNativeCamera() {
        if (captureInput) {
            captureInput.value = '';
            readRatio();
            readQuality();
            captureInput.click();
        }
    }    function shootPhoto() {
        var ori = camOriented;
        if (!camOpen || !ori || !camCanvas) return;
        var qp = currentQuality();
        var crop = cameraCropRect(ori.width, ori.height);
        var scale = Math.min(1, qp.maxSide / Math.max(crop.w, crop.h));
        var outW = Math.max(1, Math.round(crop.w * scale));
        var outH = Math.max(1, Math.round(crop.h * scale));
        var out = document.createElement('canvas');
        out.width = outW;
        out.height = outH;
        var ctx = out.getContext('2d');
        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, outW, outH);
        ctx.drawImage(ori, crop.x, crop.y, crop.w, crop.h, 0, 0, outW, outH);
        out.toBlob(function (blob) {
            if (!blob) return;
            var file = new File([blob], 'foto-' + Date.now() + '.jpg', { type: 'image/jpeg' });
            enqueueFile(file, true);
            sessionShots++;
            if (cameraStatusEl) cameraStatusEl.textContent = 'Fotos tomadas: ' + sessionShots + ' · calidad ' + qp.label + ' · se suben en segundo plano.';
        }, 'image/jpeg', qp.quality);
    }

    function handleShutter() {
        shootPhoto();
    }

    if (captureBtn) {
        captureBtn.addEventListener('click', function () {
            if (canInlineCamera() && cameraModal && camCanvas && camVideo) {
                openInlineCamera().catch(function () { openNativeCamera(); });
            } else {
                openNativeCamera();
            }
        });
    }
    if (cameraShutterBtn) cameraShutterBtn.addEventListener('click', handleShutter);
    if (cameraCloseBtn) cameraCloseBtn.addEventListener('click', closeInlineCamera);
    if (cameraModal) {
        cameraModal.addEventListener('click', function (e) {
            if (e.target === cameraModal || e.target === camStage) closeInlineCamera();
        });
        var ratioBtns = cameraModal.querySelectorAll('.cam-ratio-btn');
        for (var i = 0; i < ratioBtns.length; i++) {
            (function (btn) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    camRatioKey = btn.dataset.ratio;
                    persistRatio();
                    updateRatioUI();
                });
            })(ratioBtns[i]);
        }
    }
    var zoomInBtn = document.getElementById('btn-camera-zoom-in');
    var zoomOutBtn = document.getElementById('btn-camera-zoom-out');
    var rotateBtn = document.getElementById('btn-camera-rotate');
    if (zoomInBtn) zoomInBtn.addEventListener('click', function (e) { e.stopPropagation(); setCamZoom(nextZoom(1)); });
    if (zoomOutBtn) zoomOutBtn.addEventListener('click', function (e) { e.stopPropagation(); setCamZoom(nextZoom(-1)); });
    if (rotateBtn) rotateBtn.addEventListener('click', function (e) { e.stopPropagation(); camManualOffset = (camManualOffset + 90) % 360; });
    if (camSwitchBtn) camSwitchBtn.addEventListener('click', function (e) { e.stopPropagation(); switchCameraLens(); });
    if (camCanvas) {
        camCanvas.addEventListener('dblclick', function (e) {
            e.preventDefault();
            e.stopPropagation();
            setCamZoom(camZoomUi === 2 ? 1 : 2);
        });
        var lastTap = 0;
        camCanvas.addEventListener('touchend', function (e) {
            var now = Date.now();
            if (now - lastTap < 300) {
                e.preventDefault();
                setCamZoom(camZoomUi === 2 ? 1 : 2);
                lastTap = 0;
            } else {
                lastTap = now;
            }
        });
    }

    // Selector de calidad (chips junto a los botones de la sección)
    var qualityBar = document.getElementById('photo-quality-bar');
    if (qualityBar) {
        qualityBar.addEventListener('click', function (e) {
            var b = e.target.closest('.quality-btn');
            if (!b) return;
            qualityKey = b.dataset.quality;
            persistQuality();
            updateQualityUI();
        });
    }

    // -------------------------------------------------------------
    // Creación: intercepta el submit SOLO si hay fotos en cola
    // (window capture corre antes que form-guard; sin fotos pendientes
    //  el envío normal del formulario queda intacto)
    // -------------------------------------------------------------
    if (!IS_EDIT && formEl) {
        window.addEventListener('submit', function (e) {
            if (e.target !== formEl) return;
            var hasQueued = false;
            var hasErrored = false;
            for (var i = 0; i < photos.length; i++) {
                var p = photos[i];
                if (p.mode !== 'create') continue;
                if (p.state === 'queued') hasQueued = true;
                if (p.state === 'error') hasErrored = true;
            }
            if (!hasQueued && !(createdCheckInId && hasErrored)) return;
            e.preventDefault();
            e.stopImmediatePropagation();
            if (createdCheckInId) {
                retryFailedAfterCreate();
            } else {
                createAndUpload();
            }
        }, true);
    }
    readQuality();
    updateQualityUI();
    updateCount();
})();
</script>
@endpush