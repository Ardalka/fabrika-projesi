document.addEventListener('DOMContentLoaded', () => {
    const productSelect = document.getElementById('product');
    const machineSelect = document.getElementById('machine');
    const quantityInput = document.getElementById('quantity');
    const productionTimeDisplay = document.getElementById('production-time');
    const energyUsedDisplay = document.getElementById('energy-used');
    const carbonFootprintDisplay = document.getElementById('carbon-footprint');

    function updateProductionDetails() {
        const machine = machineSelect.options[machineSelect.selectedIndex].dataset;
        const quantity = parseInt(quantityInput.value) || 1;

        const productionTime = parseFloat(machine.productionRate) * quantity;
        const energyUsed = parseFloat(machine.energyConsumption) * quantity;
        const carbonFootprint = parseFloat(machine.carbonFootprint) * quantity;

        productionTimeDisplay.textContent = `${productionTime.toFixed(2)} saat`;
        energyUsedDisplay.textContent = `${energyUsed.toFixed(2)} kWh`;
        carbonFootprintDisplay.textContent = `${carbonFootprint.toFixed(2)} kg CO2`;
    }

    machineSelect.addEventListener('change', updateProductionDetails);
    quantityInput.addEventListener('input', updateProductionDetails);
});
