                    <!-- Navbar -->
                    <div class="container">
                        <!-- Nav Tabs -->
                        <div class="mb-5 hover-scroll-x">
                            <div class="d-grid">
                                <ul class="nav nav-tabs flex-nowrap text-nowrap">
                                    <!-- Aba de Movimentação -->
                                    <li class="nav-item">
                                        <a class="nav-link  btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                                            data-bs-toggle="tab" href="#kt_tab_pane_movimentacao">
                                            Movimentação
                                        </a>
                                    </li>

                                    <!-- Aba de Conciliações Pendentes -->
                                    <li class="nav-item">
                                        <a class="nav-link active btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                                            data-bs-toggle="tab" href="#kt_tab_pane_conciliacoes">
                                            Conciliações Pendentes
                                            @if ($conciliacoesPendentes->count() > 0)
                                                <span
                                                    class="badge badge-danger">{{ $conciliacoesPendentes->total() }}</span>
                                            @endif
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0"
                                            data-bs-toggle="tab" href="#kt_tab_pane_informacao">
                                            Informações
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Conteúdo das Abas -->
