function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function setFormError(form, message) {
    var el = form.querySelector('.js-form-error');
    if (!el) return;

    if (!message) {
        el.textContent = '';
        el.classList.add('hidden');
        return;
    }

    el.textContent = message;
    el.classList.remove('hidden');
}

function firstValidationMessage(data) {
    if (!data || !data.errors) return 'Validation error.';

    var errors = data.errors;
    var keys = Object.keys(errors);
    for (var i = 0; i < keys.length; i += 1) {
        var k = keys[i];
        if (errors[k] && errors[k][0]) return errors[k][0];
    }

    return 'Validation error.';
}

function htmlToElement(html) {
    var tpl = document.createElement('template');
    tpl.innerHTML = String(html || '').trim();
    return tpl.content.firstElementChild;
}

function ensureToastRoot() {
    var existing = document.getElementById('client-toast');
    if (existing) return existing;

    var root = document.createElement('div');
    root.id = 'client-toast';
    root.className = 'pointer-events-none fixed inset-x-0 top-4 z-[120] flex justify-center px-4 sm:justify-end';
    document.body.appendChild(root);
    return root;
}

function showToast(toast) {
    if (!toast || !toast.message) return;

    var root = ensureToastRoot();
    root.innerHTML = '';

    var card = document.createElement('div');
    card.className = 'pointer-events-auto w-full max-w-sm rounded-2xl bg-white/95 p-4 shadow-sm ring-1 ring-slate-900/10 backdrop-blur dark:bg-slate-900/90 dark:ring-white/10 border border-indigo-200/70 dark:border-indigo-500/20';

    var row = document.createElement('div');
    row.className = 'flex items-start gap-3';

    var dot = document.createElement('div');
    dot.className = 'mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-indigo-600';

    var msg = document.createElement('div');
    msg.className = 'min-w-0 flex-1 text-sm font-semibold text-slate-900 dark:text-slate-100';
    msg.textContent = toast.message;

    var ok = document.createElement('button');
    ok.type = 'button';
    ok.className = '-m-1 inline-flex rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white dark:focus-visible:ring-indigo-900/40';
    ok.textContent = 'OK';

    ok.addEventListener('click', function () {
        root.innerHTML = '';
    });

    row.appendChild(dot);
    row.appendChild(msg);
    row.appendChild(ok);
    card.appendChild(row);
    root.appendChild(card);

    window.setTimeout(function () {
        root.innerHTML = '';
    }, 3200);
}

function ajaxSubmitForm(form) {
    setFormError(form, '');

    var formData = new FormData(form);

    var render = form.getAttribute('data-render');
    if (render) {
        formData.set('_render', render);
    }
    return fetch(form.action, {
        method: String(form.getAttribute('method') || 'POST').toUpperCase(),
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: formData,
    }).then(function (res) {
        if (res.status === 422) {
            return res.json().catch(function () { return null; }).then(function (data) {
                setFormError(form, firstValidationMessage(data));
                return { ok: false, status: 422, data: data };
            });
        }

        if (!res.ok) {
            setFormError(form, 'Something went wrong. Please try again.');
            return { ok: false, status: res.status, data: null };
        }

        return res.json().catch(function () { return null; }).then(function (data) {
            return { ok: true, status: res.status, data: data };
        });
    });
}

function toggleEl(el, openText, closedText, button) {
    var isHidden = el.classList.contains('hidden');
    if (isHidden) {
        el.classList.remove('hidden');
    } else {
        el.classList.add('hidden');
    }

    if (button && openText && closedText) {
        button.textContent = isHidden ? openText : closedText;
    }
}

function normalizeTarget(target) {
    if (!target) return null;

    // Some browsers can report a Text node as the event target (e.g., clicking the “⋯” glyph).
    // Normalize to an Element so closest()/contains() work reliably.
    if (target.nodeType === 3) {
        return target.parentElement || null;
    }

    return target;
}

function bindReplyToggles(root) {
    root.addEventListener('click', function (e) {
        var target = normalizeTarget(e.target);
        if (!target) return;

        var btn = target.closest ? target.closest('[data-reply-toggle]') : null;
        if (!btn) return;

        var targetId = btn.getAttribute('data-target');
        if (!targetId) return;

        var el = document.getElementById(targetId);
        if (!el) return;

        e.preventDefault();
        toggleEl(el, btn.getAttribute('data-label-open'), btn.getAttribute('data-label-closed'), btn);
    });
}

