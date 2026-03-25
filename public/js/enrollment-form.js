/**
 * DMF Dental Training Center — Enrollment Form JS
 * enrollment-form.js
 *
 * Handles: "same address" copy logic, program card selection, and payment type reveal.
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ── Copy address ──────────────────────────────────────── */
    const sameBox = document.getElementById('same_address');
    const fields = ['street', 'city', 'province', 'zip'];
    const mainInputs  = fields.map(f => document.querySelector(`input[name="addr_${f}"]`));
    const delivInputs = fields.map(f => document.querySelector(`input[name="deliv_${f}"]`));

    if (sameBox) {
        sameBox.addEventListener('change', function () {
            if (this.checked) {
                delivInputs.forEach((input, i) => {
                    input.value    = mainInputs[i].value;
                    input.readOnly = true;
                    input.classList.add('bg-gray-50');
                });
            } else {
                delivInputs.forEach(input => {
                    input.readOnly = false;
                    input.classList.remove('bg-gray-50');
                });
            }
        });

        mainInputs.forEach((mainInput, i) => {
            mainInput.addEventListener('input', function () {
                if (sameBox.checked) delivInputs[i].value = this.value;
            });
        });
    }

    /* ── Program card selection ───────────────────────────── */
    const radios         = document.querySelectorAll('.program-opt input[type="radio"]');
    const paymentSection = document.getElementById('payment-options-section');
    const lblFull        = document.getElementById('lbl-full-price');
    const lblDp          = document.getElementById('lbl-dp-price');
    const scheduleSection = document.getElementById('schedule-section');
    const scheduleSelect  = document.getElementById('schedule_id');

    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            // Refresh check indicators on all cards
            document.querySelectorAll('.program-opt').forEach(card => {
                const r = card.querySelector('input');
                const iconEl  = card.querySelector('.program-check-icon');
                const ringEl  = card.querySelector('.program-check');
                if (r.checked) {
                    iconEl.style.opacity         = '1';
                    ringEl.style.borderColor      = '#4c6ebb';
                    ringEl.style.backgroundColor  = '#e5ebf8';
                } else {
                    iconEl.style.opacity         = '0';
                    ringEl.style.borderColor      = '#e2e8f0';
                    ringEl.style.backgroundColor  = 'transparent';
                }
            });

            // Show payment section and update price labels
            if (this.checked && paymentSection) {
                paymentSection.style.display = 'block';
                const fullP = parseInt(this.getAttribute('data-full'));
                const dpP   = parseInt(this.getAttribute('data-dp'));
                if (lblFull) lblFull.innerText = '₱' + fullP.toLocaleString();
                if (lblDp)   lblDp.innerText   = '₱' + dpP.toLocaleString();
            }

            // Batch dropdown (only when applicable)
            if (scheduleSection && scheduleSelect) {
                const raw = this.getAttribute('data-schedules') || '[]';
                let schedules = [];
                try {
                    schedules = JSON.parse(raw);
                } catch (e) {
                    schedules = [];
                }

                scheduleSelect.innerHTML = '<option value=\"\">Select a batch</option>';

                if (Array.isArray(schedules) && schedules.length > 0) {
                    schedules.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = String(s.id);
                        const suffix = s.mode ? ` (${s.mode})` : '';
                        opt.textContent = `${s.label}${suffix}`;
                        scheduleSelect.appendChild(opt);
                    });

                    const oldScheduleId = scheduleSelect.getAttribute('data-old');
                    if (oldScheduleId) {
                        scheduleSelect.value = oldScheduleId;
                    }

                    if (schedules.length === 1) {
                        scheduleSelect.value = String(schedules[0].id);
                        scheduleSection.style.display = 'none';
                    } else {
                        scheduleSection.style.display = 'block';
                    }
                } else {
                    scheduleSection.style.display = 'none';
                    scheduleSelect.value = '';
                }
            }
        });
    });

    // Trigger change on page load for any pre-selected program (e.g. old input restore)
    document.querySelectorAll('.program-opt input[type="radio"]:checked')
        .forEach(r => r.dispatchEvent(new Event('change')));
});
