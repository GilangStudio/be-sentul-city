<!-- BEGIN PAGE LIBRARIES -->
<script src="{{ asset('libs/apexcharts/dist/apexcharts.min.js') }}" defer></script>
<script src="{{ asset('libs/jsvectormap/dist/jsvectormap.min.js') }}" defer></script>
<script src="{{ asset('libs/jsvectormap/dist/maps/world.js') }}" defer></script>
<script src="{{ asset('libs/jsvectormap/dist/maps/world-merc.js') }}" defer></script>
<script src="{{ asset('libs/list.js/dist/list.min.js') }}" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<!-- END PAGE LIBRARIES -->
<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
{{-- <script src="{{ asset('js/tabler.min.js') }}" defer></script> --}}
<!-- END GLOBAL MANDATORY SCRIPTS -->


<!-- BEGIN PAGE SCRIPTS -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("chart-visitors"), {
        chart: {
          type: "line",
          fontFamily: "inherit",
          height: 96,
          sparkline: {
            enabled: true,
          },
          animations: {
            enabled: false,
          },
        },
        stroke: {
          width: [2, 1],
          dashArray: [0, 3],
          lineCap: "round",
          curve: "smooth",
        },
        series: [
          {
            name: "Visitors",
            data: [
              7687, 7543, 7545, 7543, 7635, 8140, 7810, 8315, 8379, 8441, 8485, 8227, 8906, 8561, 8333, 8551, 9305, 9647, 9359, 9840, 9805, 8612, 8970,
              8097, 8070, 9829, 10545, 10754, 10270, 9282,
            ],
          },
          {
            name: "Visitors last month",
            data: [
              8630, 9389, 8427, 9669, 8736, 8261, 8037, 8922, 9758, 8592, 8976, 9459, 8125, 8528, 8027, 8256, 8670, 9384, 9813, 8425, 8162, 8024, 8897,
              9284, 8972, 8776, 8121, 9476, 8281, 9065,
            ],
          },
        ],
        tooltip: {
          theme: "dark",
        },
        grid: {
          strokeDashArray: 4,
        },
        xaxis: {
          labels: {
            padding: 0,
          },
          tooltip: {
            enabled: false,
          },
          type: "datetime",
        },
        yaxis: {
          labels: {
            padding: 4,
          },
        },
        labels: [
          "2020-06-20",
          "2020-06-21",
          "2020-06-22",
          "2020-06-23",
          "2020-06-24",
          "2020-06-25",
          "2020-06-26",
          "2020-06-27",
          "2020-06-28",
          "2020-06-29",
          "2020-06-30",
          "2020-07-01",
          "2020-07-02",
          "2020-07-03",
          "2020-07-04",
          "2020-07-05",
          "2020-07-06",
          "2020-07-07",
          "2020-07-08",
          "2020-07-09",
          "2020-07-10",
          "2020-07-11",
          "2020-07-12",
          "2020-07-13",
          "2020-07-14",
          "2020-07-15",
          "2020-07-16",
          "2020-07-17",
          "2020-07-18",
          "2020-07-19",
        ],
        colors: ["color-mix(in srgb, transparent, var(--tblr-primary) 100%)", "color-mix(in srgb, transparent, var(--tblr-gray-400) 100%)"],
        legend: {
          show: false,
        },
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("chart-active-users-3"), {
        chart: {
          type: "radialBar",
          fontFamily: "inherit",
          height: 192,
          sparkline: {
            enabled: true,
          },
          animations: {
            enabled: false,
          },
        },
        plotOptions: {
          radialBar: {
            startAngle: -120,
            endAngle: 120,
            hollow: {
              margin: 16,
              size: "50%",
            },
            dataLabels: {
              show: true,
              value: {
                offsetY: -8,
                fontSize: "24px",
              },
            },
          },
        },
        series: [78],
        labels: [""],
        tooltip: {
          theme: "dark",
        },
        grid: {
          strokeDashArray: 4,
        },
        colors: ["color-mix(in srgb, transparent, var(--tblr-primary) 100%)"],
        legend: {
          show: false,
        },
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("chart-revenue-bg"), {
        chart: {
          type: "area",
          fontFamily: "inherit",
          height: 40,
          sparkline: {
            enabled: true,
          },
          animations: {
            enabled: false,
          },
        },
        dataLabels: {
          enabled: false,
        },
        fill: {
          colors: ["color-mix(in srgb, transparent, var(--tblr-primary) 16%)", "color-mix(in srgb, transparent, var(--tblr-primary) 16%)"],
          type: "solid",
        },
        stroke: {
          width: 2,
          lineCap: "round",
          curve: "smooth",
        },
        series: [
          {
            name: "Profits",
            data: [37, 35, 44, 28, 36, 24, 65, 31, 37, 39, 62, 51, 35, 41, 35, 27, 93, 53, 61, 27, 54, 43, 19, 46, 39, 62, 51, 35, 41, 67],
          },
        ],
        tooltip: {
          theme: "dark",
        },
        grid: {
          strokeDashArray: 4,
        },
        xaxis: {
          labels: {
            padding: 0,
          },
          tooltip: {
            enabled: false,
          },
          axisBorder: {
            show: false,
          },
          type: "datetime",
        },
        yaxis: {
          labels: {
            padding: 4,
          },
        },
        labels: [
          "2020-06-20",
          "2020-06-21",
          "2020-06-22",
          "2020-06-23",
          "2020-06-24",
          "2020-06-25",
          "2020-06-26",
          "2020-06-27",
          "2020-06-28",
          "2020-06-29",
          "2020-06-30",
          "2020-07-01",
          "2020-07-02",
          "2020-07-03",
          "2020-07-04",
          "2020-07-05",
          "2020-07-06",
          "2020-07-07",
          "2020-07-08",
          "2020-07-09",
          "2020-07-10",
          "2020-07-11",
          "2020-07-12",
          "2020-07-13",
          "2020-07-14",
          "2020-07-15",
          "2020-07-16",
          "2020-07-17",
          "2020-07-18",
          "2020-07-19",
        ],
        colors: ["color-mix(in srgb, transparent, var(--tblr-primary) 100%)"],
        legend: {
          show: false,
        },
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("chart-new-clients"), {
        chart: {
          type: "line",
          fontFamily: "inherit",
          height: 40,
          sparkline: {
            enabled: true,
          },
          animations: {
            enabled: false,
          },
        },
        stroke: {
          width: [2, 1],
          dashArray: [0, 3],
          lineCap: "round",
          curve: "smooth",
        },
        series: [
          {
            name: "May",
            data: [37, 35, 44, 28, 36, 24, 65, 31, 37, 39, 62, 51, 35, 41, 35, 27, 93, 53, 61, 27, 54, 43, 4, 46, 39, 62, 51, 35, 41, 67],
          },
          {
            name: "April",
            data: [93, 54, 51, 24, 35, 35, 31, 67, 19, 43, 28, 36, 62, 61, 27, 39, 35, 41, 27, 35, 51, 46, 62, 37, 44, 53, 41, 65, 39, 37],
          },
        ],
        tooltip: {
          theme: "dark",
        },
        grid: {
          strokeDashArray: 4,
        },
        xaxis: {
          labels: {
            padding: 0,
          },
          tooltip: {
            enabled: false,
          },
          type: "datetime",
        },
        yaxis: {
          labels: {
            padding: 4,
          },
        },
        labels: [
          "2020-06-20",
          "2020-06-21",
          "2020-06-22",
          "2020-06-23",
          "2020-06-24",
          "2020-06-25",
          "2020-06-26",
          "2020-06-27",
          "2020-06-28",
          "2020-06-29",
          "2020-06-30",
          "2020-07-01",
          "2020-07-02",
          "2020-07-03",
          "2020-07-04",
          "2020-07-05",
          "2020-07-06",
          "2020-07-07",
          "2020-07-08",
          "2020-07-09",
          "2020-07-10",
          "2020-07-11",
          "2020-07-12",
          "2020-07-13",
          "2020-07-14",
          "2020-07-15",
          "2020-07-16",
          "2020-07-17",
          "2020-07-18",
          "2020-07-19",
        ],
        colors: ["color-mix(in srgb, transparent, var(--tblr-primary) 100%)", "color-mix(in srgb, transparent, var(--tblr-gray-600) 100%)"],
        legend: {
          show: false,
        },
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("chart-active-users"), {
        chart: {
          type: "bar",
          fontFamily: "inherit",
          height: 40,
          sparkline: {
            enabled: true,
          },
          animations: {
            enabled: false,
          },
        },
        plotOptions: {
          bar: {
            columnWidth: "50%",
          },
        },
        dataLabels: {
          enabled: false,
        },
        series: [
          {
            name: "Profits",
            data: [37, 35, 44, 28, 36, 24, 65, 31, 37, 39, 62, 51, 35, 41, 35, 27, 93, 53, 61, 27, 54, 43, 19, 46, 39, 62, 51, 35, 41, 67],
          },
        ],
        tooltip: {
          theme: "dark",
        },
        grid: {
          strokeDashArray: 4,
        },
        xaxis: {
          labels: {
            padding: 0,
          },
          tooltip: {
            enabled: false,
          },
          axisBorder: {
            show: false,
          },
          type: "datetime",
        },
        yaxis: {
          labels: {
            padding: 4,
          },
        },
        labels: [
          "2020-06-20",
          "2020-06-21",
          "2020-06-22",
          "2020-06-23",
          "2020-06-24",
          "2020-06-25",
          "2020-06-26",
          "2020-06-27",
          "2020-06-28",
          "2020-06-29",
          "2020-06-30",
          "2020-07-01",
          "2020-07-02",
          "2020-07-03",
          "2020-07-04",
          "2020-07-05",
          "2020-07-06",
          "2020-07-07",
          "2020-07-08",
          "2020-07-09",
          "2020-07-10",
          "2020-07-11",
          "2020-07-12",
          "2020-07-13",
          "2020-07-14",
          "2020-07-15",
          "2020-07-16",
          "2020-07-17",
          "2020-07-18",
          "2020-07-19",
        ],
        colors: ["color-mix(in srgb, transparent, var(--tblr-primary) 100%)"],
        legend: {
          show: false,
        },
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("chart-mentions"), {
        chart: {
          type: "bar",
          fontFamily: "inherit",
          height: 240,
          parentHeightOffset: 0,
          toolbar: {
            show: false,
          },
          animations: {
            enabled: false,
          },
          stacked: true,
        },
        plotOptions: {
          bar: {
            columnWidth: "50%",
          },
        },
        dataLabels: {
          enabled: false,
        },
        series: [
          {
            name: "Web",
            data: [1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 2, 12, 5, 8, 22, 6, 8, 6, 4, 1, 8, 24, 29, 51, 40, 47, 23, 26, 50, 26, 41, 22, 46, 47, 81, 46, 6],
          },
          {
            name: "Social",
            data: [2, 5, 4, 3, 3, 1, 4, 7, 5, 1, 2, 5, 3, 2, 6, 7, 7, 1, 5, 5, 2, 12, 4, 6, 18, 3, 5, 2, 13, 15, 20, 47, 18, 15, 11, 10, 0],
          },
          {
            name: "Other",
            data: [2, 9, 1, 7, 8, 3, 6, 5, 5, 4, 6, 4, 1, 9, 3, 6, 7, 5, 2, 8, 4, 9, 1, 2, 6, 7, 5, 1, 8, 3, 2, 3, 4, 9, 7, 1, 6],
          },
        ],
        tooltip: {
          theme: "dark",
        },
        grid: {
          padding: {
            top: -20,
            right: 0,
            left: -4,
            bottom: -4,
          },
          strokeDashArray: 4,
          xaxis: {
            lines: {
              show: true,
            },
          },
        },
        xaxis: {
          labels: {
            padding: 0,
          },
          tooltip: {
            enabled: false,
          },
          axisBorder: {
            show: false,
          },
          type: "datetime",
        },
        yaxis: {
          labels: {
            padding: 4,
          },
        },
        labels: [
          "2020-06-20",
          "2020-06-21",
          "2020-06-22",
          "2020-06-23",
          "2020-06-24",
          "2020-06-25",
          "2020-06-26",
          "2020-06-27",
          "2020-06-28",
          "2020-06-29",
          "2020-06-30",
          "2020-07-01",
          "2020-07-02",
          "2020-07-03",
          "2020-07-04",
          "2020-07-05",
          "2020-07-06",
          "2020-07-07",
          "2020-07-08",
          "2020-07-09",
          "2020-07-10",
          "2020-07-11",
          "2020-07-12",
          "2020-07-13",
          "2020-07-14",
          "2020-07-15",
          "2020-07-16",
          "2020-07-17",
          "2020-07-18",
          "2020-07-19",
          "2020-07-20",
          "2020-07-21",
          "2020-07-22",
          "2020-07-23",
          "2020-07-24",
          "2020-07-25",
          "2020-07-26",
        ],
        colors: [
          "color-mix(in srgb, transparent, var(--tblr-primary) 100%)",
          "color-mix(in srgb, transparent, var(--tblr-primary) 80%)",
          "color-mix(in srgb, transparent, var(--tblr-green) 80%)",
        ],
        legend: {
          show: false,
        },
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const map = new jsVectorMap({
      selector: "#map-world",
      map: "world",
      backgroundColor: "transparent",
      regionStyle: {
        initial: {
          fill: "var(--tblr-bg-surface-secondary)",
          stroke: "var(--tblr-border-color)",
          strokeWidth: 2,
        },
      },
      zoomOnScroll: false,
      zoomButtons: false,
      series: {
        regions: [
          {
            attribute: "fill",
            scale: {
              scale1: "color-mix(in srgb, transparent, var(--tblr-primary) 10%)",
              scale2: "color-mix(in srgb, transparent, var(--tblr-primary) 20%)",
              scale3: "color-mix(in srgb, transparent, var(--tblr-primary) 30%)",
              scale4: "color-mix(in srgb, transparent, var(--tblr-primary) 40%)",
              scale5: "color-mix(in srgb, transparent, var(--tblr-primary) 50%)",
              scale6: "color-mix(in srgb, transparent, var(--tblr-primary) 60%)",
              scale7: "color-mix(in srgb, transparent, var(--tblr-primary) 70%)",
              scale8: "color-mix(in srgb, transparent, var(--tblr-primary) 80%)",
              scale9: "color-mix(in srgb, transparent, var(--tblr-primary) 90%)",
              scale10: "color-mix(in srgb, transparent, var(--tblr-primary) 100%)",
            },
            values: {
              AF: "scale2",
              AL: "scale2",
              DZ: "scale4",
              AO: "scale3",
              AG: "scale1",
              AR: "scale5",
              AM: "scale1",
              AU: "scale7",
              AT: "scale5",
              AZ: "scale3",
              BS: "scale1",
              BH: "scale2",
              BD: "scale4",
              BB: "scale1",
              BY: "scale3",
              BE: "scale5",
              BZ: "scale1",
              BJ: "scale1",
              BT: "scale1",
              BO: "scale2",
              BA: "scale2",
              BW: "scale2",
              BR: "scale8",
              BN: "scale2",
              BG: "scale2",
              BF: "scale1",
              BI: "scale1",
              KH: "scale2",
              CM: "scale2",
              CA: "scale7",
              CV: "scale1",
              CF: "scale1",
              TD: "scale1",
              CL: "scale4",
              CN: "scale9",
              CO: "scale5",
              KM: "scale1",
              CD: "scale2",
              CG: "scale2",
              CR: "scale2",
              CI: "scale2",
              HR: "scale3",
              CY: "scale2",
              CZ: "scale4",
              DK: "scale5",
              DJ: "scale1",
              DM: "scale1",
              DO: "scale3",
              EC: "scale3",
              EG: "scale5",
              SV: "scale2",
              GQ: "scale2",
              ER: "scale1",
              EE: "scale2",
              ET: "scale2",
              FJ: "scale1",
              FI: "scale5",
              FR: "scale8",
              GA: "scale2",
              GM: "scale1",
              GE: "scale2",
              DE: "scale8",
              GH: "scale2",
              GR: "scale5",
              GD: "scale1",
              GT: "scale2",
              GN: "scale1",
              GW: "scale1",
              GY: "scale1",
              HT: "scale1",
              HN: "scale2",
              HK: "scale5",
              HU: "scale4",
              IS: "scale2",
              IN: "scale7",
              ID: "scale6",
              IR: "scale5",
              IQ: "scale3",
              IE: "scale5",
              IL: "scale5",
              IT: "scale8",
              JM: "scale2",
              JP: "scale9",
              JO: "scale2",
              KZ: "scale4",
              KE: "scale2",
              KI: "scale1",
              KR: "scale6",
              KW: "scale4",
              KG: "scale1",
              LA: "scale1",
              LV: "scale2",
              LB: "scale2",
              LS: "scale1",
              LR: "scale1",
              LY: "scale3",
              LT: "scale2",
              LU: "scale3",
              MK: "scale1",
              MG: "scale1",
              MW: "scale1",
              MY: "scale5",
              MV: "scale1",
              ML: "scale1",
              MT: "scale1",
              MR: "scale1",
              MU: "scale1",
              MX: "scale7",
              MD: "scale1",
              MN: "scale1",
              ME: "scale1",
              MA: "scale3",
              MZ: "scale2",
              MM: "scale2",
              NA: "scale2",
              NP: "scale2",
              NL: "scale6",
              NZ: "scale4",
              NI: "scale1",
              NE: "scale1",
              NG: "scale5",
              NO: "scale5",
              OM: "scale3",
              PK: "scale4",
              PA: "scale2",
              PG: "scale1",
              PY: "scale2",
              PE: "scale4",
              PH: "scale4",
              PL: "scale10",
              PT: "scale5",
              QA: "scale4",
              RO: "scale4",
              RU: "scale7",
              RW: "scale1",
              WS: "scale1",
              ST: "scale1",
              SA: "scale5",
              SN: "scale2",
              RS: "scale2",
              SC: "scale1",
              SL: "scale1",
              SG: "scale5",
              SK: "scale3",
              SI: "scale2",
              SB: "scale1",
              ZA: "scale5",
              ES: "scale7",
              LK: "scale2",
              KN: "scale1",
              LC: "scale1",
              VC: "scale1",
              SD: "scale3",
              SR: "scale1",
              SZ: "scale1",
              SE: "scale5",
              CH: "scale6",
              SY: "scale3",
              TW: "scale5",
              TJ: "scale1",
              TZ: "scale2",
              TH: "scale5",
              TL: "scale1",
              TG: "scale1",
              TO: "scale1",
              TT: "scale2",
              TN: "scale2",
              TR: "scale6",
              TM: "scale1",
              UG: "scale2",
              UA: "scale4",
              AE: "scale5",
              GB: "scale8",
              US: "scale10",
              UY: "scale2",
              UZ: "scale2",
              VU: "scale1",
              VE: "scale5",
              VN: "scale4",
              YE: "scale2",
              ZM: "scale2",
              ZW: "scale1",
            },
          },
        ],
      },
    });
    window.addEventListener("resize", () => {
      map.updateSize();
    });
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("sparkline-activity"), {
        chart: {
          type: "radialBar",
          fontFamily: "inherit",
          height: 40,
          width: 40,
          animations: {
            enabled: false,
          },
          sparkline: {
            enabled: true,
          },
        },
        tooltip: {
          enabled: false,
        },
        plotOptions: {
          radialBar: {
            hollow: {
              margin: 0,
              size: "75%",
            },
            track: {
              margin: 0,
            },
            dataLabels: {
              show: false,
            },
          },
        },
        colors: ["var(--tblr-primary)"],
        series: [35],
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("chart-development-activity"), {
        chart: {
          type: "area",
          fontFamily: "inherit",
          height: 192,
          sparkline: {
            enabled: true,
          },
          animations: {
            enabled: false,
          },
        },
        dataLabels: {
          enabled: false,
        },
        fill: {
          colors: ["color-mix(in srgb, transparent, var(--tblr-primary) 16%)", "color-mix(in srgb, transparent, var(--tblr-primary) 16%)"],
          type: "solid",
        },
        stroke: {
          width: 2,
          lineCap: "round",
          curve: "smooth",
        },
        series: [
          {
            name: "Purchases",
            data: [3, 5, 4, 6, 7, 5, 6, 8, 24, 7, 12, 5, 6, 3, 8, 4, 14, 30, 17, 19, 15, 14, 25, 32, 40, 55, 60, 48, 52, 70],
          },
        ],
        tooltip: {
          theme: "dark",
        },
        grid: {
          strokeDashArray: 4,
        },
        xaxis: {
          labels: {
            padding: 0,
          },
          tooltip: {
            enabled: false,
          },
          axisBorder: {
            show: false,
          },
          type: "datetime",
        },
        yaxis: {
          labels: {
            padding: 4,
          },
        },
        labels: [
          "2020-06-20",
          "2020-06-21",
          "2020-06-22",
          "2020-06-23",
          "2020-06-24",
          "2020-06-25",
          "2020-06-26",
          "2020-06-27",
          "2020-06-28",
          "2020-06-29",
          "2020-06-30",
          "2020-07-01",
          "2020-07-02",
          "2020-07-03",
          "2020-07-04",
          "2020-07-05",
          "2020-07-06",
          "2020-07-07",
          "2020-07-08",
          "2020-07-09",
          "2020-07-10",
          "2020-07-11",
          "2020-07-12",
          "2020-07-13",
          "2020-07-14",
          "2020-07-15",
          "2020-07-16",
          "2020-07-17",
          "2020-07-18",
          "2020-07-19",
        ],
        colors: ["color-mix(in srgb, transparent, var(--tblr-primary) 100%)"],
        legend: {
          show: false,
        },
        point: {
          show: false,
        },
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("sparkline-bounce-rate-1"), {
        chart: {
          type: "line",
          fontFamily: "inherit",
          height: 24,
          animations: {
            enabled: false,
          },
          sparkline: {
            enabled: true,
          },
        },
        tooltip: {
          enabled: false,
        },
        stroke: {
          width: 2,
          lineCap: "round",
        },
        series: [
          {
            color: "var(--tblr-primary)",
            data: [17, 24, 20, 10, 5, 1, 4, 18, 13],
          },
        ],
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("sparkline-bounce-rate-2"), {
        chart: {
          type: "line",
          fontFamily: "inherit",
          height: 24,
          animations: {
            enabled: false,
          },
          sparkline: {
            enabled: true,
          },
        },
        tooltip: {
          enabled: false,
        },
        stroke: {
          width: 2,
          lineCap: "round",
        },
        series: [
          {
            color: "var(--tblr-primary)",
            data: [13, 11, 19, 22, 12, 7, 14, 3, 21],
          },
        ],
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("sparkline-bounce-rate-3"), {
        chart: {
          type: "line",
          fontFamily: "inherit",
          height: 24,
          animations: {
            enabled: false,
          },
          sparkline: {
            enabled: true,
          },
        },
        tooltip: {
          enabled: false,
        },
        stroke: {
          width: 2,
          lineCap: "round",
        },
        series: [
          {
            color: "var(--tblr-primary)",
            data: [10, 13, 10, 4, 17, 3, 23, 22, 19],
          },
        ],
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("sparkline-bounce-rate-4"), {
        chart: {
          type: "line",
          fontFamily: "inherit",
          height: 24,
          animations: {
            enabled: false,
          },
          sparkline: {
            enabled: true,
          },
        },
        tooltip: {
          enabled: false,
        },
        stroke: {
          width: 2,
          lineCap: "round",
        },
        series: [
          {
            color: "var(--tblr-primary)",
            data: [6, 15, 13, 13, 5, 7, 17, 20, 19],
          },
        ],
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("sparkline-bounce-rate-5"), {
        chart: {
          type: "line",
          fontFamily: "inherit",
          height: 24,
          animations: {
            enabled: false,
          },
          sparkline: {
            enabled: true,
          },
        },
        tooltip: {
          enabled: false,
        },
        stroke: {
          width: 2,
          lineCap: "round",
        },
        series: [
          {
            color: "var(--tblr-primary)",
            data: [2, 11, 15, 14, 21, 20, 8, 23, 18, 14],
          },
        ],
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.ApexCharts &&
      new ApexCharts(document.getElementById("sparkline-bounce-rate-6"), {
        chart: {
          type: "line",
          fontFamily: "inherit",
          height: 24,
          animations: {
            enabled: false,
          },
          sparkline: {
            enabled: true,
          },
        },
        tooltip: {
          enabled: false,
        },
        stroke: {
          width: 2,
          lineCap: "round",
        },
        series: [
          {
            color: "var(--tblr-primary)",
            data: [22, 12, 7, 14, 3, 21, 8, 23, 18, 14],
          },
        ],
      }).render();
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    var themeConfig = {
      theme: "light",
      "theme-base": "gray",
      "theme-font": "sans-serif",
      "theme-primary": "blue",
      "theme-radius": "1",
    };
    var url = new URL(window.location);
    var form = document.getElementById("offcanvasSettings");
    var resetButton = document.getElementById("reset-changes");
    var checkItems = function () {
      for (var key in themeConfig) {
        var value = window.localStorage["tabler-" + key] || themeConfig[key];
        if (!!value) {
          var radios = form.querySelectorAll(`[name="${key}"]`);
          if (!!radios) {
            radios.forEach((radio) => {
              radio.checked = radio.value === value;
            });
          }
        }
      }
    };
    form.addEventListener("change", function (event) {
      var target = event.target,
        name = target.name,
        value = target.value;
      for (var key in themeConfig) {
        if (name === key) {
          document.documentElement.setAttribute("data-bs-" + key, value);
          window.localStorage.setItem("tabler-" + key, value);
          url.searchParams.set(key, value);
        }
      }
      window.history.pushState({}, "", url);
    });
    resetButton.addEventListener("click", function () {
      for (var key in themeConfig) {
        var value = themeConfig[key];
        document.documentElement.removeAttribute("data-bs-" + key);
        window.localStorage.removeItem("tabler-" + key);
        url.searchParams.delete(key);
      }
      checkItems();
      window.history.pushState({}, "", url);
    });
    checkItems();
  });
</script>
<!-- END PAGE SCRIPTS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
@stack('scripts')
