# Documento de Requisitos — Painel Admin de Simulados Respondidos

## Introdução

Este documento descreve os requisitos para a criação de uma visão administrativa dedicada aos simulados respondidos pelos usuários. A equipe de operações precisa visualizar todas as tentativas realizadas, consultar notas e desempenho, e gerenciar reclamações de usuários sobre erros em questões ou outros problemas. O sistema já possui o domínio Exam com os modelos Attempt, ExamResult, UserAnswer, Question e Exam, além de um painel admin funcional com AdminLTE.

## Glossário

- **Sistema**: A aplicação Laravel que gerencia simulados, questões, carreiras e assinaturas.
- **Painel_Admin**: Interface web administrativa do Sistema, acessível apenas por Administradores autenticados, construída com AdminLTE.
- **Administrador**: Usuário com papel "admin" que tem acesso ao Painel_Admin.
- **Simulado**: Prova/exame cadastrado no Sistema (modelo Exam), composto por questões de múltipla escolha.
- **Tentativa**: Registro de uma sessão de resolução de um Simulado por um Usuário (modelo Attempt), contendo data de início, data de término, duração, acertos e nota.
- **Resultado**: Registro consolidado do desempenho de um Usuário em uma Tentativa (modelo ExamResult), contendo nota, total de questões, acertos e tempo total.
- **Resposta_Usuário**: Registro individual de cada resposta dada pelo Usuário a uma questão durante uma Tentativa (modelo UserAnswer), contendo a opção selecionada e se está correta.
- **Questão**: Pergunta de múltipla escolha pertencente a um Simulado (modelo Question), com enunciado, cinco alternativas (A-E), resposta correta e explicação.
- **Usuário**: Pessoa cadastrada no Sistema que realiza Simulados.
- **Reclamação**: Registro de contestação ou relato de problema feito por um Administrador sobre uma Questão específica, contendo tipo do problema, descrição, status e resolução.
- **Página_Tentativas**: Página no Painel_Admin que lista todas as Tentativas realizadas pelos Usuários.
- **Página_Detalhe_Tentativa**: Página no Painel_Admin que exibe os detalhes completos de uma Tentativa específica, incluindo cada Resposta_Usuário.
- **Página_Reclamações**: Página no Painel_Admin que lista e gerencia Reclamações sobre Questões.

## Requisitos

### Requisito 1: Listagem de Tentativas Realizadas

**User Story:** Como um Administrador, eu quero visualizar todas as tentativas de simulados realizadas pelos usuários, para que eu possa acompanhar o uso da plataforma e o desempenho geral.

#### Critérios de Aceitação

1. THE Página_Tentativas SHALL ser acessível apenas por Administradores autenticados no Painel_Admin.
2. THE Página_Tentativas SHALL exibir uma lista paginada de Tentativas ordenadas por data de finalização decrescente.
3. THE Página_Tentativas SHALL exibir para cada Tentativa: identificador da Tentativa, nome do Usuário, título do Simulado, nome da Carreira associada, nota obtida, quantidade de acertos, total de questões, duração em minutos e data de finalização.
4. WHEN o Administrador utiliza o campo de busca, THE Página_Tentativas SHALL filtrar as Tentativas pelo nome do Usuário ou pelo título do Simulado.
5. WHERE o Administrador utiliza o filtro por Simulado, THE Página_Tentativas SHALL exibir apenas as Tentativas do Simulado selecionado.
6. WHERE o Administrador utiliza o filtro por Carreira, THE Página_Tentativas SHALL exibir apenas as Tentativas de Simulados pertencentes à Carreira selecionada.
7. WHERE o Administrador utiliza o filtro por período de datas, THE Página_Tentativas SHALL exibir apenas as Tentativas finalizadas dentro do período selecionado.
8. THE Página_Tentativas SHALL exibir apenas Tentativas que possuem data de finalização registrada.

### Requisito 2: Detalhe de uma Tentativa

**User Story:** Como um Administrador, eu quero visualizar os detalhes completos de uma tentativa específica, para que eu possa analisar o desempenho do usuário questão a questão e identificar possíveis problemas.

#### Critérios de Aceitação

1. WHEN o Administrador clica em uma Tentativa na Página_Tentativas, THE Sistema SHALL exibir a Página_Detalhe_Tentativa correspondente.
2. THE Página_Detalhe_Tentativa SHALL exibir as informações resumidas da Tentativa: nome do Usuário, e-mail do Usuário, título do Simulado, Carreira, nota, acertos, total de questões, duração e data de finalização.
3. THE Página_Detalhe_Tentativa SHALL exibir a lista de todas as Respostas_Usuário da Tentativa, contendo para cada uma: número da questão, enunciado resumido da questão, opção selecionada pelo Usuário, resposta correta da questão e indicação visual se a resposta está correta ou incorreta.
4. THE Página_Detalhe_Tentativa SHALL destacar visualmente as Respostas_Usuário incorretas com cor diferenciada das corretas.
5. WHEN o Administrador clica em uma Resposta_Usuário na lista, THE Página_Detalhe_Tentativa SHALL expandir e exibir o enunciado completo da Questão, todas as alternativas e a explicação da resposta correta.

