<x-app-layout>
    <div class="py-10 max-w-5xl mx-auto">
        <div class="mx-auto sm:px-6 lg:px-8 flex gap-6">
            <div class="border border-gray-600 w-80 p-10 rounded-lg">
                <h3 class="text-center text-gray-100 py-2">Total de Clientes</h3>
                <p class="text-center text-gray-100 text-4xl">
                    {{ $clients }}
                </p>
            </div>
            <div class="border border-gray-600 w-80 p-10 rounded-lg">
                <h3 class="text-center text-gray-100 py-2">Total de Formulários Enviados</h3>
                <p class="text-center text-gray-100 text-4xl">
                    {{ $forms }}
                </p>
            </div>
            <div class="border border-gray-600 w-80 p-10 rounded-lg">
                <h3 class="text-center text-gray-100 py-2">Perguntas Cadastradas</h3>
                <p class="text-center text-gray-100 text-4xl">
                    {{ $questions }}
                </p>
            </div>
        </div>

        <div class="mt-4 bg-dark p-6 rounded-lg">
            <h2 class="text-center text-gray-100 text-xl mb-4">Formulários Enviados por Dia</h2>
            <canvas id="formChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('formChart').getContext('2d');
            let chartData = {
                labels: @json($labels),
                datasets: [{
                    label: 'Formulários Preenchidos',
                    data: @json($data),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    fill: true
                }]
            };

            const formChart = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            function filterData() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                const filteredLabels = [];
                const filteredData = [];

                @json($labels).forEach((label, index) => {
                    const dataDate = new Date(label); // Assumindo que seus labels são datas

                    if (dataDate >= startDate && dataDate <= endDate) {
                        filteredLabels.push(label);
                        filteredData.push(@json($data)[index]);
                    }
                });

                formChart.data.labels = filteredLabels;
                formChart.data.datasets[0].data = filteredData;
                formChart.update();
            }

            startDateInput.addEventListener('change', filterData);
            endDateInput.addEventListener('change', filterData);
        });
    </script>

</x-app-layout>
