const MONTHS_ID = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];

const MONTHS_SHORT_ID = [
    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
];

function pad2(n) {
    return String(n).padStart(2, '0');
}

function normalizeIsoDate(raw) {
    if (raw === null || raw === undefined) return '';
    const str = String(raw).trim();
    if (!str) return '';
    const match = str.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (!match) return '';
    const y = Number(match[1]);
    const m = Number(match[2]);
    const d = Number(match[3]);
    if (m < 1 || m > 12 || d < 1 || d > 31) return '';
    const test = new Date(y, m - 1, d);
    if (test.getFullYear() !== y || test.getMonth() !== m - 1 || test.getDate() !== d) {
        return '';
    }
    return `${match[1]}-${match[2]}-${match[3]}`;
}

function getTodayIso() {
    const now = new Date();
    return `${now.getFullYear()}-${pad2(now.getMonth() + 1)}-${pad2(now.getDate())}`;
}

export function sibimaDatePicker(config = {}) {
    const todayIso = getTodayIso();
    const todayParts = todayIso.split('-').map(Number);
    const initialIso = normalizeIsoDate(config.value || '');
    const minIso = normalizeIsoDate(config.min || '');
    const maxIso = normalizeIsoDate(config.max || '');

    const defaultViewParts = initialIso
        ? initialIso.split('-').map(Number)
        : (maxIso && todayIso > maxIso ? maxIso.split('-').map(Number)
            : (minIso && todayIso < minIso ? minIso.split('-').map(Number) : todayParts));

    return {
        value: initialIso,
        min: minIso,
        max: maxIso,
        compact: Boolean(config.compact),
        required: Boolean(config.required),
        disabled: Boolean(config.disabled),
        placeholder: config.placeholder || 'Pilih tanggal...',
        configuredMinYear: config.minYear ? Number(config.minYear) : null,
        configuredMaxYear: config.maxYear ? Number(config.maxYear) : null,

        open: false,
        viewYear: defaultViewParts[0],
        viewMonth: defaultViewParts[1] - 1,
        popoverStyle: 'top: -9999px; left: -9999px;',
        months: MONTHS_ID,
        _cleanupListeners: null,

        init() {
            this.$nextTick(() => {
                const normalized = normalizeIsoDate(this.value);
                if (normalized !== this.value) {
                    this.value = normalized;
                }
                if (this.$refs.input && this.$refs.input.value !== normalized) {
                    this.$refs.input.value = normalized;
                }
                this.syncViewToIso(normalized);
            });

            this.$watch('value', (val) => {
                const normalized = normalizeIsoDate(val);
                if (normalized !== (val || '')) {
                    this.value = normalized;
                    return;
                }
                if (this.$refs.input && this.$refs.input.value !== normalized) {
                    this.$refs.input.value = normalized;
                }
                this.syncViewToIso(normalized);
            });
        },

        destroy() {
            this.detachListeners();
        },

        syncViewToIso(iso) {
            if (!iso) return;
            const [y, m] = iso.split('-').map(Number);
            if (!Number.isNaN(y) && !Number.isNaN(m)) {
                this.viewYear = y;
                this.viewMonth = m - 1;
            }
        },

        syncFromInput(rawVal) {
            const normalized = normalizeIsoDate(rawVal);
            if (this.value !== normalized) {
                this.value = normalized;
            }
            if (this.$refs.input && this.$refs.input.value !== normalized) {
                this.$refs.input.value = normalized;
            }
            this.syncViewToIso(normalized);
        },

        get displayValue() {
            const iso = normalizeIsoDate(this.value);
            if (!iso) return '';
            const [y, m, d] = iso.split('-').map(Number);
            const monthName = this.compact ? MONTHS_SHORT_ID[m - 1] : MONTHS_ID[m - 1];
            return `${d} ${monthName} ${y}`;
        },

        get yearOptions() {
            const currentYear = new Date().getFullYear();
            let minY = this.configuredMinYear ?? (this.min ? Number(this.min.slice(0, 4)) : 1950);
            let maxY = this.configuredMaxYear ?? (this.max ? Number(this.max.slice(0, 4)) : currentYear + 10);
            if (this.viewYear < minY) minY = this.viewYear;
            if (this.viewYear > maxY) maxY = this.viewYear;
            const years = [];
            for (let y = maxY; y >= minY; y--) {
                years.push(y);
            }
            return years;
        },

        get canSelectToday() {
            const today = getTodayIso();
            if (this.min && today < this.min) return false;
            if (this.max && today > this.max) return false;
            return true;
        },

        get calendarDays() {
            const year = Number(this.viewYear);
            const month = Number(this.viewMonth);
            const firstOfMonth = new Date(year, month, 1);
            const startOffset = (firstOfMonth.getDay() + 6) % 7; // Monday = 0, Sunday = 6
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const prevMonthDays = new Date(year, month, 0).getDate();
            const today = getTodayIso();
            const selected = normalizeIsoDate(this.value);
            const cells = [];

            const pushCell = (dObj, isOutside) => {
                const iso = `${dObj.getFullYear()}-${pad2(dObj.getMonth() + 1)}-${pad2(dObj.getDate())}`;
                const isDisabled = Boolean((this.min && iso < this.min) || (this.max && iso > this.max));
                cells.push({
                    iso,
                    day: dObj.getDate(),
                    isOutside,
                    isSunday: dObj.getDay() === 0,
                    isToday: iso === today,
                    isSelected: iso === selected,
                    isDisabled,
                });
            };

            for (let i = startOffset - 1; i >= 0; i--) {
                pushCell(new Date(year, month - 1, prevMonthDays - i), true);
            }
            for (let d = 1; d <= daysInMonth; d++) {
                pushCell(new Date(year, month, d), false);
            }
            let nextDay = 1;
            while (cells.length < 42) {
                pushCell(new Date(year, month + 1, nextDay++), true);
            }

            return cells;
        },

        dayButtonClass(cell) {
            if (cell.isSelected) {
                return 'bg-orange-600 text-white font-bold shadow-xs hover:bg-orange-700 cursor-pointer';
            }
            if (cell.isDisabled) {
                return cell.isSunday
                    ? 'text-rose-300 dark:text-rose-900/70 opacity-50 cursor-not-allowed'
                    : 'text-slate-300 dark:text-slate-700 opacity-50 cursor-not-allowed';
            }

            const todayRing = cell.isToday ? ' ring-1 ring-inset ring-orange-500 font-bold' : '';

            if (cell.isOutside) {
                return (cell.isSunday
                    ? 'text-rose-300 dark:text-rose-800 hover:bg-rose-50/60 dark:hover:bg-rose-950/30'
                    : 'text-slate-400 dark:text-slate-600 hover:bg-slate-100/70 dark:hover:bg-slate-800/60') + todayRing + ' cursor-pointer';
            }

            if (cell.isSunday) {
                return 'text-rose-600 dark:text-rose-400 font-semibold hover:bg-rose-50 dark:hover:bg-rose-950/40' + todayRing + ' cursor-pointer';
            }

            return 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' + todayRing + ' cursor-pointer';
        },

        toggleCalendar() {
            if (this.disabled) return;
            if (this.open) {
                this.closeCalendar();
            } else {
                this.openCalendar();
            }
        },

        openCalendar() {
            if (this.disabled) return;
            const selected = normalizeIsoDate(this.value);
            if (selected) {
                this.syncViewToIso(selected);
            } else {
                const today = getTodayIso();
                const fallback = this.max && today > this.max ? this.max : (this.min && today < this.min ? this.min : today);
                this.syncViewToIso(fallback);
            }
            this.open = true;
            this.$nextTick(() => {
                this.updatePosition();
                this.attachListeners();
            });
        },

        closeCalendar() {
            this.open = false;
            this.detachListeners();
        },

        attachListeners() {
            this.detachListeners();
            const onPointerDown = (e) => {
                const trigger = this.$refs.trigger;
                const popover = this.$refs.popover;
                if (trigger && trigger.contains(e.target)) return;
                if (popover && popover.contains(e.target)) return;
                this.closeCalendar();
            };
            const onKeyDown = (e) => {
                if (e.key === 'Escape') {
                    this.closeCalendar();
                    this.$refs.trigger?.focus();
                }
            };
            const onReposition = () => {
                if (!this.open) return;
                this.updatePosition();
            };

            document.addEventListener('pointerdown', onPointerDown, true);
            document.addEventListener('keydown', onKeyDown, true);
            window.addEventListener('scroll', onReposition, true);
            window.addEventListener('resize', onReposition, { passive: true });

            this._cleanupListeners = () => {
                document.removeEventListener('pointerdown', onPointerDown, true);
                document.removeEventListener('keydown', onKeyDown, true);
                window.removeEventListener('scroll', onReposition, true);
                window.removeEventListener('resize', onReposition);
            };
        },

        detachListeners() {
            if (this._cleanupListeners) {
                this._cleanupListeners();
                this._cleanupListeners = null;
            }
        },

        updatePosition() {
            const trigger = this.$refs.trigger;
            if (!trigger) return;
            const rect = trigger.getBoundingClientRect();
            const popW = Math.min(312, window.innerWidth - 16);
            const popH = this.$refs.popover ? Math.max(this.$refs.popover.offsetHeight, 316) : 336;
            const margin = 8;

            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;

            let top;
            if (spaceBelow >= popH + margin || spaceBelow >= spaceAbove) {
                top = Math.min(rect.bottom + 6, window.innerHeight - popH - margin);
            } else {
                top = Math.max(margin, rect.top - popH - 6);
            }
            top = Math.max(margin, top);

            let left = rect.left;
            if (left + popW > window.innerWidth - margin) {
                left = window.innerWidth - popW - margin;
            }
            left = Math.max(margin, left);

            this.popoverStyle = `top: ${Math.round(top)}px; left: ${Math.round(left)}px; width: ${Math.round(popW)}px;`;
        },

        prevMonth() {
            const d = new Date(Number(this.viewYear), Number(this.viewMonth) - 1, 1);
            this.viewYear = d.getFullYear();
            this.viewMonth = d.getMonth();
        },

        nextMonth() {
            const d = new Date(Number(this.viewYear), Number(this.viewMonth) + 1, 1);
            this.viewYear = d.getFullYear();
            this.viewMonth = d.getMonth();
        },

        commitDate(iso) {
            const normalized = normalizeIsoDate(iso);
            const changed = this.value !== normalized;
            this.value = normalized;
            if (this.$refs.input) {
                this.$refs.input.value = normalized;
            }
            this.syncViewToIso(normalized);
            this.closeCalendar();

            if (changed && this.$refs.input) {
                this.$nextTick(() => {
                    this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }
        },

        selectCell(cell) {
            if (cell.isDisabled) return;
            this.commitDate(cell.iso);
        },

        selectToday() {
            if (!this.canSelectToday) return;
            this.commitDate(getTodayIso());
        },

        clearDate() {
            this.commitDate('');
        },
    };
}
