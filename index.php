<?php
// index.php — Electricity Cost Calculator (Multi-device) | PHP + jQuery + Bootstrap 5
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Electricity Cost Calculator — Multi-Device</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    :root{
      --bg: #f8fafc;
      --card: #ffffff;
      --ink: #0f172a;
      --muted: #64748b;
      --accent: #0ea5e9;
      --ring: rgba(14,165,233,.35);
    }
    body{
      background: radial-gradient(1200px 600px at 10% -20%, #eef6ff 0%, transparent 60%),
                  radial-gradient(1200px 600px at 110% 120%, #f0fff4 0%, transparent 60%),
                  var(--bg);
      color: var(--ink);
      min-height: 100vh;
    }
    .app-container{ max-width: 1100px; }
    .glass{
      background: var(--card);
      border: 1px solid rgba(15,23,42,.06);
      box-shadow: 0 10px 30px rgba(2,6,23,.05), 0 2px 6px rgba(2,6,23,.06);
      border-radius: 18px;
    }
    .card-head{
      border-bottom: 1px solid rgba(15,23,42,.06);
      background: linear-gradient(180deg, rgba(14,165,233,.06), transparent);
      border-top-left-radius: 18px;
      border-top-right-radius: 18px;
    }
    .form-control:focus, .form-select:focus{
      border-color: var(--accent);
      box-shadow: 0 0 0 .25rem var(--ring);
    }
    .subtle{ color: var(--muted); }
    .badge-soft{
      background: rgba(14,165,233,.12);
      color: #0369a1;
      border: 1px solid rgba(14,165,233,.2);
    }
    .stat{
      border-radius: 14px;
      border: 1px solid rgba(15,23,42,.06);
      padding: 1rem 1.25rem;
      background: linear-gradient(180deg, #fff, #fbfdff 60%);
    }
    .stat h6{
      font-weight: 600;
      letter-spacing: .02em;
      color: var(--muted);
      margin-bottom: .35rem;
      text-transform: uppercase;
      font-size: .8rem;
    }
    .value{ font-variant-numeric: tabular-nums; font-weight: 700; }
    table th{ font-weight: 600; color: #0b2447; }
    table td, table th{ vertical-align: middle; }
    .table thead th{ background: #f2f7ff; }
    .row-actions .btn{ padding: .35rem .6rem; }
    .totals-row th{ background: #f7fbff; }
    .kwh-note{ font-size: .9rem; color: var(--muted); }
  </style>
</head>
<body>
  <div class="container py-5 app-container">
    <div class="glass">
      <div class="card-head p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <h1 class="h3 mb-1">Electricity Cost Calculator</h1>
          <div class="subtle">Add multiple devices, set hours/day, and apply your utility rate to see running totals.</div>
        </div>

        <div class="d-flex align-items-end gap-3">
          <div>
            <label for="rate" class="form-label mb-1">Cost per kWh</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" step="any" min="0" class="form-control" id="rate" placeholder="e.g., 0.15">
            </div>
            <div class="form-text">Shared rate for all devices.</div>
          </div>
          <span class="badge badge-soft rounded-pill px-3 py-2 align-self-start">Light Theme</span>
        </div>
      </div>

      <div class="p-4 p-md-5">
        <!-- Controls -->
        <div class="d-flex flex-wrap gap-2 mb-3">
          <button type="button" id="addRow" class="btn btn-primary">
            + Add Device
          </button>
          <button type="button" id="clearAll" class="btn btn-outline-secondary">
            Clear All
          </button>
        </div>

        <!-- Devices Table -->
        <div class="table-responsive">
          <table class="table table-bordered align-middle" id="devicesTable">
            <thead class="table-light">
              <tr>
                <th style="width: 26%">Device (optional)</th>
                <th style="width: 22%">Power</th>
                <th style="width: 16%">Hours / Day</th>
                <th style="width: 12%">kWh / Day</th>
                <th style="width: 12%">Cost / Day</th>
                <th style="width: 12%">Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- rows injected here -->
            </tbody>
            <tfoot>
              <tr class="totals-row">
                <th colspan="3" class="text-end">Totals</th>
                <th id="totalKwhDay" class="text-end">0.000</th>
                <th id="totalCostDay" class="text-end">$0.00</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Rollup Cards -->
        <div class="row g-3 mt-4">
          <div class="col-sm-6 col-lg-3">
            <div class="stat h-100">
              <h6>Per Day</h6>
              <div class="value fs-5" id="costDay">$0.00</div>
              <div class="kwh-note" id="kwhDay">0.000 kWh</div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="stat h-100">
              <h6>Per Week</h6>
              <div class="value fs-5" id="costWeek">$0.00</div>
              <div class="kwh-note" id="kwhWeek">0.000 kWh</div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="stat h-100">
              <h6>Per Month*</h6>
              <div class="value fs-5" id="costMonth">$0.00</div>
              <div class="kwh-note" id="kwhMonth">0.000 kWh</div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="stat h-100">
              <h6>Annually</h6>
              <div class="value fs-5" id="costYear">$0.00</div>
              <div class="kwh-note" id="kwhYear">0.000 kWh</div>
            </div>
          </div>
        </div>

        <p class="mt-3 subtle mb-0">
          *Month uses an average of <strong>30.4375</strong> days (365/12) for a realistic estimate.
        </p>
      </div>
    </div>

    <div class="text-center mt-4 subtle">
      Built with PHP, jQuery, and Bootstrap 5
    </div>
  </div>

  <script>
    // Formatting helpers
    const fmtMoney = (n) =>
      isFinite(n) ? new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD', maximumFractionDigits: 2 }).format(n) : "$0.00";
    const fmtKwh = (n) =>
      isFinite(n) ? new Intl.NumberFormat(undefined, { minimumFractionDigits: 3, maximumFractionDigits: 3 }).format(n) : "0.000";

    const DAYS_PER_WEEK = 7;
    const DAYS_PER_MONTH = 365/12;
    const DAYS_PER_YEAR = 365;

    // Row template
    function makeRow(id) {
      return `
        <tr data-row-id="${id}">
          <td>
            <input type="text" class="form-control device-name" placeholder="e.g., Space Heater">
          </td>
          <td>
            <div class="input-group">
              <input type="number" step="any" min="0" class="form-control power" placeholder="e.g., 1500">
              <select class="form-select unit" style="max-width: 7.5rem;">
                <option value="W" selected>Watts (W)</option>
                <option value="kW">Kilowatts (kW)</option>
              </select>
            </div>
            <div class="form-text">Choose W or kW.</div>
          </td>
          <td>
            <input type="number" step="any" min="0" class="form-control hours" placeholder="e.g., 3.5">
          </td>
          <td class="text-end kwh-day">0.000</td>
          <td class="text-end cost-day">$0.00</td>
          <td class="row-actions text-center">
            <button type="button" class="btn btn-outline-danger btn-sm remove-row">Remove</button>
          </td>
        </tr>`;
    }

    let rowId = 0;

    function addRow(prefill = {}) {
      const id = ++rowId;
      $("#devicesTable tbody").append(makeRow(id));
      const $row = $(`#devicesTable tbody tr[data-row-id="${id}"]`);
      if (prefill.name)  $row.find(".device-name").val(prefill.name);
      if (prefill.power) $row.find(".power").val(prefill.power);
      if (prefill.unit)  $row.find(".unit").val(prefill.unit);
      if (prefill.hours) $row.find(".hours").val(prefill.hours);
      recalcAll();
    }

    function getRate() {
      const r = parseFloat($("#rate").val());
      return isNaN(r) || r < 0 ? 0 : r;
    }

    function recalcRow($row, rate) {
      const power = parseFloat($row.find(".power").val());
      const unit  = $row.find(".unit").val();
      const hours = parseFloat($row.find(".hours").val());

      if (isNaN(power) || power < 0 || isNaN(hours) || hours < 0) {
        $row.find(".kwh-day").text("0.000");
        $row.find(".cost-day").text("$0.00");
        return { kwhDay: 0, costDay: 0 };
      }

      const kW = (unit === "W") ? (power / 1000) : power;
      const kwhDay = kW * hours;
      const costDay = kwhDay * rate;

      $row.find(".kwh-day").text(fmtKwh(kwhDay));
      $row.find(".cost-day").text(fmtMoney(costDay));
      return { kwhDay, costDay };
    }

    function recalcAll() {
      const rate = getRate();
      let totalKwhDay = 0;
      let totalCostDay = 0;

      $("#devicesTable tbody tr").each(function(){
        const r = recalcRow($(this), rate);
        totalKwhDay += r.kwhDay;
        totalCostDay += r.costDay;
      });

      // Update totals in table footer
      $("#totalKwhDay").text(fmtKwh(totalKwhDay));
      $("#totalCostDay").text(fmtMoney(totalCostDay));

      // Rollups
      const kwhWeek  = totalKwhDay * DAYS_PER_WEEK;
      const kwhMonth = totalKwhDay * DAYS_PER_MONTH;
      const kwhYear  = totalKwhDay * DAYS_PER_YEAR;

      const costWeek  = totalCostDay * DAYS_PER_WEEK;
      const costMonth = totalCostDay * DAYS_PER_MONTH;
      const costYear  = totalCostDay * DAYS_PER_YEAR;

      $("#kwhDay").text(fmtKwh(totalKwhDay) + " kWh");
      $("#kwhWeek").text(fmtKwh(kwhWeek) + " kWh");
      $("#kwhMonth").text(fmtKwh(kwhMonth) + " kWh");
      $("#kwhYear").text(fmtKwh(kwhYear) + " kWh");

      $("#costDay").text(fmtMoney(totalCostDay));
      $("#costWeek").text(fmtMoney(costWeek));
      $("#costMonth").text(fmtMoney(costMonth));
      $("#costYear").text(fmtMoney(costYear));
    }

    $(function(){
      // Seed a couple of sample rows for convenience
      addRow({ name: "Space Heater", power: 1500, unit: "W", hours: 3 });
      addRow({ name: "LED Bulb", power: 9, unit: "W", hours: 6 });
      $("#rate").val("0.15");

      // Add / Clear
      $("#addRow").on("click", function(){ addRow(); });
      $("#clearAll").on("click", function(){
        $("#devicesTable tbody").empty();
        recalcAll();
      });

      // Delegate input events for dynamic rows
      $("#devicesTable").on("input change", ".power, .unit, .hours", function(){
        recalcAll();
      });

      // Remove row
      $("#devicesTable").on("click", ".remove-row", function(){
        $(this).closest("tr").remove();
        recalcAll();
      });

      // Rate change
      $("#rate").on("input change", function(){ recalcAll(); });

      // First calc
      recalcAll();
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
