(function () {
    "use strict";

    let idx = wcCuotasData.count;
    const tarjetas = wcCuotasData.tarjetas;

    function buildRow(i, plan) {
        plan = plan || {};
        const label      = plan.label      || "";
        const descuento  = plan.descuento  !== undefined ? plan.descuento  : 0;
        const cuotas     = plan.cuotas     !== undefined ? plan.cuotas     : 1;
        const sinInteres = !!plan.sin_interes;
        const selTarj    = plan.tarjetas   || [];

        const tarjetasHtml = Object.entries(tarjetas)
            .map(([key, t]) => {
                const checked = selTarj.includes(key) ? "checked" : "";
                return (
                    `<label class="wc-card-check">` +
                    `<input type="checkbox" name="planes[${i}][tarjetas][]" value="${key}" ${checked}>` +
                    `<span class="wc-card-preview" style="background:${t.color};color:${t.text}">${t.label}</span>` +
                    `</label>`
                );
            })
            .join("");

        return (
            `<div class="wc-plan-box" data-index="${i}">` +
            `<div class="wc-plan-header">` +
            `<span class="wc-plan-title">Plan #<span class="wc-plan-num">${i + 1}</span></span>` +
            `<button type="button" class="button button-small wc-remove-plan">✕ Eliminar</button>` +
            `</div>` +
            `<div class="wc-plan-grid">` +
            `<div class="wc-field"><label>Etiqueta</label>` +
            `<input type="text" name="planes[${i}][label]" value="${escHtml(label)}" class="regular-text" placeholder="Ej: 30% Off en 1 Pago"></div>` +
            `<div class="wc-field"><label>Descuento %</label>` +
            `<input type="number" name="planes[${i}][descuento]" value="${descuento}" min="0" max="100" step="0.01" class="small-text"></div>` +
            `<div class="wc-field"><label>Cuotas</label>` +
            `<input type="number" name="planes[${i}][cuotas]" value="${cuotas}" min="1" max="60" class="small-text"></div>` +
            `<div class="wc-field wc-field-check"><label>` +
            `<input type="checkbox" name="planes[${i}][sin_interes]" value="1" ${sinInteres ? "checked" : ""}> Sin interés` +
            `</label></div>` +
            `</div>` +
            `<div class="wc-tarjetas-row"><span class="wc-tarjetas-label">Tarjetas:</span>${tarjetasHtml}</div>` +
            `</div>`
        );
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/"/g, "&quot;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    }

    function renumberAll() {
        document
            .querySelectorAll("#wc-planes-container .wc-plan-num")
            .forEach((el, i) => { el.textContent = i + 1; });
    }

    // Agregar plan
    document.getElementById("wc-add-plan").addEventListener("click", function () {
        const container = document.getElementById("wc-planes-container");
        container.insertAdjacentHTML("beforeend", buildRow(idx++));
        renumberAll();
    });

    // Eliminar plan (delegado)
    document.getElementById("wc-planes-container").addEventListener("click", function (e) {
        if (e.target.classList.contains("wc-remove-plan")) {
            if (!confirm("¿Eliminar este plan?")) return;
            e.target.closest(".wc-plan-box").remove();
            renumberAll();
        }
    });
})();
