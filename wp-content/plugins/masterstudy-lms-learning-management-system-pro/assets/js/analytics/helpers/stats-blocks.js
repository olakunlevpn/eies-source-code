function updateStatsBlock(selector, value, type = 'default') {
    const statsBlock = document.querySelector(selector);
    if (statsBlock) {
        const valueElement = statsBlock.querySelector('.masterstudy-stats-block__value');
        if (valueElement) {
            if (type === 'currency') {
                valueElement.innerText = formatCurrency(value);
            } else {
                valueElement.innerText = value;
            }
        }
    }
}

function updateTotal(selector, total, type) {
    const totalElement = document.querySelector(selector);
    if (totalElement) {
        if (type === 'percent') {
            totalElement.innerText = `${total}%`;
        } else if (type === 'currency') {
            totalElement.innerText = formatCurrency(total);
        } else {
            totalElement.innerText = total;
        }
    }
}

function formatCurrency(value) {
    let formattedValue = Number(value).toFixed(stats_block_data.decimals_num);
    let parts = formattedValue.split('.');

    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, stats_block_data.currency_thousands);

    formattedValue = parts.join(stats_block_data.currency_decimals);

    if (stats_block_data.currency_position === 'left') {
        return stats_block_data.currency_symbol + formattedValue;
    } else {
        return formattedValue + stats_block_data.currency_symbol;
    }
}