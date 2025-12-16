"use strict";

// Class definition
var KTChartsWidgetOverview = function () {
    var chart = {
        self: null,
        rendered: false
    };
    
    // Estado da paginação
    var paginationState = {
        currentOffset: 0,
        limit: 30,
        hasMore: false,
        total: 0,
        currentData: {
            categorias: [],
            entradas: [],
            saidas: []
        }
    };

    // Private methods
    var loadChartData = function(startDate, endDate, callback, resetPagination) {
        // Se resetPagination for true, reseta o offset
        if (resetPagination !== false) {
            paginationState.currentOffset = 0;
            paginationState.currentData = {
                categorias: [],
                entradas: [],
                saidas: []
            };
        }
        console.log('[KTChartsWidgetOverview] Carregando dados do gráfico:', { startDate, endDate });

        // Usar a URL definida no Blade ou fallback para a rota padrão
        var url = typeof bancoFluxoChartDataUrl !== 'undefined' ? bancoFluxoChartDataUrl : '/banco/fluxo-chart-data';
        console.log('[KTChartsWidgetOverview] URL base da rota:', url);

        // Se a URL contém o domínio completo, extrair apenas o caminho
        try {
            var urlObj = new URL(url, window.location.origin);
            url = urlObj.pathname;
            console.log('[KTChartsWidgetOverview] URL extraída (apenas caminho):', url);
        } catch (e) {
            // Se já for um caminho relativo, usar como está
            console.log('[KTChartsWidgetOverview] URL já é relativa, usando como está');
        }

        // Obter valor do select de agrupamento
        var groupBySelect = document.getElementById('group-by-select');
        var groupBy = groupBySelect ? groupBySelect.value : 'auto';

        var params = new URLSearchParams({
            start_date: startDate,
            end_date: endDate,
            group_by: groupBy,
            limit: paginationState.limit,
            offset: paginationState.currentOffset
        });

        var fullUrl = url + '?' + params.toString();
        console.log('[KTChartsWidgetOverview] URL da requisição:', fullUrl);

        fetch(fullUrl, {
            method: 'GET',
            credentials: 'include', // Incluir cookies de autenticação (igual ao chart-combined.js)
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('[KTChartsWidgetOverview] Resposta recebida:', response.status, response.statusText);
            console.log('[KTChartsWidgetOverview] Headers da resposta:', {
                contentType: response.headers.get('content-type'),
                location: response.headers.get('location')
            });

            if (!response.ok) {
                console.error('[KTChartsWidgetOverview] ========== ERRO HTTP ==========');
                console.error('[KTChartsWidgetOverview] Status:', response.status, response.statusText);
                console.error('[KTChartsWidgetOverview] URL que falhou:', fullUrl);

                // Tentar ler o corpo da resposta mesmo em caso de erro
                return response.text().then(text => {
                    console.error('[KTChartsWidgetOverview] Corpo da resposta de erro:', text);
                    console.error('[KTChartsWidgetOverview] Tamanho da resposta:', text ? text.length : 0);

                    if (text) {
                        try {
                            var errorData = JSON.parse(text);
                            console.error('[KTChartsWidgetOverview] Dados do erro (JSON):', errorData);
                        } catch (e) {
                            console.error('[KTChartsWidgetOverview] Resposta não é JSON válido, é HTML ou texto');
                            console.error('[KTChartsWidgetOverview] Primeiros 500 caracteres:', text.substring(0, 500));
                        }
                    } else {
                        console.error('[KTChartsWidgetOverview] Resposta vazia');
                    }

                    console.error('[KTChartsWidgetOverview] =================================');
                    return null; // Retornar null para indicar erro
                }).catch(err => {
                    console.error('[KTChartsWidgetOverview] Erro ao ler corpo da resposta:', err);
                    return null;
                });
            }

            // Se a resposta está OK, tentar parsear como JSON
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('[KTChartsWidgetOverview] Erro ao parsear JSON:', e);
                    console.error('[KTChartsWidgetOverview] Texto recebido:', text);
                    return null;
                }
            });
        })
        .then(data => {
            console.log('[KTChartsWidgetOverview] Dados recebidos:', data);
            if (!data || data === null) {
                console.error('[KTChartsWidgetOverview] ERRO: Nenhum dado recebido ou requisição falhou');
                console.error('[KTChartsWidgetOverview] Verifique se a rota /banco/fluxo-chart-data está acessível');
                console.error('[KTChartsWidgetOverview] Verifique se você está autenticado e tem permissão para acessar esta rota');

                // Tentar com dados vazios para verificar se o gráfico renderiza
                console.warn('[KTChartsWidgetOverview] Tentando renderizar gráfico com dados vazios para teste...');
                if (callback) {
                    callback({
                        categorias: [],
                        entradas: [],
                        saidas: [],
                        totais: {
                            entradas: 0,
                            saidas: 0,
                            saldo: 0
                        },
                        paginacao: {
                            has_more: false,
                            total: 0
                        }
                    });
                }
                return;
            }
            
            // Atualizar estado da paginação
            if (data.paginacao) {
                paginationState.hasMore = data.paginacao.has_more || false;
                paginationState.total = data.paginacao.total || 0;
                paginationState.currentOffset = data.paginacao.next_offset || paginationState.currentOffset;
            }
            
            // Se for reset (primeira carga), substituir dados
            // Se for carregar mais, adicionar aos dados existentes
            if (resetPagination !== false) {
                paginationState.currentData = {
                    categorias: data.categorias || [],
                    entradas: data.entradas || [],
                    saidas: data.saidas || []
                };
            } else {
                // Adicionar novos dados aos existentes
                paginationState.currentData.categorias = (paginationState.currentData.categorias || []).concat(data.categorias || []);
                paginationState.currentData.entradas = (paginationState.currentData.entradas || []).concat(data.entradas || []);
                paginationState.currentData.saidas = (paginationState.currentData.saidas || []).concat(data.saidas || []);
            }
            
            // Preparar dados combinados para o callback
            var combinedData = {
                categorias: paginationState.currentData.categorias,
                entradas: paginationState.currentData.entradas,
                saidas: paginationState.currentData.saidas,
                totais: data.totais || {},
                paginacao: data.paginacao || {}
            };
            
            if (callback) {
                callback(combinedData);
            }
        })
        .catch(error => {
            console.error('[KTChartsWidgetOverview] ========== ERRO NO CATCH ==========');
            console.error('[KTChartsWidgetOverview] Erro ao carregar dados do gráfico:', error);
            console.error('[KTChartsWidgetOverview] Mensagem:', error.message);
            console.error('[KTChartsWidgetOverview] Stack trace:', error.stack);
            console.error('[KTChartsWidgetOverview] ====================================');
        });
    }

    var initChart = function(chart, chartData) {
        console.log('[KTChartsWidgetOverview] Inicializando gráfico...');
        console.log('[KTChartsWidgetOverview] Dados recebidos para o gráfico:', chartData);

        var element = document.getElementById("kt_charts_widget_overview");

        if (!element) {
            console.error('[KTChartsWidgetOverview] ERRO: Elemento kt_charts_widget_overview não encontrado no DOM');
            console.error('[KTChartsWidgetOverview] Verifique se o elemento existe no HTML com o ID correto');
            return;
        }

        console.log('[KTChartsWidgetOverview] Elemento encontrado:', element);

        // Verificar se ApexCharts está disponível
        if (typeof ApexCharts === 'undefined') {
            console.error('[KTChartsWidgetOverview] ERRO: ApexCharts não está carregado');
            console.error('[KTChartsWidgetOverview] Certifique-se de que a biblioteca ApexCharts está incluída antes deste script');
            return;
        }

        console.log('[KTChartsWidgetOverview] ApexCharts disponível, continuando...');

        // Verificar se KTUtil está disponível
        if (typeof KTUtil === 'undefined') {
            console.error('[KTChartsWidgetOverview] ERRO: KTUtil não está disponível');
            return;
        }

        var height = parseInt(KTUtil.css(element, 'height')) || 300;
        if (!height || isNaN(height)) {
            height = 300; // Altura padrão
            console.warn('[KTChartsWidgetOverview] Altura não encontrada, usando padrão: 300px');
        }
        console.log('[KTChartsWidgetOverview] Altura do elemento:', height);

        var labelColor = KTUtil.getCssVariableValue('--bs-gray-500') || '#6c757d';
        var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color') || '#e4e6ef';
        var basedangerColor = KTUtil.getCssVariableValue('--bs-danger') || '#f1416c';
        var lightdangerColor = KTUtil.getCssVariableValue('--bs-danger') || '#f1416c';
        var basesuccessColor = KTUtil.getCssVariableValue('--bs-success') || '#50cd89';
        var lightsuccessColor = KTUtil.getCssVariableValue('--bs-success') || '#50cd89';

        // Dados padrão se não houver dados do servidor
        var entradasData = chartData?.entradas || [];
        var saidasData = chartData?.saidas || [];
        var categorias = chartData?.categorias || [];

        console.log('[KTChartsWidgetOverview] Dados processados:', {
            entradas: entradasData.length,
            saidas: saidasData.length,
            categorias: categorias.length
        });

        // Calcular valores máximos e mínimos para o eixo Y
        var allValues = [...entradasData, ...saidasData].filter(v => v > 0);
        var maxValue = allValues.length > 0 ? Math.max(...allValues) : 100;
        var minValue = 0;
        var yAxisMax = maxValue > 0 ? Math.ceil(maxValue * 1.2) : 100;

        var options = {
            series: [{
                name: 'Saídas',
                data: saidasData
            }, {
                name: 'Entradas',
                data: entradasData
            }],
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {

            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.2,
                    stops: [15, 120, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: [basedangerColor, basesuccessColor]
            },
            xaxis: {
                categories: categorias.length > 0 ? categorias : [],
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                tickAmount: 6,
                labels: {
                    rotate: 0,
                    rotateAlways: true,
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: [basedangerColor, basesuccessColor],
                        width: 1,
                        dashArray: 3
                    }
                },
                tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                max: yAxisMax,
                min: minValue,
                tickAmount: 6,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                style: {
                    fontSize: '12px'
                }
            },
            colors: [lightdangerColor, lightsuccessColor],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            markers: {
                strokeColor: [basedangerColor, basesuccessColor],
                strokeWidth: 3
            }
        };

        // Destruir gráfico existente se houver
        if (chart.self) {
            console.log('[KTChartsWidgetOverview] Destruindo gráfico existente...');
            chart.self.destroy();
        }

        try {
            console.log('[KTChartsWidgetOverview] Criando instância do ApexCharts...');
            chart.self = new ApexCharts(element, options);
            console.log('[KTChartsWidgetOverview] Instância do ApexCharts criada com sucesso');

            // Set timeout to properly get the parent elements width
            setTimeout(function() {
                try {
                    console.log('[KTChartsWidgetOverview] Renderizando gráfico...');
                    chart.self.render();
                    chart.rendered = true;
                    console.log('[KTChartsWidgetOverview] Gráfico renderizado com sucesso');
                } catch (renderError) {
                    console.error('[KTChartsWidgetOverview] ERRO ao renderizar gráfico:', renderError);
                    console.error('[KTChartsWidgetOverview] Stack trace:', renderError.stack);
                }
            }, 200);
        } catch (chartError) {
            console.error('[KTChartsWidgetOverview] ERRO ao criar gráfico:', chartError);
            console.error('[KTChartsWidgetOverview] Stack trace:', chartError.stack);
            console.error('[KTChartsWidgetOverview] Opções do gráfico:', options);
        }
    }

    var updateChart = function(startDate, endDate) {
        console.log('[KTChartsWidgetOverview] ========== updateChart chamado ==========');
        console.log('[KTChartsWidgetOverview] Atualizando gráfico com período:', { startDate, endDate });

        if (!startDate || !endDate) {
            console.error('[KTChartsWidgetOverview] ERRO: Datas inválidas', { startDate, endDate });
            return;
        }

        loadChartData(startDate, endDate, function(data) {
            console.log('[KTChartsWidgetOverview] Callback do loadChartData executado');
            console.log('[KTChartsWidgetOverview] Dados recebidos no callback:', data);

            if (!data) {
                console.error('[KTChartsWidgetOverview] ERRO: Nenhum dado recebido para atualizar o gráfico');
                return;
            }

            // Atualizar estatísticas
            if (data.totais) {
                var totalElement = document.getElementById('saldo-periodo');
                if (totalElement) {
                    var saldo = data.totais.saldo || 0;
                    totalElement.textContent = saldo.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    console.log('[KTChartsWidgetOverview] Estatística de saldo atualizada:', saldo);
                } else {
                    console.warn('[KTChartsWidgetOverview] Elemento de saldo não encontrado (#saldo-periodo)');
                }

                // Atualizar badge de variação
                var badgeElement = document.getElementById('percentual-saldo');
                var percentualTexto = document.getElementById('percentual-texto');
                if (badgeElement && percentualTexto) {
                    if (data.totais.entradas > 0) {
                        var percentual = ((data.totais.saldo / data.totais.entradas) * 100).toFixed(1);
                        percentualTexto.textContent = percentual + '%';
                        badgeElement.style.display = 'inline-block';
                        
                        // Mudar cor do badge baseado no saldo (verde se positivo, vermelho se negativo)
                        if (saldo >= 0) {
                            badgeElement.className = 'badge badge-light-success fs-base';
                        } else {
                            badgeElement.className = 'badge badge-light-danger fs-base';
                        }
                        console.log('[KTChartsWidgetOverview] Badge de percentual atualizado:', percentual + '%');
                    } else {
                        badgeElement.style.display = 'none';
                    }
                } else {
                    console.warn('[KTChartsWidgetOverview] Elemento de badge não encontrado');
                }
            } else {
                console.warn('[KTChartsWidgetOverview] Dados de totais não disponíveis');
            }

            // Atualizar gráfico
            initChart(chart, data);
            
            // Atualizar botão "Carregar Mais"
            updateLoadMoreButton(data.paginacao);
        });
    }
    
    // Função para atualizar o botão "Carregar Mais"
    var updateLoadMoreButton = function(paginacao) {
        var container = document.getElementById('chart-load-more-container');
        var button = document.getElementById('chart-load-more-btn');
        var info = document.getElementById('chart-data-info');
        
        if (!container || !button || !info) {
            return;
        }
        
        if (paginacao && paginacao.has_more) {
            container.style.display = 'block';
            button.style.display = 'inline-block';
            var exibindo = paginationState.currentData.categorias ? paginationState.currentData.categorias.length : 0;
            var total = paginacao.total;
            info.textContent = 'Exibindo ' + exibindo + ' de ' + total + ' períodos';
            button.disabled = false;
            button.querySelector('.indicator-label').style.display = 'inline';
            button.querySelector('.indicator-progress').style.display = 'none';
        } else {
            if (paginacao && paginacao.total) {
                container.style.display = 'block';
                info.textContent = 'Todos os ' + paginacao.total + ' períodos foram carregados';
                button.style.display = 'none';
            } else {
                container.style.display = 'none';
            }
        }
    }
    
    // Função para carregar mais dados
    var loadMoreData = function() {
        var button = document.getElementById('chart-load-more-btn');
        if (!button) {
            return;
        }
        
        // Desabilitar botão e mostrar loading
        button.disabled = true;
        button.querySelector('.indicator-label').style.display = 'none';
        button.querySelector('.indicator-progress').style.display = 'inline';
        
        // Obter datas atuais do daterangepicker
        var daterangepickerElement = document.querySelector('[data-kt-daterangepicker="true"]');
        if (!daterangepickerElement || !$(daterangepickerElement).data('daterangepicker')) {
            console.error('[KTChartsWidgetOverview] Daterangepicker não encontrado');
            button.disabled = false;
            button.querySelector('.indicator-label').style.display = 'inline';
            button.querySelector('.indicator-progress').style.display = 'none';
            return;
        }
        
        var picker = $(daterangepickerElement).data('daterangepicker');
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        
        // Carregar mais dados (não resetar paginação)
        loadChartData(startDate, endDate, function(data) {
            if (!data) {
                console.error('[KTChartsWidgetOverview] Erro ao carregar mais dados');
                button.disabled = false;
                button.querySelector('.indicator-label').style.display = 'inline';
                button.querySelector('.indicator-progress').style.display = 'none';
                return;
            }
            
            // Se o gráfico já existe, atualizar apenas as séries
            if (chart.self && chart.rendered) {
                console.log('[KTChartsWidgetOverview] Atualizando gráfico existente com novos dados');
                chart.self.updateSeries([
                    {
                        name: 'Saídas',
                        data: data.saidas
                    },
                    {
                        name: 'Entradas',
                        data: data.entradas
                    }
                ]);
                chart.self.updateOptions({
                    xaxis: {
                        categories: data.categorias
                    }
                });
            } else {
                // Se não existe, criar novo gráfico
                initChart(chart, data);
            }
            
            // Atualizar botão
            updateLoadMoreButton(data.paginacao);
        }, false); // false = não resetar paginação, adicionar aos dados existentes
    }

    // Init group by select
    var initGroupBySelect = function() {
        var groupBySelect = document.getElementById('group-by-select');
        if (groupBySelect) {
            // Inicializar Select2 se disponível
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(groupBySelect).select2({
                    minimumResultsForSearch: Infinity
                });
            }

            // Listener para mudança de agrupamento
            groupBySelect.addEventListener('change', function() {
                console.log('[KTChartsWidgetOverview] Agrupamento alterado para:', this.value);
                
                // Obter datas atuais do daterangepicker
                var daterangepickerElement = document.querySelector('[data-kt-daterangepicker="true"]');
                if (daterangepickerElement && $(daterangepickerElement).data('daterangepicker')) {
                    var picker = $(daterangepickerElement).data('daterangepicker');
                    var startDate = picker.startDate.format('YYYY-MM-DD');
                    var endDate = picker.endDate.format('YYYY-MM-DD');
                    updateChart(startDate, endDate);
                }
            });
        }
    }
    
    // Init load more button
    var initLoadMoreButton = function() {
        var button = document.getElementById('chart-load-more-btn');
        if (button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                loadMoreData();
            });
        }
    }

    // Init daterangepicker
    var initDaterangepicker = function() {
        console.log('[KTChartsWidgetOverview] Inicializando daterangepicker...');

        // Check if jQuery and daterangepicker are available
        if (typeof jQuery == 'undefined') {
            console.error('[KTChartsWidgetOverview] ERRO: jQuery não está carregado');
            console.error('[KTChartsWidgetOverview] Certifique-se de que jQuery está incluído antes deste script');
            return;
        }

        if (typeof $.fn.daterangepicker === 'undefined') {
            console.error('[KTChartsWidgetOverview] ERRO: daterangepicker não está disponível');
            console.error('[KTChartsWidgetOverview] Certifique-se de que a biblioteca daterangepicker está incluída');
            return;
        }

        console.log('[KTChartsWidgetOverview] jQuery e daterangepicker disponíveis');
        console.log('[KTChartsWidgetOverview] Procurando elemento daterangepicker...');

        var element = document.querySelector('[data-kt-daterangepicker="true"]');
        console.log('[KTChartsWidgetOverview] Resultado da busca do elemento:', element);

        if (!element) {
            console.error('[KTChartsWidgetOverview] ERRO: Elemento daterangepicker não encontrado');
            console.error('[KTChartsWidgetOverview] Verifique se existe um elemento com [data-kt-daterangepicker="true"] no HTML');
            console.log('[KTChartsWidgetOverview] Elementos com data-kt-daterangepicker encontrados:',
                Array.from(document.querySelectorAll('[data-kt-daterangepicker]')).map(el => ({
                    id: el.id,
                    classes: el.className,
                    initialized: el.getAttribute('data-kt-initialized')
                }))
            );
            // Mesmo sem daterangepicker, vamos tentar carregar os dados
            console.log('[KTChartsWidgetOverview] Tentando carregar dados sem daterangepicker...');
            var startDate = moment().subtract(29, 'days').format('YYYY-MM-DD');
            var endDate = moment().format('YYYY-MM-DD');
            updateChart(startDate, endDate);
            return;
        }

        console.log('[KTChartsWidgetOverview] Elemento encontrado, verificando se já foi inicializado...');
        var isInitialized = element.getAttribute("data-kt-initialized");
        console.log('[KTChartsWidgetOverview] Status de inicialização:', isInitialized);

        if (isInitialized === "1") {
            console.warn('[KTChartsWidgetOverview] Daterangepicker já foi inicializado anteriormente');

            // Adicionar listener mesmo se já inicializado
            $(element).off('apply.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
                console.log('[KTChartsWidgetOverview] Período alterado no daterangepicker (já inicializado)');
                var startDate = picker.startDate.format('YYYY-MM-DD');
                var endDate = picker.endDate.format('YYYY-MM-DD');
                console.log('[KTChartsWidgetOverview] Novas datas selecionadas:', { startDate, endDate });
                updateChart(startDate, endDate);
            });

            // Tentar obter as datas atuais do daterangepicker
            var pickerInstance = $(element).data('daterangepicker');
            var startDate, endDate;

            if (pickerInstance) {
                startDate = pickerInstance.startDate.format('YYYY-MM-DD');
                endDate = pickerInstance.endDate.format('YYYY-MM-DD');
                console.log('[KTChartsWidgetOverview] Datas do daterangepicker existente:', { startDate, endDate });
            } else {
                // Se não conseguir obter, usar datas padrão baseadas no range
                var range = element.getAttribute('data-kt-daterangepicker-range');
                if (range === "this month") {
                    startDate = moment().startOf('month').format('YYYY-MM-DD');
                    endDate = moment().endOf('month').format('YYYY-MM-DD');
                } else {
                    startDate = moment().subtract(29, 'days').format('YYYY-MM-DD');
                    endDate = moment().format('YYYY-MM-DD');
                }
                console.log('[KTChartsWidgetOverview] Usando datas padrão:', { startDate, endDate });
            }

            updateChart(startDate, endDate);
            return;
        }

        console.log('[KTChartsWidgetOverview] Elemento daterangepicker encontrado:', element);

        var display = element.querySelector('.text-gray-600.fw-bold');
        if (!display) {
            console.warn('[KTChartsWidgetOverview] Elemento de display do daterangepicker não encontrado (.text-gray-600.fw-bold)');
        }

        var attrOpens = element.hasAttribute('data-kt-daterangepicker-opens')
            ? element.getAttribute('data-kt-daterangepicker-opens')
            : 'left';
        var range = element.getAttribute('data-kt-daterangepicker-range');

        console.log('[KTChartsWidgetOverview] Configuração do daterangepicker:', { attrOpens, range });

        var start = moment().subtract(29, 'days');
        var end = moment();

        var cb = function(start, end) {
            var current = moment();

            if (display) {
                if (current.isSame(start, "day") && current.isSame(end, "day")) {
                    display.innerHTML = start.format('D MMM YYYY');
                } else {
                    display.innerHTML = start.format('D MMM YYYY') + ' - ' + end.format('D MMM YYYY');
                }
            }
        }

        if (range === "today") {
            start = moment();
            end = moment();
        } else if (range === "this month") {
            start = moment().startOf('month');
            end = moment().endOf('month');
        }

        $(element).daterangepicker({
            startDate: start,
            endDate: end,
            opens: attrOpens,
            ranges: {
                'Hoje': [moment(), moment()],
                'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                format: "DD/MM/YYYY",
                applyLabel: "Aplicar",
                cancelLabel: "Cancelar",
                fromLabel: "De",
                toLabel: "Até",
                customRangeLabel: "Personalizado",
                weekLabel: "S",
                daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
                monthNames: [
                    "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
                    "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
                ],
                firstDay: 0
            }
        }, cb);

        // Callback quando o período é alterado
        $(element).on('apply.daterangepicker', function(ev, picker) {
            console.log('[KTChartsWidgetOverview] Período alterado no daterangepicker');
            var startDate = picker.startDate.format('YYYY-MM-DD');
            var endDate = picker.endDate.format('YYYY-MM-DD');
            console.log('[KTChartsWidgetOverview] Novas datas selecionadas:', { startDate, endDate });
            updateChart(startDate, endDate);
        });

        try {
            console.log('[KTChartsWidgetOverview] Configurando callback inicial do daterangepicker...');
            console.log('[KTChartsWidgetOverview] Datas iniciais calculadas:', {
                start: start.format('YYYY-MM-DD'),
                end: end.format('YYYY-MM-DD')
            });

            cb(start, end);
            element.setAttribute("data-kt-initialized", "1");
            console.log('[KTChartsWidgetOverview] Daterangepicker inicializado com sucesso');

            // Carregar dados iniciais
            // Moment.js usa 'YYYY' para ano com 4 dígitos, não 'Y'
            var startDate = start.format('YYYY-MM-DD');
            var endDate = end.format('YYYY-MM-DD');
            console.log('[KTChartsWidgetOverview] Carregando dados iniciais:', { startDate, endDate });
            console.log('[KTChartsWidgetOverview] Chamando updateChart...');

            // Usar setTimeout para garantir que o daterangepicker esteja totalmente inicializado
            setTimeout(function() {
                console.log('[KTChartsWidgetOverview] Executando updateChart após timeout...');
                updateChart(startDate, endDate);
            }, 100);
        } catch (daterangeError) {
            console.error('[KTChartsWidgetOverview] ERRO ao inicializar daterangepicker:', daterangeError);
            console.error('[KTChartsWidgetOverview] Stack trace:', daterangeError.stack);

            // Mesmo com erro, tentar carregar os dados
            console.log('[KTChartsWidgetOverview] Tentando carregar dados mesmo com erro no daterangepicker...');
            var startDate = moment().subtract(29, 'days').format('YYYY-MM-DD');
            var endDate = moment().format('YYYY-MM-DD');
            updateChart(startDate, endDate);
        }
    }

    // Public methods
    return {
        init: function () {
            initGroupBySelect();
            initLoadMoreButton();
            initDaterangepicker();

            // Update chart on theme mode change
            if (typeof KTThemeMode !== 'undefined' && typeof KTThemeMode.on === 'function') {
                KTThemeMode.on("kt.thememode.change", function() {
                    console.log('[KTChartsWidgetOverview] Tema alterado, recarregando gráfico...');
                    if (chart.rendered && chart.self) {
                        // Recarregar dados atuais
                        var daterangepickerElement = document.querySelector('[data-kt-daterangepicker="true"]');
                        if (daterangepickerElement && $(daterangepickerElement).data('daterangepicker')) {
                            var picker = $(daterangepickerElement).data('daterangepicker');
                            var startDate = picker.startDate.format('YYYY-MM-DD');
                            var endDate = picker.endDate.format('YYYY-MM-DD');
                            loadChartData(startDate, endDate, function(data) {
                                initChart(chart, data);
                            });
                        } else {
                            initChart(chart);
                        }
                    }
                });
            } else {
                console.warn('[KTChartsWidgetOverview] KTThemeMode não disponível, mudanças de tema não serão detectadas');
            }
        }
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = KTChartsWidgetOverview;
}

