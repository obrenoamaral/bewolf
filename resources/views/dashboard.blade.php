<x-app-layout>
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-[#1E1E1E] p-6 rounded-lg shadow-md flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-300">Total de Clientes</h3>
                    <p class="text-3xl font-bold text-white">{{ $clients }}</p>
                </div>
                <i class="fas fa-users text-4xl text-gray-500"></i>
            </div>

            <div class="bg-[#1E1E1E] p-6 rounded-lg shadow-md flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-300">Questões de Multipla Escolha</h3>
                    <p class="text-3xl font-bold text-white">{{ $multipleChoices }}</p>
                </div>
                <i class="fas fa-file-alt text-4xl text-gray-500"></i>
            </div>

            <div class="bg-[#1E1E1E] p-6 rounded-lg shadow-md flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-300">Perguntas Cadastradas</h3>
                    <p class="text-3xl font-bold text-white">{{ $questions }}</p>
                </div>
                <i class="fas fa-question-circle text-4xl text-gray-500"></i>
            </div>
        </div>

        <div class="bg-[#1E1E1E] p-6 rounded-lg shadow-md">
            <div class="flex flex-col md:flex-row items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-white mb-4 md:mb-0">Formulários Enviados por Dia</h2>
                <div class="flex flex-wrap gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-300">Data Inicial:</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                               class="mt-1 p-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:border-blue-500 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-300">Data Final:</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                               class="mt-1 p-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:border-blue-500 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="chart_type" class="block text-sm font-medium text-gray-300">Tipo:</label>
                        <select id="chart_type" name="chart_type"
                                class="mt-1 p-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:border-blue-500 focus:ring-blue-500 focus:outline-none">
                            <option value="line">Linha</option>
                            <option value="bar">Barra</option>
                            <option value="pie">Pizza</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="h-80">
                <canvas id="formChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('formChart').getContext('2d');

            let chartData = {
                labels: @json($labels),
                datasets: [{
                    label: 'Formulários Preenchidos',
                    data: @json($data),
                    borderColor: 'rgba(54, 162, 235, 1)',  // Azul
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4 // Linhas mais suaves
                }]
            };

            const formChart = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Importante para o gráfico ocupar o espaço definido
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white' // Cor da legenda
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: 'white', // Cor dos ticks do eixo Y
                                stepSize: 1 // Define o espaçamento entre os números (opcional)
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.1)' // Cor das linhas de grade do eixo Y
                            }
                        },
                        x: {
                            ticks: {
                                color: 'white' // Cor dos ticks do eixo X
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.1)' // Cor das linhas de grade do eixo X
                            }
                        }
                    }
                }
            });

            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const chartTypeSelect = document.getElementById('chart_type');

            function filterData() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                const chartType = chartTypeSelect.value;


                const filteredLabels = [];
                const filteredData = [];

                @json($labels).forEach((label, index) => {
                    const dataDate = new Date(label);

                    if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
                        //Ambas as datas são válidas, filtra por intervalo
                        if (dataDate >= startDate && dataDate <= endDate) {
                            filteredLabels.push(label);
                            filteredData.push(@json($data)[index]);
                        }
                    } else if (!isNaN(startDate.getTime())) {
                        //Apenas data inicial válida, filtra a partir da data inicial
                        if(dataDate >= startDate){
                            filteredLabels.push(label);
                            filteredData.push(@json($data)[index]);
                        }
                    } else if(!isNaN(endDate.getTime())) {
                        //Apenas data final válida, filtra até a data final
                        if(dataDate <= endDate){
                            filteredLabels.push(label);
                            filteredData.push(@json($data)[index]);
                        }

                    } else {
                        //Nenhuma data válida, mostra todos os dados
                        filteredLabels.push(label);
                        filteredData.push(@json($data)[index]);
                    }
                });

                formChart.data.labels = filteredLabels;
                formChart.data.datasets[0].data = filteredData;
                formChart.config.type = chartType; // Atualiza o tipo de gráfico

                if (chartType === 'pie') {
                    // Configuração específica para gráfico de pizza
                    formChart.data.datasets[0].backgroundColor = [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)' // Laranja
                    ];
                    formChart.data.datasets[0].borderColor =  [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ];
                    formChart.options.plugins.legend.display = true; //Mostra legenda

                } else {
                    // Configuração para gráfico de linha ou barra
                    formChart.data.datasets[0].backgroundColor = chartType === 'line' ? 'rgba(54, 162, 235, 0.2)' : 'rgba(54, 162, 235, 0.7)';
                    formChart.data.datasets[0].borderColor = 'rgba(54, 162, 235, 1)';
                    formChart.options.plugins.legend.display = false; // Oculta a legenda
                }

                formChart.update();
            }

            startDateInput.addEventListener('change', filterData);
            endDateInput.addEventListener('change', filterData);
            chartTypeSelect.addEventListener('change', filterData);

            filterData(); // Chama filterData inicialmente
        });
    </script>
</x-app-layout>
