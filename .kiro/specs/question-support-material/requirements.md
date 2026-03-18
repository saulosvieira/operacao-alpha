# Documento de Requisitos — Material de Apoio da Questão

## Introdução

Esta funcionalidade permite que cada questão do sistema possua, de forma opcional, um texto de apoio e um link público para download de PDF. Esses campos são cadastrados pelo administrador via tela de gerenciamento de questões ou via planilha de importação Excel. Na tela do simulado, o aluno poderá visualizar o texto de apoio em um modal/popup e efetuar o download do PDF, caso os respectivos campos estejam preenchidos.

## Glossário

- **Sistema_Admin**: Interface administrativa (painel admin) utilizada para gerenciar questões, simulados e demais entidades do sistema.
- **Tela_Simulado**: Interface React do aluno onde as questões do simulado são exibidas e respondidas (componente QuestaoCard e página ExecutarSimulado/ExecuteExam).
- **Questão**: Entidade do domínio Exam que representa uma pergunta com enunciado, alternativas, resposta correta e explicação. Modelo `Question`.
- **Material_de_Apoio**: Conjunto opcional de dados associados a uma questão, composto por um texto de apoio (support_text) e/ou um link para download de PDF (support_pdf_url).
- **Modal_Apoio**: Componente de diálogo (popup/modal) exibido na Tela_Simulado contendo o texto de apoio da questão.
- **Botão_Download_PDF**: Botão exibido na Tela_Simulado que permite ao aluno baixar um arquivo PDF a partir do link público cadastrado na questão.
- **Importador_Excel**: Funcionalidade existente que processa planilhas Excel para criar questões em lote no sistema.

## Requisitos

### Requisito 1: Armazenamento dos campos de material de apoio

**User Story:** Como administrador, quero que cada questão possua campos opcionais para texto de apoio e link de PDF, para que eu possa fornecer material complementar aos alunos.

#### Critérios de Aceitação

1. THE Questão SHALL possuir um campo opcional `support_text` do tipo texto longo para armazenar o texto de apoio.
2. THE Questão SHALL possuir um campo opcional `support_pdf_url` do tipo texto para armazenar a URL pública de download do PDF.
3. WHEN o campo `support_text` não for preenchido, THE Questão SHALL armazenar o valor como nulo.
4. WHEN o campo `support_pdf_url` não for preenchido, THE Questão SHALL armazenar o valor como nulo.

### Requisito 2: Cadastro de material de apoio via tela administrativa

**User Story:** Como administrador, quero cadastrar e editar o texto de apoio e o link de PDF diretamente na tela de criação/edição de questões, para que eu possa gerenciar o material de apoio de forma prática.

#### Critérios de Aceitação

1. THE Sistema_Admin SHALL exibir um campo de texto multilinha rotulado "Texto de Apoio" no formulário de criação de questão.
2. THE Sistema_Admin SHALL exibir um campo de texto rotulado "Link do PDF de Apoio" no formulário de criação de questão.
3. THE Sistema_Admin SHALL exibir os campos "Texto de Apoio" e "Link do PDF de Apoio" no formulário de edição de questão, preenchidos com os valores existentes.
4. WHEN o administrador preencher o campo "Texto de Apoio" e salvar a questão, THE Sistema_Admin SHALL persistir o valor no campo `support_text` da Questão.
5. WHEN o administrador preencher o campo "Link do PDF de Apoio" e salvar a questão, THE Sistema_Admin SHALL persistir o valor no campo `support_pdf_url` da Questão.
6. WHEN o administrador informar uma URL inválida no campo "Link do PDF de Apoio", THE Sistema_Admin SHALL exibir uma mensagem de erro de validação indicando que a URL é inválida.
7. THE Sistema_Admin SHALL aceitar que ambos os campos sejam deixados em branco, tratando-os como opcionais.

### Requisito 3: Importação de material de apoio via planilha Excel

