const baseColors = ['accent', 'success', 'danger', 'warning'];
const shades = ['100', '70', '50', '30', '0'];
const colorVariables = getColorVariables(baseColors, shades);
let selectedSettingsIds = [];

function getCssVariableValue(variableName) {
    return getComputedStyle(document.documentElement).getPropertyValue(variableName).trim();
}

function getColorVariables(baseColors, shades) {
    const colors = {};
    baseColors.forEach(baseColor => {
        shades.forEach(shade => {
            const variableName = `--${baseColor}-${shade}`;
            colors[`${baseColor}${shade}`] = getCssVariableValue(variableName);
        });
    });
    return colors;
}

function createChart(ctx, type, labels = [], datasets = [], currency = false) {
    const defaultDatasetSettings = {
        data: [],
        fill: 'start',
        borderWidth: 1,
        pointBackgroundColor: colorVariables.accent100,
        pointBorderColor: colorVariables.accent100,
        pointRadius: 3,
    };

    const colors = [
        [colorVariables.accent30, colorVariables.accent0],
        [colorVariables.success30, colorVariables.success0],
        [colorVariables.warning30, colorVariables.warning0],
        [colorVariables.danger30, colorVariables.danger0],
        ['rgba(123, 77, 255, 0.3)', 'rgba(123, 77, 255, 0)'],
    ];

    const defaultLineColors = {
        backgroundColor: ctx => getBackgroundColor(ctx, colorVariables.accent30, colorVariables.accent0),
        borderColor: colorVariables.accent100,
    };

    if (type === 'line' && datasets.length === 0) {
        datasets = [{ ...defaultLineColors }];
    }

    const preparedDatasets = datasets.map((dataset, index) => ({
        ...defaultDatasetSettings,
        ...(type === 'line' ? defaultLineColors : {}),
        backgroundColor: ctx => getBackgroundColor(ctx, colors[index % colors.length][0], colors[index % colors.length][1]),
        borderColor: colors[index % colors.length][0].replace('0.3', '1'),
        pointBorderColor: colors[index % colors.length][0].replace('0.3', '1'),
        pointBackgroundColor: colors[index % colors.length][0].replace('0.3', '1'),
        ...dataset
    }));

    return new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: type === 'doughnut' ? [{
                data: [],
                cutout: '80%',
                backgroundColor: [
                    colorVariables.accent100,
                    colorVariables.accent70,
                    colorVariables.accent50,
                    colorVariables.accent30,
                ],
            }] : preparedDatasets,
        },
        options: {
            ...(type === 'line' && {
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
            }),
            scales: type === 'line' ? {
                x: {
                    grid: {
                        display: true,
                        drawOnChartArea: false,
                        drawTicks: true,
                        color: 'rgba(219,224,233,1)',
                        borderColor: 'rgba(77,94,111,1)',
                    },
                    ticks: {
                        color: 'rgba(128,140,152,1)',
                        font: {
                            weight: '500'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(219,224,233,1)',
                        borderColor: 'rgba(77,94,111,1)',
                    },
                    ticks: {
                        color: 'rgba(128,140,152,1)',
                        font: {
                            weight: '500'
                        },
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null;
                        }
                    },
                    border: {
                        display: false,
                    }
                }
            } : {},
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                title: {
                    display: false,
                },
                tooltip: {
                    position: 'nearest',
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            let label = '';

                            if (context.chart.config.type === 'doughnut') {
                                label = context.label || '';
                                if (label) {
                                    label = ' ' + label + ': ';
                                }
                                if (context.raw !== null) {
                                    if (currency) {
                                        label += formatCurrency(context.raw);
                                    } else {
                                        label += context.raw;
                                    }
                                }
                            } else {
                                label = context.dataset.label || '';
                                if (label) {
                                    label = ' ' + label + ': ';
                                }
                                if (context.parsed.y !== null) {
                                    if (currency) {
                                        label += formatCurrency(context.parsed.y);
                                    } else {
                                        label += context.parsed.y;
                                    }
                                }
                            }

                            return label;
                        },
                        labelPointStyle: function(context) {
                            return {
                                pointStyle: 'rect',
                                rotation: 0,
                                borderColor: 'transparent',
                                borderWidth: 0,
                            };
                        },
                        labelColor: function(context) {
                            return {
                                borderColor: 'transparent',
                                backgroundColor: context.chart.config.type === 'line' ? context.dataset.borderColor || context.dataset.pointBackgroundColor : context.dataset.backgroundColor[context.dataIndex],
                                borderWidth: 0
                            };
                        }
                    }
                }
            }
        }
    });
}

function updateLineChart(chart, labels, items) {
    chart.data.labels = labels;
    items.forEach((item, index) => {
        chart.data.datasets[index].label = item.label;
        chart.data.datasets[index].data = item.values;
    });
    chart.update();
}

function updateDoughnutChart(chart, info, type = '') {
    const valuesExists = info.values && info.values.length;
    const hasValues = info.values && info.values.length && info.values.reduce((a, b) => a + b, 0) !== 0;
    const hasPercents = info.percents && info.percents.length && info.percents.reduce((a, b) => a + b, 0) !== 0;
    chart.data.labels = info.labels;
    chart.data.datasets[0].data = valuesExists ? info.values : info.percents;
    chart.update();

    const infoBlocks = chart.canvas.parentNode.nextElementSibling.querySelectorAll('.masterstudy-doughnut-chart__info-block');
    info.labels.forEach((label, index) => {
        if (infoBlocks[index]) {
            infoBlocks[index].querySelector('.masterstudy-doughnut-chart__info-title').innerText = label;
            infoBlocks[index].querySelector('.masterstudy-doughnut-chart__info-percent').innerText = `${info.percents[index]}%`;
            if (valuesExists) {
                infoBlocks[index].querySelector('.masterstudy-doughnut-chart__info-value').innerText = type === 'currency' ? formatCurrency(info.values[index]) : info.values[index];
            }
        }
    });

    const emptyChartElement = chart.canvas.parentNode.querySelector('.masterstudy-analytics-empty-chart');

    if (hasValues || hasPercents) {
        emptyChartElement.style.display = 'none';
    } else {
        emptyChartElement.style.display = 'flex';
    }
}

