# Manual de Uso: Módulo Quote Intelipost
[![logo](https://image.prntscr.com/image/E8AfiBL7RQKKVychm7Aubw.png)](http://www.intelipost.com.br)

## Introdução

O módulo Quote Intelipost é responsável por realizar o cálculo do frete partindo do CEP fornecido pelo cliente final.  
A consulta é feita na [API Intelipost](https://docs.intelipost.com.br/v1/cotacao/criar-cotacao-por-produto).

Com a instalação do módulo, essa funcionalidade estará disponível em 3 páginas:

- Página dos produtos
- Carrinho
- Checkout

Este manual foi divido em três partes:

  - [Instalação](#instalação): Onde você econtrará instruções para instalar nosso módulo.
  - [Configurações](#configurações): Onde você encontrará o caminho para realizar as configurações e explicações de cada uma delas.
  - [Uso](#uso): Onde você encontrará a maneira de utilização de cada uma das funcionalidades.

## Instalação
> É recomendado que você tenha um ambiente de testes para validar alterações e atualizações antes de atualizar sua loja em produção.

> A instalação do módulo é feita utilizando o Composer. Para baixar e instalar o Composer no seu ambiente acesse https://getcomposer.org/download/ e caso tenha dúvidas de como utilizá-lo consulte a [documentação oficial do Composer](https://getcomposer.org/doc/).

Navegue até o diretório raíz da sua instalação do Magento 2 e execute os seguintes comandos:

```
composer require intelipost/magento2-quote   // Faz a requisição do módulo da Intelipost
bin/magento module:enable Intelipost_Quote       // Ativa o módulo
bin/magento setup:upgrade                        // Registra a extensão
bin/magento setup:di:compile                     // Recompila o projeto Magento
```
## Configurações
Para acessar o menu de configurações, basta seguir os seguintes passos:

No menu à esquerda, acessar **Stores** -> **Configuration** -> **Intelipost** -> **Shipping Methods**:

![ac1](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/quote1.gif)

### Intelipost - Cotação
Nesta seção será explicado cada um dos parâmetros disponíveis para configuração:

- **Ativado**: Se o módulo está ativo e deve ser apresentado no front da loja.
- **Nome**: Nome que ficará registrado no pedido no Magento.
- **Título**: Nome que será exibido no checkout ao lado de cada método da Intelipost.
![q1](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/quote1.png)
------------
- **Título customizado para métodos de entrega**: Determinar como os métodos de envio serão exibidos para o cliente final. O primeiro %s será substuído pela descrição do método (exemplo: Expresso). O segundo %s será substituído pelo prazo ou data de entrega (exemplo: 3 dias).
- **Título customizado para entrega no mesmo dia**: Caso algum dos métodos de envio possua entrega com o prazo menor do que 24 horas, o seu título pode ser customizado aqui.
- **Título para Agendado**: Caso o cliente use entrega agendada, determinar o título que será apresentado no checkout da loja.
- **Ordenar Agendado no Final**: Caso o cliente faça use entrega agendada, determinar se ele deve ser exibido como último item.
![q2](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/qt2.png)
------------
- **CEP de origem**: O CEP de onde a entrega será despachada.
- **Unidade de peso**: Determinar a unidade de peso a ser considerada.
- **Atributo para altura**: Selecionar o atributo do pedido que corresponde a altura.
- **Atributo para largura**: Selecionar o atributo do pedido que corresponde a largura.
- **Atributo para comprimento**: Selecionar o atributo do pedido que corresponde ao comprimento.
![q3](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/qt3.png)
------------
- **Valores de contingencia**: Determinar dimensões e peso a serem usados em caso de contingência, isto é, quando o cálculo não puder ser executado ou quando o pedido estiver sem algum desses atributos.
- **Usar Atributo para Categoria**: Selecionar o atributo que corresponde a categoria do produto.
![q4](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/quote3.png)
------------
- **Texto para Frete Grátis**: Inserir o texto a ser apresentado quando um dos métodos de envio for grátis. Caso não preenchido, será exibido R$ 0,00.
![q7](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/quotegratis.png)
------------
- **Tempo de duração do Cache**: Cotações realizadas para o mesmo destino com os mesmos produtos são colocadas em cache, isto é, uma memoria de resposta rápida que armazena o cálculo realizado para evitar requisições desnecessárias.
![q7](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/qt7.png)
------------
- **Mensagem quando não há opções do frete para o CEP destino**: Inserir o texto que será exibido caso não haja abrangência para o CEP do cliente.
- **Mensagem de Área de Risco**: Inserir o texto que será exibido em caso de Área de Risco (Apenas Correios)
![q8](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/quotearea.png)
------------
- **Tabela de contingência**: Neste campo deve ser preenchido o nome da tabela de contingencia em caso de problema na comunicação com a Intelipost. Sugerimos que contate o consultor do projeto para entender as possibilidades.
![q9](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/qt9.png)
------------
- **Calcular Data de Entrega**: Caso esta opção esteja ativa, a data de entrega registrada no pedido será no formato dia-mes-ano e não dias uteis.
- **Adicional para Data de Entrega**: Informar os dias a serem adicionados na data de entrega em todas as cotações.
- **Exibir entrega Agendada somente no Checkout**: Se utilizado, determinar se a Entrega Agendada só aparecerá no Checkout. Outras possíveis
![q10](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/qt12.png)
------------
- **Depurar**: Quando esta configuração estiver ativa, o modelo de Debug da Intelipost será acionado, criando maior número de registros. Só utilizar em ambiente de homologação.
- **Parar quando em Erro**: Em caso de erro durante as cotações, desativar automaticamente o módulo. Esta opção só é interessante se possuir mais de um módulo de cálculo de frete.
- **Valor quando zerado**: Valor de contingência caso o produto esteja zerado.
- **Entrega aplicável para países**: Países que a cotação deve abrangir.
- **Ordenação**: Caso exita algum outro método de envio ativo, essa configuração possibilita escolher em qual ordem o módulo de frete da Intelipost deve se posicionar após a cotação.
![q11](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/quote2.png)

## Uso

Uma vez instalado e configurado, basta ir até a página do produto (carrinho ou checkout), preencher um CEP e clicar no botão **Calcular**.
Neste momento, o disparo da cotação será realizado e, respeitando a configuração feita no módulo e na ferramenta da Intelipost, os valores serão retornados.

![ac1](https://s3.amazonaws.com/email-assets.intelipost.net/integracoes/quot.gif)
