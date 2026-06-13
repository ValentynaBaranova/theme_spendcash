(function () {
    "use strict";

    const CONFIG = {
        weights: { income: 0.45, liquid: 0.25, horizon: 0.2, quickAccess: 0.1 },
        scoreToPct: [
            { maxScore: 20, pct: 2, risk: "Careful" },
            { maxScore: 40, pct: 5, risk: "Conservative" },
            { maxScore: 60, pct: 10, risk: "Moderate" },
            { maxScore: 80, pct: 15, risk: "Growth" },
            { maxScore: 100, pct: 20, risk: "Aggressive" },
        ],
        incomeLimits: {
            5: { default: 25, max: 100 },
            25: { default: 75, max: 300 },
            50: { default: 200, max: 750 },
            75: { default: 500, max: 2000 },
            90: { default: 1500, max: 5000 },
            100: { default: 3000, max: 10000 },
        },
        overrides: [
            { check: (v) => v.liquid === 10, cap: 5 },
            { check: (v) => v.horizon === 0, cap: 3 },
            { check: (v) => v.quickAccess === 0, cap: 8 },
            { check: (v) => v.objective === "speculation", cap: 10 },
            { check: (v) => v.objective === "preserve", cap: 8 },
            {
                check: (v) => v.objective === "retirement" && (v.horizon === 0 || v.horizon === 20),
                cap: 5,
            },
        ],
        riskStyle: [
            { maxPct: 3, label: "Careful" },
            { maxPct: 5, label: "Conservative" },
            { maxPct: 10, label: "Moderate" },
            { maxPct: 15, label: "Growth" },
            { maxPct: 25, label: "Aggressive" },
        ],
    };

    const fmt = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        maximumFractionDigits: 0,
    });

    function parseNum(val, fallback) {
        const n = Number(val);
        return Number.isFinite(n) ? n : fallback;
    }

    function calcScore(values) {
        return (
            values.income * CONFIG.weights.income +
            values.liquid * CONFIG.weights.liquid +
            values.horizon * CONFIG.weights.horizon +
            values.quickAccess * CONFIG.weights.quickAccess
        );
    }

    function scoreToRecommendedPct(score) {
        for (let i = 0; i < CONFIG.scoreToPct.length; i++) {
            if (score <= CONFIG.scoreToPct[i].maxScore) {
                return CONFIG.scoreToPct[i].pct;
            }
        }
        return CONFIG.scoreToPct[CONFIG.scoreToPct.length - 1].pct;
    }

    function applyOverrides(pct, values) {
        let result = pct;
        CONFIG.overrides.forEach((rule) => {
            if (rule.check(values)) {
                result = Math.min(result, rule.cap);
            }
        });
        return result;
    }

    function getWeeklyLimit(incomePoints) {
        return CONFIG.incomeLimits[incomePoints] || CONFIG.incomeLimits[5];
    }

    function getRiskStyle(pct) {
        for (let i = 0; i < CONFIG.riskStyle.length; i++) {
            if (pct <= CONFIG.riskStyle[i].maxPct) {
                return CONFIG.riskStyle[i].label;
            }
        }
        return CONFIG.riskStyle[CONFIG.riskStyle.length - 1].label;
    }

    function weeklyRate(annualRate) {
        return Math.pow(1 + annualRate / 100, 1 / 52) - 1;
    }

    function futureValueWeekly(weeklyPmt, weeks, annualRate) {
        if (weeks <= 0) return 0;
        const r = weeklyRate(annualRate);
        if (r === 0) return weeklyPmt * weeks;
        return weeklyPmt * ((Math.pow(1 + r, weeks) - 1) / r);
    }

    function getYearBreakdown(weeklyPmt, years, annualRate) {
        const rows = [];
        let maxTotal = 0;

        for (let y = 1; y <= years; y++) {
            const weeks = y * 52;
            const total = futureValueWeekly(weeklyPmt, weeks, annualRate);
            const invested = weeklyPmt * weeks;
            const growth = Math.max(0, total - invested);
            maxTotal = Math.max(maxTotal, total);
            rows.push({ year: y, total, invested, growth });
        }

        return { rows, maxTotal };
    }

    function readValues(root) {
        return {
            income: parseNum(root.querySelector('[data-calc-input="income"]')?.value, 5),
            liquid: parseNum(root.querySelector('[data-calc-input="liquid"]')?.value, 35),
            horizon: parseNum(root.querySelector('[data-calc-input="horizon"]')?.value, 50),
            quickAccess: parseNum(root.querySelector('[data-calc-input="quickAccess"]')?.value, 0),
            objective: root.querySelector('[data-calc-input="objective"]')?.value || "preserve",
            years: parseNum(root.querySelector('[data-calc-input="years"]')?.value, 10),
            annualReturn: parseNum(root.querySelector('[data-calc-input="annualReturn"]')?.value, 8),
            weeklySpending: parseNum(root.querySelector('[data-calc-input="weeklySpending"]')?.value, 1500),
        };
    }

    function renderChart(container, rows, maxTotal) {
        if (!container) return;
        container.innerHTML = "";

        if (!rows.length || maxTotal <= 0) return;

        rows.forEach((row) => {
            const bar = document.createElement("div");
            bar.className = "calculator__bar";
            bar.setAttribute("role", "img");
            bar.setAttribute("aria-label", "Year " + row.year);

            const totalPct = (row.total / maxTotal) * 100;
            const investedPct = (row.invested / maxTotal) * 100;
            const growthPct = Math.max(0, totalPct - investedPct);

            const growth = document.createElement("div");
            growth.className = "calculator__bar-segment calculator__bar-segment--growth";
            growth.style.height = growthPct > 0 ? growthPct + "%" : "0";

            const invested = document.createElement("div");
            invested.className = "calculator__bar-segment calculator__bar-segment--invested";
            invested.style.height = investedPct + "%";

            bar.appendChild(growth);
            bar.appendChild(invested);
            container.appendChild(bar);
        });
    }

    function update(root) {
        const values = readValues(root);
        const score = calcScore(values);
        let recommendedPct = scoreToRecommendedPct(score);
        recommendedPct = applyOverrides(recommendedPct, values);

        const limits = getWeeklyLimit(values.income);
        const weeklyContribution = Math.min(
            values.weeklySpending * (recommendedPct / 100),
            limits.default
        );
        const riskStyle = getRiskStyle(recommendedPct);

        const weeks = values.years * 52;
        const futureValue = futureValueWeekly(weeklyContribution, weeks, values.annualReturn);
        const totalInvested = weeklyContribution * weeks;
        const estimatedGrowth = Math.max(0, futureValue - totalInvested);

        const { rows, maxTotal } = getYearBreakdown(weeklyContribution, values.years, values.annualReturn);

        const setText = (sel, text) => {
            const el = root.querySelector(sel);
            if (el) el.textContent = text;
        };

        setText("[data-calc-future-value]", fmt.format(Math.round(futureValue)));
        setText("[data-calc-rec-pct]", recommendedPct + "%");
        setText("[data-calc-stat-pct]", recommendedPct + "%");
        setText("[data-calc-stat-limit]", fmt.format(limits.default));
        setText("[data-calc-stat-risk]", riskStyle);
        setText("[data-calc-invested]", fmt.format(Math.round(totalInvested)));
        setText("[data-calc-growth]", fmt.format(Math.round(estimatedGrowth)));

        setText(
            "[data-calc-summary]",
            "Based on estimated card spending " +
                fmt.format(values.weeklySpending) +
                "/week: investing " +
                fmt.format(Math.round(weeklyContribution)) +
                "/week (" +
                recommendedPct +
                "% capped by " +
                fmt.format(limits.default) +
                " weekly limit) for " +
                values.years +
                " years at " +
                values.annualReturn +
                "% annual return."
        );

        const ctaPct = root.querySelector("[data-calc-cta-pct]");
        if (ctaPct) ctaPct.textContent = recommendedPct + "%";

        const yearsBadge = root.querySelector('[data-calc-badge="years"]');
        if (yearsBadge) yearsBadge.textContent = values.years + " yrs";

        const returnBadge = root.querySelector('[data-calc-badge="annualReturn"]');
        if (returnBadge) returnBadge.textContent = values.annualReturn + "%";

        renderChart(root.querySelector("[data-calc-chart]"), rows, maxTotal);
    }

    function bindInputs(root) {
        root.querySelectorAll("[data-calc-input]").forEach((input) => {
            const eventName = input.type === "range" || input.type === "number" ? "input" : "change";
            input.addEventListener(eventName, () => update(root));
        });
    }

    window.initSpendvestCalculator = function () {
        const root = document.getElementById("calculator");
        if (!root) return;

        bindInputs(root);
        update(root);
    };

    function onReady(fn) {
        if (document.readyState !== "loading") fn();
        else document.addEventListener("DOMContentLoaded", fn);
    }

    onReady(function () {
        window.initSpendvestCalculator();
    });
})();
