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
     * 
     * 
     * Temporadas: [
     *      "info":{
     *          id
     *          nome
     *          poster
     *          quant_episodios
     *      },
     *      "episodios": [
     *         {
     *             ?Nome (se for nulo, Nome = Nº Episodio que será comtablilizado pela quantidade de elmentos no array)
     *             ?Imagem (se for nulo, Imagem = poster da classe Media)
     *             ?Data (Se for nulo, Data = null)
     *             Nº Episodio
     *             Nº Temporada
     *             Links:[
     *                 {
     *                     Link,
     *                     Status --> Usar um file_get_contents para saber se o link está online
     *                 }
     *             ]
     *         }
     *      ]
     * ]
     * 
     * 
     * 
     */
    private $temporadas = array();

    public function addEpisodio($nome, $imagem, $data, $numero_episodio, $numero_temporada, $links/*Array com links*/)
    {
        $links = $this->testLinks($links[0]);

        if($this->ano_lancamento == false && $data != false){
            preg_match_all('/(?:20\d\d)|(?:19\d\d)/m', $data . "", $results, PREG_SET_ORDER, 0);
            $this->ano_lancamento = intval($results[0][0]);
        }

        $this->temporadas[$numero_temporada][] = array(
            "nome" => $nome,
            "imagem" => $imagem,
            "data" => $data,
            "numero_episodio" => ($numero_episodio == false) ? count($this->episodios) + 1 : $numero_episodio,
            "numero_temporada" => $numero_temporada,
            "links" => $links
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

    public function getEpisodios(){
        return $this->temporadas;
    }

    function onAddTMDBId($id){
        $keys = parse_ini_file("../keys.ini");
        $url = "https://api.themoviedb.org/3/tv/$id?api_key=".$keys['api_key']."&language=pt-BR";
        $serie = json_decode(file_get_contents($url));

        $array_keys = array_keys($this->temporadas);

        for($i = 0; $i < count($this->temporadas); $i++){
            $this->temporadas[$array_keys[$i]] = array(
                "info" => $this->getInfoSeason($array_keys[$i], $serie->seasons),
                "episodios" => $this->temporadas[$array_keys[$i]]
            );
        }

        if($this->sinopse == false) $this->sinopse = $serie->overview;
        if($this->ano_lancamento == false) $this->ano_lancamento = $serie->first_air_date;
        if($this->poster == false) $this->poster = $serie->backdrop_path;
        if($this->tempo_episodio == false) $this->tempo_episodio = $serie->episode_run_time[0];
        if($this->generos == false) $this->generos = $serie->genres;
        if($this->nota == false) $this->nota = $serie->vote_average;
    }

    function getInfoSeason($i, $seasons){
        foreach($seasons as $season){
            if($i == $season->season_number){
                return array(
                    "nome" => $season->name,
                    "poster" => $season->poster_path,
                    "quant_episodios" => $season->episode_count,
                    "data_inicio_exibicao" => $season->air_date,
                    "sinopse" => $season->overview
                );
            }
        }
    }
}
