/**
 * Input masks. Purely cosmetic: the server normalises and re-validates
 * every value.
 */

// IBAN: upper case, groups of 4 characters, caret kept in place.
export function ibanMask() {
    return {
        format(el) {
            const start = el.selectionStart ?? el.value.length;
            const before = el.value.slice(0, start).replace(/[^a-z0-9]/gi, '').length;
            const raw = el.value.replace(/[^a-z0-9]/gi, '').toUpperCase().slice(0, 34);
            const formatted = raw.replace(/(.{4})/g, '$1 ').trim();
            el.value = formatted;
            let pos = 0;
            let count = 0;
            while (pos < formatted.length && count < before) {
                if (formatted[pos] !== ' ') count++;
                pos++;
            }
            el.setSelectionRange(pos, pos);
        },
    };
}

// Digits only (RIB, amounts), optional max length.
export function digitsMask(max = 0) {
    return {
        format(el) {
            let v = el.value.replace(/\D/g, '');
            if (max) v = v.slice(0, max);
            el.value = v;
        },
    };
}