function bindAjaxForms(root) {
    root.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !(form instanceof HTMLFormElement)) return;

        if (form.hasAttribute('data-ajax-post')) {
            e.preventDefault();
            ajaxSubmitForm(form).then(function (result) {
                if (result.ok && result.data) {
                    showToast(result.data.toast);

                    var stay = form.hasAttribute('data-stay');
                    var rootId = form.getAttribute('data-posts-root');
                    if (stay && rootId && result.data.html) {
                        var container = document.getElementById(rootId);
                        var node = htmlToElement(result.data.html);
                        if (container && node) {
                            container.prepend(node);
                            form.reset();
                            setFormError(form, '');
                            return;
                        }
                    }

                    if (result.data.redirect) window.location.assign(result.data.redirect);
                }
            });
            return;
        }

        if (form.hasAttribute('data-ajax-post-update')) {
            e.preventDefault();
            ajaxSubmitForm(form).then(function (result) {
                if (!result.ok || !result.data) return;
                showToast(result.data.toast);

                var postId = result.data.postId;
                if (postId && result.data.html) {
                    var existing = document.getElementById('post-' + postId);
                    var node = htmlToElement(result.data.html);
                    if (existing && node) {
                        existing.replaceWith(node);
                    }
                }

                closePostEditModal();
            });
            return;
        }

        if (form.hasAttribute('data-ajax-comment')) {
            e.preventDefault();
            ajaxSubmitForm(form).then(function (result) {
                if (!result.ok || !result.data) return;
                showToast(result.data.toast);

                var data = result.data;
                if (!data.html) return;

                var node = htmlToElement(data.html);
                if (!node) return;

                var parentId = data.parentId;
                var rootId = parentId ? (form.getAttribute('data-replies-root') || ('comment-replies-' + parentId)) : form.getAttribute('data-comments-root');
                if (!rootId) return;

                var container = document.getElementById(rootId);
                if (!container) return;

                container.prepend(node);
                form.reset();

                var replyWrapperId = form.getAttribute('data-reply-wrapper');
                if (replyWrapperId) {
                    var wrapper = document.getElementById(replyWrapperId);
                    if (wrapper) wrapper.classList.add('hidden');

                    var toggleId = form.getAttribute('data-reply-toggle-id');
                    if (toggleId) {
                        var toggle = document.getElementById(toggleId);
                        if (toggle) toggle.textContent = toggle.getAttribute('data-label-closed') || 'Reply';
                    }
                }
            });
            return;
        }

        if (form.hasAttribute('data-ajax-comment-update')) {
            e.preventDefault();
            ajaxSubmitForm(form).then(function (result) {
                if (!result.ok || !result.data) return;
                showToast(result.data.toast);

                var data = result.data;
                if (!data.commentId || !data.html) return;

                var existing = document.getElementById('comment-' + data.commentId);
                var node = htmlToElement(data.html);
                if (existing && node) {
                    existing.replaceWith(node);
                }
            });
            return;
        }

        if (form.hasAttribute('data-ajax-delete')) {
            e.preventDefault();
            ajaxSubmitForm(form).then(function (result) {
                if (!result.ok || !result.data) return;
                showToast(result.data.toast);

                var data = result.data;
                var removeId = data.commentId ? ('comment-' + data.commentId) : form.getAttribute('data-remove-id');
                if (!removeId) return;

                var el = document.getElementById(removeId);
                if (el) el.remove();
            });
            return;
        }

        if (form.hasAttribute('data-ajax-delete-post')) {
            e.preventDefault();
            ajaxSubmitForm(form).then(function (result) {
                if (!result.ok || !result.data) return;
                showToast(result.data.toast);

                var removeId = form.getAttribute('data-remove-id');
                if (removeId) {
                    var el = document.getElementById(removeId);
                    if (el) el.remove();
                    return;
                }

                var redirect = form.getAttribute('data-redirect');
                if (redirect) window.location.assign(redirect);
            });
        }
    });
}

function bindMenus(root) {
    var openPanel = null;

    function closePanel(panel) {
        if (!panel) return;
        panel.classList.add('hidden');
    }

    function openFor(button) {
        var container = button.parentElement;
        if (!container) return;

        var panel = container.querySelector ? container.querySelector('[data-menu-panel]') : null;
        if (!panel) return;

        if (openPanel && openPanel !== panel) {
            closePanel(openPanel);
        }

        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            openPanel = panel;
        } else {
            closePanel(panel);
            openPanel = null;
        }
    }

    root.addEventListener('click', function (e) {
        var target = normalizeTarget(e.target);
        if (!target) return;

        var btn = target.closest ? target.closest('[data-menu-button]') : null;
        if (btn) {
            e.preventDefault();
            openFor(btn);
            return;
        }

        if (openPanel) {
            var clickedInside = openPanel.contains(target);
            if (!clickedInside) {
                closePanel(openPanel);
                openPanel = null;
            }
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (openPanel) {
            closePanel(openPanel);
            openPanel = null;
        }
    });
}

