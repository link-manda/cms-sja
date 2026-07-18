<script>
    document.addEventListener('DOMContentLoaded', function () {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        const maxFileSize = 4 * 1024 * 1024;
        const maxTotal = 10;

        const zones = Array.from(document.querySelectorAll('.calc-dropzone'));

        const totalSelected = () => zones.reduce((sum, z) => {
            const input = z.querySelector('.calc-file-input');
            return sum + (input.files ? input.files.length : 0);
        }, 0);

        zones.forEach(zone => {
            const input = zone.querySelector('.calc-file-input');
            const preview = document.getElementById(zone.dataset.preview);
            const errorBox = document.getElementById(zone.dataset.errors);

            const showErrors = (messages) => {
                errorBox.innerHTML = '';
                messages.forEach(m => {
                    const p = document.createElement('p');
                    p.className = 'flex items-center gap-1';
                    p.textContent = m;
                    errorBox.appendChild(p);
                });
                errorBox.classList.remove('hidden');
            };
            const clearErrors = () => { errorBox.innerHTML = ''; errorBox.classList.add('hidden'); };

            const validate = (files) => {
                const errors = [];
                Array.from(files).forEach(file => {
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (!allowedTypes.includes(file.type) || !allowedExt.includes(ext)) {
                        errors.push(`${file.name}: format must be JPG, PNG, or WEBP.`);
                    }
                    if (file.size > maxFileSize) {
                        errors.push(`${file.name}: size may not exceed 4 MB.`);
                    }
                });
                return errors;
            };

            const render = (files) => {
                preview.innerHTML = '';
                if (!files.length) { preview.classList.add('hidden'); return; }
                preview.classList.remove('hidden');
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = 'relative rounded overflow-hidden aspect-square border border-default-200';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover';
                        img.alt = file.name;
                        div.appendChild(img);
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            };

            const handle = (files) => {
                const errors = validate(files);
                // Hitung total lintas zona (kurangi file lama zona ini, tambah yang baru).
                const others = totalSelected() - (input.files ? input.files.length : 0);
                if (others + files.length > maxTotal) {
                    errors.push(`Total images across all zones may not exceed ${maxTotal}.`);
                }
                if (errors.length) {
                    showErrors(errors);
                    input.value = '';
                    render([]);
                    return;
                }
                clearErrors();
                render(files);
            };

            input.addEventListener('change', (e) => handle(e.target.files));

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev =>
                zone.addEventListener(ev, (e) => { e.preventDefault(); e.stopPropagation(); }));
            ['dragenter', 'dragover'].forEach(ev =>
                zone.addEventListener(ev, () => zone.classList.add('bg-primary/10', 'border-primary')));
            ['dragleave', 'drop'].forEach(ev =>
                zone.addEventListener(ev, () => zone.classList.remove('bg-primary/10', 'border-primary')));
            zone.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                const dt = new DataTransfer();
                Array.from(files).forEach(f => dt.items.add(f));
                input.files = dt.files;
                handle(input.files);
            });
        });
    });
</script>
