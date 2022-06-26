# Extrator de links de filmes e séries
O arquivo `env` já é enviado por na raiz do projeto (ainda que isso não seja uma boa prática).
Para executar o projeto é necessário possuir um `key` no site [The Movie DB API](https://developers.themoviedb.org/3) (para consultar se já possui uma API acesse [Consultar](https://www.themoviedb.org/settings/api)) para extrair as informações que não puderam ser extraídas no processo.

Após conseguir a key necessária basta criar um arquivo com o nome `keys.ini` na pasta `www`, o conteúdo desta será a key obtida no passo anterior, o arquivo derá estar no formato:
`api_key=valor_da_api_key_no_formato_v3_auth`