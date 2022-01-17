<?php

require 'media.php';

class Serie extends Media
{

    /**
     * Todas os atributos com ? na frente são opcionais
     * Info:[
     *      Nome
     *      Sinopse
     *      Poster
     * ]
     * Episodios[
     *      {
     *          ?Nome (se for nulo, Nome = Nº Episodio que será comtablilizado pela quantidade de elmentos no array)
     *          ?Imagem (se for nulo, Imagem = poster da classe Media)
     *          ?Data (Se for nulo, Data = null)
     *          Nº Episodio
     *          Nº Temporada
     *          Links:[
     *              {
     *                  Link,
     *                  Status --> Usar um file_get_contents para saber se o link está online
     *              }
     *          ]
     *      }
     * ]
     */
    private $episodios = array();

    public function addEpisodio($nome, $imagem, $data, $numero_episodio, $numero_temporada, $links/*Array com links*/)
    {
        $links = $this->testLinks($links[0]);

        if($this->ano_lancamento == false && $data != false){
            preg_match_all('/(?:20\d\d)|(?:19\d\d)/m', $data . "", $results, PREG_SET_ORDER, 0);
            $this->ano_lancamento = intval($results[0][0]);
        }

        $this->episodios[] = array(
            "nome" => $nome,
            "imagem" => $imagem,
            "data" => $data,
            "numero_episodio" => ($numero_episodio == false) ? count($this->episodios) + 1 : $numero_episodio,
            "numero_temporada" => $numero_temporada,
            "links" => $links
        );
    }

    public function getInfo(){
        return array(
            "nome" => $this->nome,
            "sinopse" => $this->sinopse,
            "ano_lancamento" => $this->ano_lancamento,
            "poster" => $this->poster
        );
    }

    private function testLinks($links){
        for($j = 0; $j < count($links); $j++){
            file_get_contents($links[$j]);

            $links[$j] = array(
                "link" => $links[$j],
                "status" => $http_response_header[0]
            );
        }
        return $links;
    }

    public function getEpisodios()
    {
        return $this->episodios;
    }
}
