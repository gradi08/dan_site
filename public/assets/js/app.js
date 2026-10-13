/**
 * VideGrenier+ — amélioration progressive. Le site fonctionne sans JavaScript ;
 * ce fichier ajoute la gestion des photos multiples, la galerie, le compteur de caractères, la synchro ARIA
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

    // Photos multiples : aperçus, retrait, limite, redimensionnement avant envoi.
    const photos = document.querySelector('.photos');
    if (photos) {
        const input = photos.querySelector('input[type="file"]');
        const dropzone = photos.querySelector('.dropzone');
        const previews = photos.querySelector('[data-previews]');
        const label = photos.querySelector('[data-dropzone-label]');
        const count = photos.querySelector('[data-dropzone-count]');
        const removeBoxes = [...photos.querySelectorAll('[data-remove-photo]')];
        const max = Number(photos.dataset.max);
        const existing = Number(photos.dataset.existing);
        const hint = count.textContent;
        const MAX_SIDE = 1600;
        let selected = [];

        const kept = () => existing - removeBoxes.filter((box) => box.checked).length;
        const slots = () => max - kept() - selected.length;

        // Réduit la photo côté navigateur : envoi plus rapide et moins de données mobiles.
        const resize = async (file) => {
            try {
                const bitmap = await createImageBitmap(file, { imageOrientation: 'from-image' });
                const ratio = Math.min(1, MAX_SIDE / Math.max(bitmap.width, bitmap.height));
                if (ratio === 1 && file.size < 1024 * 1024) return file;
                const canvas = document.createElement('canvas');
                canvas.width = Math.round(bitmap.width * ratio);
                canvas.height = Math.round(bitmap.height * ratio);
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
                const blob = await new Promise((done) => canvas.toBlob(done, 'image/jpeg', 0.85));
                return blob ? new File([blob], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' }) : file;
            } catch {
                return file; // Le serveur validera et ré-encodera de toute façon.
            }
        };

        const sync = () => {
            const transfer = new DataTransfer();
            selected.forEach((item) => transfer.items.add(item.file));
            input.files = transfer.files;
            input.required = kept() + selected.length === 0;
            input.removeAttribute('aria-invalid');

            const total = kept() + selected.length;
            count.textContent = total > 0 ? `${total} photo${total > 1 ? 's' : ''} sur ${max}` : hint;
            label.textContent = slots() > 0 ? (total > 0 ? 'Ajouter des photos' : 'Choisir des photos') : 'Nombre maximum atteint';
            dropzone.classList.toggle('is-full', slots() <= 0);

            previews.replaceChildren(...selected.map((item, index) => {
                const li = document.createElement('li');
                li.className = 'photo-tile';
                const img = document.createElement('img');
                img.src = item.url;
                img.alt = '';
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'photo-tile__drop';
                button.textContent = 'Retirer';
                button.setAttribute('aria-label', `Retirer ${item.file.name}`);
                button.addEventListener('click', () => {
                    URL.revokeObjectURL(item.url);
                    selected.splice(index, 1);
                    sync();
                });
                li.append(img, button);
                return li;
            }));
            previews.hidden = selected.length === 0;
        };

        const add = async (files) => {
            const images = [...files].filter((file) => /^image\/(jpeg|png|webp)$/.test(file.type));
            const accepted = images.slice(0, Math.max(0, slots()));
            for (const file of accepted) {
                const resized = await resize(file);
                selected.push({ file: resized, url: URL.createObjectURL(resized) });
            }
            sync();
            if (images.length > accepted.length) {
                count.textContent = `${max} photos maximum : ${images.length - accepted.length} photo(s) non ajoutée(s).`;
            }
        };

        input.addEventListener('change', () => {
            const files = [...input.files];
            // input.files vient d'être remplacé par le choix de l'utilisateur : on repart de la sélection mémorisée.
            const transfer = new DataTransfer();
            selected.forEach((item) => transfer.items.add(item.file));
            input.files = transfer.files;
            add(files);
        });

        removeBoxes.forEach((box) => box.addEventListener('change', () => {
            box.closest('.photo-tile').classList.toggle('is-removed', box.checked);
            sync();
        }));

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
            if (event.dataTransfer?.files.length) add(event.dataTransfer.files);
        });
    }

    // Galerie de la fiche objet : miniatures synchronisées avec le défilement.
    const track = document.querySelector('.gallery__track');
    const thumbs = [...document.querySelectorAll('.gallery__thumbs a')];
    if (track && thumbs.length) {
        thumbs.forEach((thumb) => thumb.addEventListener('click', (event) => {
            event.preventDefault();
            const slide = document.querySelector(thumb.getAttribute('href'));
            track.scrollTo({ left: slide.offsetLeft, behavior: 'smooth' });
        }));

        const observer = new IntersectionObserver((entries) => {
            entries.filter((entry) => entry.isIntersecting).forEach((entry) => {
                thumbs.forEach((thumb) => {
                    if (thumb.getAttribute('href') === `#${entry.target.id}`) {
                        thumb.setAttribute('aria-current', 'true');
                    } else {
                        thumb.removeAttribute('aria-current');
                    }
                });
            });
        }, { root: track, threshold: 0.6 });
        track.querySelectorAll('.gallery__slide').forEach((slide) => observer.observe(slide));
    }
})();
