/**
 * VideGrenier+ — amélioration progressive. Le site fonctionne sans JavaScript ;
 * ce fichier ajoute l'aperçu photo, le compteur de caractères, la synchro ARIA
 * des erreurs et un repli pour les commandes de <dialog> (commandfor/command).
 */
(() => {
    'use strict';

    // Repli Invoker Commands (navigateurs antérieurs à fin 2025).
    if (!('commandForElement' in HTMLButtonElement.prototype)) {
        document.addEventListener('click', (event) => {
            const button = event.target.closest('button[commandfor]');
            const target = button && document.getElementById(button.getAttribute('commandfor'));
            if (!(target instanceof HTMLDialogElement)) return;

            const command = button.getAttribute('command');
            if (command === 'show-modal') target.showModal();
            if (command === 'close') target.close();
        });
    }

    // Fermer le menu compte au clic extérieur ou avec Échap.
    const account = document.querySelector('.account');
    if (account) {
        document.addEventListener('click', (event) => {
            if (account.open && !account.contains(event.target)) account.open = false;
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && account.open) {
                account.open = false;
                account.querySelector('summary').focus();
            }
        });
    }

    // Synchronise aria-invalid avec l'état visuel :user-invalid.
    const syncAria = (field) => {
        if (!field.matches?.('input, textarea, select') || field.type === 'hidden') return;
        if (field.matches(':user-invalid')) {
            field.setAttribute('aria-invalid', 'true');
        } else if (field.checkValidity()) {
            field.removeAttribute('aria-invalid');
        }
    };
    document.addEventListener('blur', (event) => syncAria(event.target), true);
    document.addEventListener('input', (event) => {
        if (event.target.hasAttribute?.('aria-invalid')) syncAria(event.target);
    });

    // Compteur de caractères pour les zones de texte.
    document.querySelectorAll('textarea[data-counter]').forEach((textarea) => {
        const output = document.querySelector(`[data-counter-for="${textarea.id}"]`);
        if (!output) return;
        const max = Number(textarea.maxLength);
        const update = () => {
            output.textContent = `${textarea.value.length} / ${max} caractères`;
        };
        textarea.addEventListener('input', update);
        update();
    });

    // Aperçu de la photo choisie + glisser-déposer.
    const fileInput = document.querySelector('input[type="file"][name="image"]');
    const dropzone = document.querySelector('.dropzone');
    if (fileInput && dropzone) {
        const preview = dropzone.querySelector('.dropzone__preview');
        const label = dropzone.querySelector('.dropzone__text strong');
        let objectUrl = null;

        const showPreview = () => {
            const file = fileInput.files?.[0];
            if (!file) return;
            if (objectUrl) URL.revokeObjectURL(objectUrl);
            objectUrl = URL.createObjectURL(file);
            preview.src = objectUrl;
            preview.hidden = false;
            dropzone.classList.add('has-image');
            label.textContent = file.name;
            fileInput.removeAttribute('aria-invalid');
        };

        fileInput.addEventListener('change', showPreview);

        ['dragenter', 'dragover'].forEach((type) =>
            dropzone.addEventListener(type, (event) => {
                event.preventDefault();
                dropzone.classList.add('is-dragover');
            })
        );
        ['dragleave', 'drop'].forEach((type) =>
            dropzone.addEventListener(type, () => dropzone.classList.remove('is-dragover'))
        );
        dropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            if (event.dataTransfer?.files.length) {
                fileInput.files = event.dataTransfer.files;
                showPreview();
            }
        });
    }
})();