// On document ready
if (typeof KTUtil !== 'undefined' && typeof KTUtil.onDOMContentLoaded === 'function') {
    KTUtil.onDOMContentLoaded(function() {
        console.log('[KTChartsWidgetOverview] DOM carregado, verificando elemento...');

        // Só inicializar se o elemento existir
        var element = document.getElementById("kt_charts_widget_overview");
        if (element) {
            console.log('[KTChartsWidgetOverview] Elemento encontrado, inicializando widget...');
            KTChartsWidgetOverview.init();
        } else {
            console.error('[KTChartsWidgetOverview] ERRO: Elemento kt_charts_widget_overview não encontrado no DOM');
            console.error('[KTChartsWidgetOverview] Verifique se o elemento existe no HTML com o ID: kt_charts_widget_overview');
            console.error('[KTChartsWidgetOverview] Elementos disponíveis com "kt_charts" no ID:',
                Array.from(document.querySelectorAll('[id*="kt_charts"]')).map(el => el.id)
            );
        }
    });
} else {
    // Fallback caso KTUtil não esteja disponível
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            var element = document.getElementById("kt_charts_widget_overview");
            if (element) {
                console.log('[KTChartsWidgetOverview] Inicializando via fallback...');
                KTChartsWidgetOverview.init();
            }
        });
    } else {
        var element = document.getElementById("kt_charts_widget_overview");
        if (element) {
            console.log('[KTChartsWidgetOverview] Inicializando via fallback (DOM já carregado)...');
            KTChartsWidgetOverview.init();
        }
    }
}

