import Plugin from 'src/plugin-system/plugin.class';

export default class ApplicationFormPlugin extends Plugin {
    init() {
        const fileInputs = [
            { input: 'anschreiben_file', placeholder: 'anschreiben' },
            { input: 'lebenslauf_file', placeholder: 'lebenslauf' },
            { input: 'zertifikate_file', placeholder: 'zertifikate' },
            { input: 'arbeitszeugnisse_file', placeholder: 'arbeitszeugnisse' }
        ];

        fileInputs.forEach(({ input, placeholder }) => {
            const fileInput = document.getElementById(input);
            const placeholderElement = document.getElementById(placeholder);

            if (fileInput && placeholderElement) {
                fileInput.addEventListener('change', function() {
                    const fileName = this.value.split('\\').pop();
                    placeholderElement.setAttribute('placeholder', fileName);
                });
            }
        });
    }
}