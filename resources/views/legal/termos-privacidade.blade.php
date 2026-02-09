<!DOCTYPE html>
<html lang="pt_BR">
<!--begin::Head-->
<head>
    <base href="../../../" />
    <title>Termos de Uso e Política de Privacidade - {{ config('app.name', 'Dominus') }}</title>
    <meta charset="utf-8" />
    <meta name="description" content="Termos de Uso e Política de Privacidade do Sistema Dominus" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ url('tenancy/assets/media/app/mini-logo.svg') }}" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="/tenancy/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/tenancy/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <style>
        .terms-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .terms-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #e4e6ef;
        }
        .terms-section {
            margin-bottom: 2.5rem;
        }
        .terms-section h2 {
            color: #009ef7;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-top: 1rem;
        }
        .terms-section h3 {
            color: #181c32;
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .terms-section p {
            color: #5e6278;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .terms-section ul, .terms-section ol {
            color: #5e6278;
            line-height: 1.8;
            margin-bottom: 1rem;
            padding-left: 2rem;
        }
        .terms-section li {
            margin-bottom: 0.5rem;
        }
        .terms-footer {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #e4e6ef;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            color: #009ef7;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="bg-body">
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid">
            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Container-->
                    <div class="container-xxl" id="kt_content_container">
                        <div class="terms-container">
                            <a href="{{ route('login') }}" class="back-link">
                                <i class="fa-solid fa-arrow-left me-2"></i>Voltar ao Login
                            </a>

                            <div class="terms-header">
                                <h1 class="fw-bold text-gray-800 mb-3">TERMOS DE USO E POLÍTICA DE PRIVACIDADE</h1>
                                <p class="text-muted">Última atualização: {{ date('d/m/Y') }}</p>
                            </div>

                            <div class="card shadow-sm">
                                <div class="card-body p-10">
                                    <!-- Seção 1: Introdução -->
                                    <div class="terms-section">
                                        <h2>1. INTRODUÇÃO</h2>
                                        <p>
                                            <strong>DOMINUS SISTEMA ECLESIAL</strong>, sociedade empresária com sede no Brasil,
                                            responsável legal pelo tratamento e pela proteção dos dados pessoais transitados na
                                            plataforma eletrônica mantida e controlada pela Dominus (conjuntamente denominada apenas
                                            "Plataforma Dominus").
                                        </p>
                                        <p>
                                            Esta política de privacidade ("Política de Privacidade") define regras, princípios e
                                            diretrizes do compromisso da Dominus com a segurança e a privacidade dos dados pessoais dos
                                            Usuários da Plataforma Dominus, e descreve como, quais e os motivos pelos quais os dados
                                            pessoais são tratados e a finalidade destes tratamentos, além de definir como os Usuários
                                            podem exercer seus direitos previstos na legislação aplicável.
                                        </p>
                                        <p>
                                            Ao clicar no botão indicado para aceitar essa Política ou ao utilizar a Plataforma Dominus,
                                            você, Usuário, concorda com o tratamento dos seus dados pessoais na forma descrita nesta
                                            Política, requisito necessário para a prestação dos serviços Dominus.
                                            <strong>Caso NÃO concorde com todos os termos dessa Política de Privacidade, você NÃO deverá
                                            acessar, utilizar, permanecer na Plataforma Dominus ou contratar qualquer serviço disponibilizado
                                            pela Dominus.</strong>
                                        </p>
                                    </div>

                                    <!-- Seção 2: Definições -->
                                    <div class="terms-section">
                                        <h2>2. DEFINIÇÕES</h2>
                                        <p><strong>"Clientes"</strong> são as pessoas jurídicas ou físicas contratantes dos produtos e serviços Dominus.</p>
                                        <p><strong>"Cookies"</strong> são arquivos que armazenam temporariamente identificação do Usuário na plataforma, usados, por exemplo, para oferta de conteúdo personalizado.</p>
                                        <p><strong>"Dados Pessoais"</strong> são definidos de forma ampla e incluem quaisquer informações a partir das quais é possível identificar uma pessoa natural, direta ou indiretamente, tais como nome, endereço de e-mail, foto, telefone, CPF, número de CNH, número do equipamento, ou endereço de IP.</p>
                                        <p><strong>"Dado pessoal sensível"</strong> é o dado pessoal sobre origem racial ou étnica, convicção religiosa, opinião política, filiação a sindicato ou organização de caráter religioso, filosófico ou político, dado referente à saúde ou à vida sexual, dado genético ou biométrico, quando vinculado a uma pessoa natural.</p>
                                        <p><strong>"Legislação aplicável à Proteção de Dados"</strong> é qualquer norma legal ou regulatória que disponha sobre questões relacionadas à Privacidade e à Proteção de Dados Pessoais, incluindo, mas não se limitando: (i) a Lei 13.709 de 14 de agosto de 2018 (Lei Geral de Proteção de Dados); (ii) a Lei 12.965 de 23 de abril de 2014 (Marco Civil da Internet); (iii) a Lei Complementar 105, de 10 de janeiro de 2001 (Lei do Sigilo Bancário), dentre outras.</p>
                                        <p><strong>"Plataforma"</strong> são aplicações de internet que permitem o uso, de qualquer forma, de funcionalidades oferecidas pela Dominus.</p>
                                        <p><strong>"Usuários"</strong> são quaisquer empregados, diretores, representantes legais ou pessoas relacionadas que fazem uso da Plataforma em nome do Cliente.</p>
                                    </div>

                                    <!-- Seção 3: Dados Coletados -->
                                    <div class="terms-section">
                                        <h2>3. DADOS COLETADOS</h2>
                                        <p>A Dominus coleta e trata os seguintes dados pessoais dos Usuários:</p>
                                        <h3>3.1. Dados de Cadastro</h3>
                                        <ul>
                                            <li>Nome completo</li>
                                            <li>Endereço de e-mail</li>
                                            <li>Telefone</li>
                                            <li>CPF</li>
                                            <li>Data de nascimento</li>
                                            <li>Endereço residencial</li>
                                        </ul>
                                        <h3>3.2. Dados de Uso da Plataforma</h3>
                                        <ul>
                                            <li>Logs de acesso</li>
                                            <li>Endereço IP</li>
                                            <li>Informações do dispositivo</li>
                                            <li>Histórico de navegação</li>
                                            <li>Cookies e tecnologias similares</li>
                                        </ul>
                                        <h3>3.3. Dados Financeiros</h3>
                                        <ul>
                                            <li>Informações bancárias (quando necessário para processamento de pagamentos)</li>
                                            <li>Histórico de transações</li>
                                            <li>Documentos fiscais e contábeis</li>
                                        </ul>
                                    </div>

                                    <!-- Seção 4: Finalidades do Tratamento -->
                                    <div class="terms-section">
                                        <h2>4. FINALIDADES DO TRATAMENTO</h2>
                                        <p>Os dados pessoais coletados são utilizados para as seguintes finalidades:</p>
                                        <ul>
                                            <li>Prestação dos serviços contratados</li>
                                            <li>Autenticação e controle de acesso à plataforma</li>
                                            <li>Comunicação com o usuário sobre serviços e atualizações</li>
                                            <li>Melhoria e desenvolvimento de novos produtos e serviços</li>
                                            <li>Cumprimento de obrigações legais e regulatórias</li>
                                            <li>Prevenção de fraudes e segurança da informação</li>
                                            <li>Análise estatística e geração de relatórios</li>
                                            <li>Personalização da experiência do usuário</li>
                                        </ul>
                                    </div>

                                    <!-- Seção 5: Compartilhamento de Dados -->
                                    <div class="terms-section">
                                        <h2>5. COMPARTILHAMENTO DE DADOS</h2>
                                        <p>A Dominus poderá compartilhar dados pessoais nas seguintes situações:</p>
                                        <ul>
                                            <li>Com prestadores de serviços que auxiliam na operação da plataforma (sob obrigação de confidencialidade)</li>
                                            <li>Com autoridades públicas quando exigido por lei ou ordem judicial</li>
                                            <li>Em caso de fusão, aquisição ou venda de ativos da empresa</li>
                                            <li>Com o consentimento expresso do usuário</li>
                                        </ul>
                                        <p>
                                            <strong>Não vendemos, alugamos ou comercializamos dados pessoais de nossos usuários para terceiros
                                            para fins de marketing ou publicidade.</strong>
                                        </p>
                                    </div>

                                    <!-- Seção 6: Segurança dos Dados -->
                                    <div class="terms-section">
                                        <h2>6. SEGURANÇA DOS DADOS</h2>
                                        <p>
                                            A Dominus emprega medidas técnicas e organizacionais adequadas para proteger os dados pessoais
                                            contra acesso não autorizado, alteração, divulgação ou destruição, incluindo:
                                        </p>
                                        <ul>
                                            <li>Criptografia de dados sensíveis</li>
                                            <li>Controles de acesso baseados em permissões</li>
                                            <li>Monitoramento contínuo de segurança</li>
                                            <li>Backup regular dos dados</li>
                                            <li>Treinamento de equipe em segurança da informação</li>
                                        </ul>
                                        <p>
                                            No entanto, por mais que a Dominus empregue seus melhores esforços para garantia da segurança
                                            da informação, não é possível garantir a não ocorrência de invasões, vazamentos de dados e
                                            demais eventos relacionados à segurança cibernética. Nesse caso, eventuais incidentes serão
                                            tratados na forma prevista em lei.
                                        </p>
                                    </div>

                                    <!-- Seção 7: Retenção de Dados -->
                                    <div class="terms-section">
                                        <h2>7. RETENÇÃO E EXCLUSÃO DE DADOS</h2>
                                        <p>
                                            Os dados pessoais tratados serão mantidos em ambientes seguros e controlados segundo as melhores
                                            práticas de privacidade e segurança da informação, enquanto (i) a relação contratual for mantida,
                                            (ii) a exclusão não for solicitada por você e/ou (iii) eles não puderem ser eliminados por serem
                                            necessários para o cumprimento de uma obrigação legal ou para a formulação, exercício e defesa de
                                            reivindicações.
                                        </p>
                                        <p>
                                            <strong>Se você revogar seu consentimento ou exercer o direito de exclusão (quando legalmente
                                            aplicável), seus dados pessoais serão mantidos bloqueados durante os prazos legalmente estabelecidos
                                            para atender às possíveis responsabilidades decorrentes de seu tratamento.</strong>
                                        </p>
                                        <p>
                                            Em alguns casos, ainda que o Cliente ou o Usuário requisite a exclusão de dados pessoais e informações
                                            ou caso o Cliente deixe de ser cliente Dominus, a Dominus poderá manter armazenados os dados tratados
                                            por um período adicional para cumprimento de dever legal ou exercício legítimo de direitos, o que
                                            pode incluir, por exemplo:
                                        </p>
                                        <ul>
                                            <li>Manutenção de informações de acesso a aplicações Dominus na forma da Lei n. 12.965/2014 (Marco Civil da Internet)</li>
                                            <li>Manutenção de dados cadastrais e transacionais conforme determinado por órgãos reguladores</li>
                                            <li>Documentos, informações e dados pessoais úteis à preservação de direitos da Dominus em processos judiciais e/ou administrativos</li>
                                            <li>Demais obrigações legais de guarda e retenção de dados</li>
                                        </ul>
                                        <p>
                                            <strong>Caso você solicite a exclusão de seus dados pessoais, por favor, saiba que algumas funcionalidades
                                            da Plataforma Dominus podem ficar indisponíveis.</strong> Caso você decida por acessar ou de qualquer forma
                                            interagir novamente com a Plataforma Dominus após a solicitação da exclusão dos seus dados, seus dados
                                            poderão ser objeto de uma nova coleta.
                                        </p>
                                    </div>

                                    <!-- Seção 8: Direitos do Titular -->
                                    <div class="terms-section">
                                        <h2>8. DIREITOS DO TITULAR DOS DADOS PESSOAIS</h2>
                                        <p>
                                            O Usuário poderá exercer os direitos previstos na legislação brasileira referente a Proteção de Dados
                                            Pessoais e nesta Política de Privacidade mediante o envio de solicitação ao nosso Encarregado de Proteção
                                            de Dados. Os direitos do Usuário sobre seus dados pessoais incluem, entre outros, o direito de solicitar:
                                        </p>
                                        <ul>
                                            <li>Confirmação de tratamento de dados pessoais</li>
                                            <li>Acesso aos seus dados pessoais</li>
                                            <li>Correção de dados pessoais incompletos, inexatos ou desatualizados</li>
                                            <li>Anonimização, bloqueio ou eliminação de dados pessoais que sejam desnecessários, excessivos ou tratados em desconformidade com a LGPD</li>
                                            <li>Portabilidade dos dados (nos casos em que for possível e conforme regulamentação)</li>
                                            <li>Exclusão dos dados pessoais tratados com base em seu consentimento, observadas as restrições legais</li>
                                            <li>Informação das entidades públicas e privadas com as quais o controlador realizou uso compartilhado de dados</li>
                                            <li>Informação sobre a possibilidade de não fornecer consentimento e sobre as consequências da negativa</li>
                                            <li>Revogação do consentimento, nos termos da Lei</li>
                                        </ul>
                                    </div>

                                    <!-- Seção 9: Cookies -->
                                    <div class="terms-section">
                                        <h2>9. COOKIES E TECNOLOGIAS SIMILARES</h2>
                                        <p>
                                            A Plataforma Dominus utiliza cookies e tecnologias similares para melhorar a experiência do usuário,
                                            analisar o uso da plataforma e personalizar conteúdo. Os cookies são pequenos arquivos de texto armazenados
                                            no dispositivo do usuário.
                                        </p>
                                        <p>Tipos de cookies utilizados:</p>
                                        <ul>
                                            <li><strong>Cookies Essenciais:</strong> Necessários para o funcionamento básico da plataforma</li>
                                            <li><strong>Cookies de Desempenho:</strong> Coletam informações sobre como os usuários utilizam a plataforma</li>
                                            <li><strong>Cookies de Funcionalidade:</strong> Permitem que a plataforma lembre de escolhas do usuário</li>
                                        </ul>
                                        <p>
                                            Você pode gerenciar ou desabilitar cookies através das configurações do seu navegador. No entanto,
                                            desabilitar cookies pode afetar a funcionalidade da plataforma.
                                        </p>
                                    </div>

                                    <!-- Seção 10: Alterações -->
                                    <div class="terms-section">
                                        <h2>10. ALTERAÇÕES A ESTA POLÍTICA DE PRIVACIDADE</h2>
                                        <p>
                                            A Dominus poderá alterar esta Política de Privacidade a qualquer tempo e a seu exclusivo critério,
                                            e as alterações serão válidas e vinculantes após a divulgação na Plataforma Dominus ou após informada
                                            ao usuário por outro meio legítimo. Ao continuar usando os produtos e serviços Dominus após a alteração,
                                            o Cliente e o Usuário têm ciência e concordam com os novos termos e condições.
                                        </p>
                                        <p>
                                            Caso as mudanças envolvam alterações nas práticas relacionadas a tratamento de dados pessoais que dependam
                                            do consentimento do Usuário, a Dominus solicitará a sua concordância expressa e específica com os novos
                                            termos desta Política de Privacidade.
                                        </p>
                                    </div>

                                    <!-- Seção 11: Contato -->
                                    <div class="terms-section">
                                        <h2>11. CONTATO E ENCARREGADO DE PROTEÇÃO DE DADOS</h2>
                                        <p>
                                            Dúvidas ou esclarecimentos sobre a Política de Privacidade ou sobre as práticas de tratamento de dados
                                            pessoais realizadas pela Dominus poderão ser direcionadas ao Encarregado de Proteção de Dados (DPO) por meio de:
                                        </p>
                                        <ul>
                                            <li><strong>E-mail:</strong> privacidade@dominus.com.br</li>
                                            <li><strong>Telefone:</strong> (11) 0000-0000</li>
                                            <li><strong>Endereço:</strong> [Endereço da empresa]</li>
                                        </ul>
                                    </div>

                                    <!-- Seção 12: Lei Aplicável -->
                                    <div class="terms-section">
                                        <h2>12. LEI APLICÁVEL E FORO</h2>
                                        <p>
                                            Esta Política de Privacidade é regida pela legislação brasileira, em especial pela Lei Geral de Proteção
                                            de Dados (Lei 13.709/2018) e pelo Marco Civil da Internet (Lei 12.965/2014).
                                        </p>
                                        <p>
                                            Qualquer controvérsia decorrente desta Política será resolvida pelo foro da comarca de [Cidade/Estado],
                                            renunciando as partes a qualquer outro, por mais privilegiado que seja.
                                        </p>
                                    </div>

                                    <div class="terms-footer">
                                        <div class="text-muted fs-7">
                                            <p class="mb-1 fw-bold">Dominus Tecnologia</p>
                                            <p class="mb-1">
                                                CNPJ: 60.571.888/0001-44 <span class="mx-1">-</span> Razão Social: Jose Thiago Pereira de Oliveira
                                            </p>
                                            <p class="mt-4 mb-0">
                                                © {{ date('Y') }} Dominus. Todos os direitos reservados.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "/tenancy/assets/";
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="/tenancy/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/tenancy/assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--end::Javascript-->
</body>
<!--end::Body-->
</html>

