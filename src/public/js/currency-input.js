(function() {
    function init(el) {
        if (el.dataset.ci) return;
        el.dataset.ci = '1';
        el.type = 'text';
        el.inputMode = 'numeric';

        function format(raw) {
            raw = raw.replace(/[^0-9jtJTrbRBkK,]/g, '').toLowerCase();

            let mult = 1;
            if (raw.endsWith('jt') || raw.endsWith('juta')) {
                mult = 1000000;
                raw = raw.replace(/juta?$/, '');
            } else if (raw.endsWith('rb') || raw.endsWith('ribu')) {
                mult = 1000;
                raw = raw.replace(/ribu?$/, '');
            } else if (raw.endsWith('k')) {
                mult = 1000;
                raw = raw.slice(0, -1);
            }

            let num = parseFloat(raw.replace(/,/g, '.'));
            if (isNaN(num)) return null;

            let total = Math.round(num * mult);
            el.dataset.raw = total.toString();
            return total.toLocaleString('id-ID');
        }

        function setRaw(val) {
            el.dataset.guard = 'true';
            el.value = val;
            el.dataset.raw = val;
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dataset.guard = 'false';
        }

        el.addEventListener('input', function() {
            if (el.dataset.guard === 'true') return;
            let f = format(this.value);
            if (f === null) { if (this.value !== '') this.value = ''; return; }
            this.value = f;
        });

        el.addEventListener('blur', function() {
            let raw = el.dataset.raw || el.value.replace(/\./g, '');
            let num = parseInt(raw, 10);
            if (isNaN(num) || num <= 0) return;
            setRaw(num.toString());
        });

        if (el.value && el.value !== '') {
            let clean = el.value.replace(/\./g, '');
            if (/^\d+$/.test(clean)) {
                let num = parseInt(clean, 10);
                if (!isNaN(num) && num > 0) {
                    el.value = num.toLocaleString('id-ID');
                    el.dataset.raw = num.toString();
                }
            }
        }
    }

    function scan() {
        document.querySelectorAll('input[data-field="amount"], input[data-field="target_amount"], input[data-field="current_amount"]').forEach(init);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scan);
    } else {
        scan();
    }

    new MutationObserver(scan).observe(document.body, { childList: true, subtree: true });

    document.addEventListener('livewire:submit', function() {
        document.querySelectorAll('input[data-field="amount"], input[data-field="target_amount"], input[data-field="current_amount"]').forEach(function(el) {
            let raw = el.dataset.raw || el.value.replace(/\./g, '');
            let num = parseInt(raw, 10);
            if (!isNaN(num) && num > 0) {
                el.dataset.guard = 'true';
                el.value = num.toString();
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dataset.guard = 'false';
            }
        });
    });
})();