### Requisito 3: Estatísticas por Simulado

**User Story:** Como um Administrador, eu quero visualizar estatísticas agregadas de cada simulado, para que eu possa avaliar a qualidade das provas e identificar questões problemáticas.

#### Critérios de Aceitação

1. THE Página_Tentativas SHALL exibir um painel de estatísticas resumidas contendo: total de Tentativas finalizadas, nota média geral, taxa média de acerto e tempo médio de resolução.
2. WHEN o Administrador aplica filtros na Página_Tentativas, THE Sistema SHALL recalcular as estatísticas resumidas considerando apenas as Tentativas filtradas.
3. WHEN o Administrador acessa a Página_Detalhe_Tentativa, THE Sistema SHALL exibir a taxa de acerto de cada Questão do Simulado calculada com base em todas as Tentativas finalizadas daquele Simulado.
4. THE Página_Detalhe_Tentativa SHALL ordenar as Questões por número da questão e indicar visualmente as Questões com taxa de acerto inferior a 30 por cento como potencialmente problemáticas.

### Requisito 4: Gestão de Reclamações sobre Questões

**User Story:** Como um Administrador, eu quero registrar e gerenciar reclamações sobre questões de simulados, para que eu possa rastrear problemas reportados por usuários e garantir a qualidade do conteúdo.

#### Critérios de Aceitação

1. WHEN o Administrador visualiza uma Questão na Página_Detalhe_Tentativa, THE Sistema SHALL exibir um botão para registrar uma Reclamação sobre a Questão.
2. WHEN o Administrador registra uma Reclamação, THE Sistema SHALL solicitar: tipo do problema (gabarito incorreto, enunciado ambíguo, questão desatualizada, erro de formatação, outro), descrição detalhada do problema e prioridade (baixa, média, alta).
3. THE Sistema SHALL persistir a Reclamação associada à Questão, ao Administrador que a registrou e à data de criação.
4. THE Página_Reclamações SHALL ser acessível pelo Painel_Admin e exibir uma lista paginada de Reclamações ordenadas por data de criação decrescente.
5. THE Página_Reclamações SHALL exibir para cada Reclamação: identificador, título do Simulado, número da questão, tipo do problema, prioridade, status (aberta, em análise, resolvida, rejeitada), Administrador responsável e data de criação.
6. WHERE o Administrador utiliza o filtro por status, THE Página_Reclamações SHALL exibir apenas as Reclamações com o status selecionado.
7. WHERE o Administrador utiliza o filtro por tipo de problema, THE Página_Reclamações SHALL exibir apenas as Reclamações do tipo selecionado.
8. WHERE o Administrador utiliza o filtro por prioridade, THE Página_Reclamações SHALL exibir apenas as Reclamações com a prioridade selecionada.
9. WHEN o Administrador atualiza o status de uma Reclamação, THE Sistema SHALL registrar a data da atualização e permitir a inclusão de uma nota de resolução.
10. WHEN o Administrador resolve uma Reclamação com tipo "gabarito incorreto", THE Sistema SHALL exibir um link direto para a página de edição da Questão no Painel_Admin.

### Requisito 5: Exportação de Dados de Tentativas

**User Story:** Como um Administrador, eu quero exportar os dados de tentativas para análise externa, para que eu possa gerar relatórios detalhados fora do sistema.

#### Critérios de Aceitação

1. THE Página_Tentativas SHALL exibir um botão de exportação para o formato CSV.
2. WHEN o Administrador clica no botão de exportação, THE Sistema SHALL gerar um arquivo CSV contendo as Tentativas atualmente filtradas na Página_Tentativas.
3. THE Sistema SHALL incluir no arquivo CSV os campos: identificador da Tentativa, nome do Usuário, e-mail do Usuário, título do Simulado, Carreira, nota, acertos, total de questões, duração em minutos e data de finalização.
4. IF a quantidade de Tentativas a exportar excede 10000 registros, THEN THE Sistema SHALL exibir uma mensagem informando o Administrador e solicitar confirmação antes de prosseguir.

### Requisito 6: Indicadores de Reclamações na Listagem

**User Story:** Como um Administrador, eu quero ver indicadores visuais de reclamações pendentes diretamente na listagem de tentativas, para que eu possa priorizar a análise de simulados com problemas reportados.

#### Critérios de Aceitação

1. THE Página_Tentativas SHALL exibir um indicador visual ao lado do título do Simulado quando o Simulado possui Reclamações com status "aberta" ou "em análise".
2. WHEN o Administrador passa o cursor sobre o indicador de Reclamação, THE Sistema SHALL exibir a quantidade de Reclamações pendentes do Simulado.
3. THE Página_Detalhe_Tentativa SHALL exibir um indicador visual ao lado de cada Questão que possui Reclamações com status "aberta" ou "em análise".