function createGradient(ctx, chartArea, startColor, endColor) {
    if (!chartArea || !startColor || !endColor) {
        return startColor || endColor || 'rgba(0, 0, 0, 0)';
    }

    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
    gradient.addColorStop(0, startColor);
    gradient.addColorStop(0.8, endColor);

    return gradient;
}

function getBackgroundColor(ctx, startColor, endColor) {
    const chart = ctx.chart;
    const { ctx: context, chartArea } = chart;

    if (!chartArea) {
        return null;
    }

    return createGradient(context, chartArea, startColor, endColor);
}

function getPercentesByValues(values) {
    const total = values.reduce((acc, value) => acc + value, 0);

    const percentages = values.map(value => value > 0 ? (value / total * 100) : 0);

    const roundedPercentages = percentages.map(Math.round);

    const roundedTotal = roundedPercentages.reduce((acc, value) => acc + value, 0);

    // Adjust the last percentage to ensure the total is 100
    if (roundedTotal !== 100) {
        const difference = 100 - roundedTotal;
        const index = roundedPercentages.findIndex(value => value > 0);
        roundedPercentages[index] += difference;
    }

    return roundedPercentages;
}

function chartsVisibilityControl() {
    document.querySelectorAll('[data-chart-id]').forEach(function(element) {
        element.style.display = 'flex';
    });
    if (selectedSettingsIds.length > 0) {
        selectedSettingsIds.forEach(id => {
            const targetContainer = document.querySelector(`[data-chart-id="${id}"]`);
            if (targetContainer) {
                targetContainer.style.display = 'none';
            }
        });
    }
}

function saveChartsVisibility() {
    localStorage.setItem('chartsVisibilityIds', JSON.stringify(selectedSettingsIds));
}

function loadChartsVisibility() {
    const savedIds = localStorage.getItem('chartsVisibilityIds');
    if (savedIds) {
        selectedSettingsIds = JSON.parse(savedIds);
        chartsVisibilityControl();
    }

    document.querySelectorAll('.masterstudy-settings-modal__item-wrapper').forEach(function(wrapper) {
        const parentId = wrapper.parentNode.id;

        if (selectedSettingsIds.includes(parentId)) {
            wrapper.classList.remove('masterstudy-settings-modal__item-wrapper_fill');
        } else {
            wrapper.classList.add('masterstudy-settings-modal__item-wrapper_fill');
        }
    });
}

function closeSettingsModal() {
    document.querySelector('.masterstudy-settings-modal').classList.remove('masterstudy-settings-modal_open');
    document.body.classList.remove('masterstudy-settings-modal-body-hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    loadChartsVisibility();

    const settingsButton = document.querySelector('.masterstudy-settings-button');
    const settingsModal  = document.querySelector('.masterstudy-settings-modal');

    if (settingsButton) {
        document.querySelector('.masterstudy-settings-button').addEventListener('click', function() {
            settingsModal.classList.add('masterstudy-settings-modal_open');
            document.body.classList.add('masterstudy-settings-modal-body-hidden');
        });

        document.addEventListener('click', function(event) {
            const clickedDropdown = event.target.closest('.masterstudy-settings-dropdown');

            document.querySelectorAll('.masterstudy-settings-dropdown__menu_open').forEach(function(openDropdown) {
                if (openDropdown !== clickedDropdown?.querySelector('.masterstudy-settings-dropdown__menu')) {
                    openDropdown.classList.remove('masterstudy-settings-dropdown__menu_open');
                }
            });

            if (clickedDropdown) {
                const dropdownMenu = clickedDropdown.querySelector('.masterstudy-settings-dropdown__menu');
                dropdownMenu.classList.toggle('masterstudy-settings-dropdown__menu_open');
            }
        });

        document.querySelectorAll('.masterstudy-settings-dropdown__item').forEach(function(item) {
            item.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                selectedSettingsIds.push(id);
                chartsVisibilityControl();
                saveChartsVisibility();
                document.querySelector(`#${id}`).querySelector('.masterstudy-settings-modal__item-wrapper').classList.remove('masterstudy-settings-modal__item-wrapper_fill');
            });
        });

        if (settingsModal) {
            setTimeout(function() {
                settingsModal.removeAttribute('style');
            }, 1000);

            settingsModal.addEventListener('click', function(event) {
                if (event.target === this) {
                    closeSettingsModal();
                }
            });

            document.querySelector('.masterstudy-settings-modal__header-close').addEventListener('click', function() {
                closeSettingsModal();
            });

            document.querySelectorAll('.masterstudy-settings-modal__item-wrapper').forEach(function(wrapper) {
                wrapper.addEventListener('click', function() {
                    this.classList.toggle('masterstudy-settings-modal__item-wrapper_fill');
                    const parentId = this.parentNode.id;

                    if (selectedSettingsIds.includes(parentId)) {
                        selectedSettingsIds = selectedSettingsIds.filter(id => id !== parentId);
                    } else {
                        selectedSettingsIds.push(parentId);
                    }

                    chartsVisibilityControl();
                    saveChartsVisibility();
                });
            });
        }
    }
})