function bindCommentEdit(root) {
    function toggleEdit(container, open) {
        var display = container.querySelector ? container.querySelector('[data-comment-display]') : null;
        var form = container.querySelector ? container.querySelector('[data-comment-edit-form]') : null;
        if (!display || !form) return;

        if (open) {
            display.classList.add('hidden');
            form.classList.remove('hidden');
            var textarea = form.querySelector ? form.querySelector('textarea[name="body"]') : null;
            if (textarea) textarea.focus();
        } else {
            form.classList.add('hidden');
            display.classList.remove('hidden');
            setFormError(form, '');
        }
    }

    root.addEventListener('click', function (e) {
        var target = normalizeTarget(e.target);
        if (!target) return;

        var openBtn = target.closest ? target.closest('[data-comment-edit-toggle]') : null;
        if (openBtn) {
            var container = openBtn.closest ? openBtn.closest('[data-comment-id]') : null;
            if (!container) return;
            e.preventDefault();
            toggleEdit(container, true);
            return;
        }

        var cancelBtn = target.closest ? target.closest('[data-comment-edit-cancel]') : null;
        if (cancelBtn) {
            var c = cancelBtn.closest ? cancelBtn.closest('[data-comment-id]') : null;
            if (!c) return;
            e.preventDefault();
            toggleEdit(c, false);
        }
    });
}

function openPostEditModal(payload) {
    var modal = document.getElementById('post-edit-modal');
    var form = document.getElementById('post-edit-form');
    if (!modal || !form) return;

    var idInput = document.getElementById('post-edit-post-id');
    var renderInput = document.getElementById('post-edit-render');
    var titleInput = document.getElementById('post-edit-title');
    var tagsInput = document.getElementById('post-edit-tags');
    var bodyInput = document.getElementById('post-edit-body');
    var privateInput = document.getElementById('post-edit-private');

    if (idInput) idInput.value = payload.postId || '';
    if (renderInput) renderInput.value = payload.render || 'index';
    if (titleInput) titleInput.value = payload.title || '';
    if (tagsInput) tagsInput.value = payload.tags || '';
    if (bodyInput) bodyInput.value = payload.body || '';
    if (privateInput) privateInput.checked = payload.isPrivate === '1' || payload.isPrivate === 1 || payload.isPrivate === true;

    form.setAttribute('action', payload.updateUrl || '');
    form.setAttribute('data-render', payload.render || 'index');
    setFormError(form, '');

    modal.classList.remove('hidden');
    document.documentElement.classList.add('overflow-hidden');
    document.body.classList.add('overflow-hidden');

    if (titleInput) titleInput.focus();
}

function closePostEditModal() {
    var modal = document.getElementById('post-edit-modal');
    if (!modal) return;

    modal.classList.add('hidden');
    document.documentElement.classList.remove('overflow-hidden');
    document.body.classList.remove('overflow-hidden');
}

function bindPostEdit(root) {
    root.addEventListener('click', function (e) {
        var target = normalizeTarget(e.target);
        if (!target) return;

        var btn = target.closest ? target.closest('[data-post-edit]') : null;
        if (!btn) return;

        e.preventDefault();
        openPostEditModal({
            postId: btn.getAttribute('data-post-id'),
            title: btn.getAttribute('data-post-title'),
            tags: btn.getAttribute('data-post-tags'),
            body: btn.getAttribute('data-post-body'),
            isPrivate: btn.getAttribute('data-post-private'),
            updateUrl: btn.getAttribute('data-post-update-url'),
            render: btn.getAttribute('data-render') || 'index',
        });
    });

    function bindClose(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('click', function () {
            closePostEditModal();
        });
    }

    bindClose('post-edit-cancel');
    bindClose('post-edit-cancel-2');

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePostEditModal();
    });

    var backdrop = document.getElementById('post-edit-modal');
    if (backdrop) {
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) closePostEditModal();
        });
    }
}

(function initPostsUi() {
    bindReplyToggles(document);
    bindAjaxForms(document);
    bindMenus(document);
    bindCommentEdit(document);
    bindPostEdit(document);
})();