**User Story:** Como administrador, quero importar o texto de apoio e o link de PDF via planilha Excel, para que eu possa cadastrar material de apoio em lote junto com as questões.

#### Critérios de Aceitação

1. THE Importador_Excel SHALL reconhecer as colunas `texto_apoio` e `link_pdf_apoio` na planilha de importação.
2. WHEN a planilha contiver a coluna `texto_apoio` preenchida para uma questão, THE Importador_Excel SHALL importar o valor para o campo `support_text` da Questão correspondente.
3. WHEN a planilha contiver a coluna `link_pdf_apoio` preenchida para uma questão, THE Importador_Excel SHALL importar o valor para o campo `support_pdf_url` da Questão correspondente.
4. WHEN as colunas `texto_apoio` e `link_pdf_apoio` estiverem vazias ou ausentes na planilha, THE Importador_Excel SHALL importar a questão normalmente com os campos de material de apoio como nulos.
5. WHEN a coluna `link_pdf_apoio` contiver uma URL inválida, THE Importador_Excel SHALL registrar um aviso no relatório de importação e importar a questão sem o link de PDF.
6. THE Sistema_Admin SHALL exibir as novas colunas `texto_apoio` e `link_pdf_apoio` na tabela de formato esperado da tela de importação.

### Requisito 4: Exibição do modal de texto de apoio na tela do simulado

**User Story:** Como aluno, quero visualizar o texto de apoio de uma questão em um popup/modal, para que eu possa consultar material complementar durante o simulado.

#### Critérios de Aceitação

1. WHEN a Questão possuir o campo `support_text` preenchido, THE Tela_Simulado SHALL exibir um botão rotulado "Material de Apoio" no componente da questão.
2. WHEN o aluno clicar no botão "Material de Apoio", THE Tela_Simulado SHALL abrir o Modal_Apoio contendo o texto de apoio da questão.
3. THE Modal_Apoio SHALL exibir o texto de apoio completo com formatação legível.
4. THE Modal_Apoio SHALL possuir um botão ou ação para fechar o modal e retornar à questão.
5. WHEN a Questão não possuir o campo `support_text` preenchido, THE Tela_Simulado SHALL ocultar o botão "Material de Apoio".

### Requisito 5: Exibição do botão de download de PDF na tela do simulado

**User Story:** Como aluno, quero baixar um PDF de apoio sobre o tópico da questão, para que eu possa estudar o conteúdo complementar.

#### Critérios de Aceitação

1. WHEN a Questão possuir o campo `support_pdf_url` preenchido, THE Tela_Simulado SHALL exibir um botão rotulado "Baixar PDF" no componente da questão.
2. WHEN o aluno clicar no botão "Baixar PDF", THE Tela_Simulado SHALL iniciar o download do arquivo PDF a partir da URL pública cadastrada, abrindo em uma nova aba do navegador.
3. WHEN a Questão não possuir o campo `support_pdf_url` preenchido, THE Tela_Simulado SHALL ocultar o botão "Baixar PDF".
4. IF a URL do PDF retornar um erro de acesso (404, 403 ou similar), THEN THE Tela_Simulado SHALL exibir uma mensagem informando que o arquivo não está disponível no momento.

### Requisito 6: Entrega dos dados de material de apoio pela API

**User Story:** Como desenvolvedor frontend, quero que a API retorne os campos de material de apoio junto com os dados da questão, para que o frontend possa renderizar os componentes condicionalmente.

#### Critérios de Aceitação

1. WHEN a API retornar os dados de uma questão para a Tela_Simulado, THE Sistema SHALL incluir os campos `supportText` e `supportPdfUrl` no payload da questão.
2. WHEN o campo `support_text` da Questão for nulo, THE Sistema SHALL retornar `supportText` como `null` no payload.
3. WHEN o campo `support_pdf_url` da Questão for nulo, THE Sistema SHALL retornar `supportPdfUrl` como `null` no payload.
