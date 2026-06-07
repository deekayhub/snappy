<script src="https://cdn.jsdelivr.net/npm/heic2any/dist/heic2any.min.js"></script>
<script>
    (function () {
        const MAX_SIZE_BYTES = 10 * 1024 * 1024;

        function isHeicFile(file) {
            if (!file) {
                return false;
            }

            const type = (file.type || '').toLowerCase();
            const name = (file.name || '').toLowerCase();

            return type === 'image/heic'
                || type === 'image/heif'
                || name.endsWith('.heic')
                || name.endsWith('.heif');
        }

        function isAllowedImage(file) {
            if (!file) {
                return false;
            }

            const type = (file.type || '').toLowerCase();
            const name = (file.name || '').toLowerCase();

            return ['image/jpeg', 'image/png', 'image/heic', 'image/heif'].includes(type)
                || /\.(jpe?g|png|heic|heif)$/.test(name);
        }

        function setMessage(wrapper, message) {
            const errorEl = wrapper.querySelector('[data-profile-picture-error]');
            if (!errorEl) {
                return;
            }

            errorEl.textContent = message || '';
            errorEl.classList.toggle('d-none', !message);
        }

        function setPreview(wrapper, src) {
            const preview = wrapper.querySelector('[data-profile-picture-preview]');
            const placeholder = wrapper.querySelector('[data-profile-picture-placeholder]');

            if (!preview || !placeholder) {
                return;
            }

            if (src) {
                preview.src = src;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
            } else {
                preview.removeAttribute('src');
                preview.classList.add('d-none');
                placeholder.classList.remove('d-none');
            }
        }

        function setInputFile(input, file) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;
        }

        async function convertHeic(file) {
            if (typeof window.heic2any !== 'function') {
                throw new Error('The HEIF conversion tool is not available.');
            }

            const converted = await window.heic2any({
                blob: file,
                toType: 'image/jpeg',
                quality: 0.9,
            });

            const blob = Array.isArray(converted) ? converted[0] : converted;
            const fileName = file.name.replace(/\.(heic|heif)$/i, '.jpg');

            return new File([blob], fileName, { type: 'image/jpeg' });
        }

        async function handleFileSelection(input) {
            const wrapper = input.closest('[data-profile-picture-uploader]');
            if (!wrapper || !input.files || !input.files.length) {
                return;
            }

            const originalFile = input.files[0];
            setMessage(wrapper, '');

            if (!isAllowedImage(originalFile)) {
                input.value = '';
                setPreview(wrapper, '');
                setMessage(wrapper, 'Please choose a JPG, JPEG, PNG, HEIC, or HEIF image.');
                return;
            }

            input.dataset.converting = '1';

            try {
                let finalFile = originalFile;

                if (isHeicFile(originalFile)) {
                    finalFile = await convertHeic(originalFile);
                }

                if (finalFile.size > MAX_SIZE_BYTES) {
                    input.value = '';
                    setPreview(wrapper, '');
                    setMessage(wrapper, 'Profile picture must not be larger than 10 MB.');
                    return;
                }

                setInputFile(input, finalFile);
                setPreview(wrapper, URL.createObjectURL(finalFile));
            } catch (error) {
                input.value = '';
                setPreview(wrapper, '');
                setMessage(wrapper, 'We could not prepare this photo. Please try another image or upload a JPG or PNG instead.');
            } finally {
                input.dataset.converting = '0';
            }
        }

        function bindForms() {
            document.querySelectorAll('[data-profile-picture-uploader]').forEach((wrapper) => {
                const input = wrapper.querySelector('[data-profile-picture-input]');
                const form = input?.form;

                if (input) {
                    input.addEventListener('change', function () {
                        handleFileSelection(this);
                    });
                }

                if (form) {
                    form.addEventListener('submit', function (event) {
                        const converting = this.querySelector('[data-profile-picture-input][data-converting="1"]');
                        if (converting) {
                            event.preventDefault();
                            setMessage(wrapper, 'Please wait while the profile picture is being prepared.');
                        }
                    });
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bindForms);
        } else {
            bindForms();
        }
    })();
</script>
