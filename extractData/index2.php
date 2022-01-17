<?php

require '../media/serie.php';

/*Deverá selecionar somente o nome da série*/
const REGEX_NOME_MEDIA         = 0;

/*Somente o texto da sinopse*/
const REGEX_SINOPSE            = 1;

/*Somente o link da imagem do poster do anime*/
const REGEX_POSTER             = 2;

/*Somente o nome do episodio*/
const REGEX_NOME_EPISODIO      = 3;

/*Somente o link da imagem do episodio*/
const REGEX_IMAGEM_EPISODIO    = 4;

/*Seleciona somente o nome ou numero da temporada*/
const REGEX_TEMPORADA_EPISODIO = 5;

/*Seleciona somente o nome ou numero do episodio*/
const REGEX_NUMERO_EPISODIO    = 6;

/*Seleciona somente a data do episódio*/
const REGEX_DATA_EPISODIO      = 7;


class ExtractData2
{

    private string $url;
    private $regexLinks;
    private $regexInfosAnime;
    private $regexInfosEpisodio;
    private $media;

    function __construct($url, $regexLinks, $regexInfosAnime, $regexInfosEpisodio)
    {
        $this->media = new Serie();
        $this->url = $url;
        $this->regexLinks = $regexLinks;
        $this->regexInfosAnime = $regexInfosAnime;
        $this->regexInfosEpisodio = $regexInfosEpisodio;
    }

    function getPage($url)
    {
        return file_get_contents($url);
    }

    function extract()
    {
        $this->extractRecursion(0, $this->url);

        return json_encode(array(
            "info" => $this->media->getInfo(),
            "episodios" => $this->media->getEpisodios()
        ));
    }

    function extractRecursion($i, $url, $htmlHistory = array(), $imagemEpisodio = null, $nomeEpisodio = null, $dataEpisodio = null, $temporadaEpisodio = null, $numeroEpisodio = null)
    {
        $html = $this->getPage($url);
        preg_match_all($this->regexLinks[$i], $html, $results);
        $this->getInfosAnime($i, $html);
        $htmlHistory[] = array($url, $html);

        $imagemEpisodio    = $this->getInfoEpisode($i, $html, REGEX_IMAGEM_EPISODIO);
        $temporadaEpisodio = $this->getInfoEpisode($i, $html, REGEX_TEMPORADA_EPISODIO);
        $numeroEpisodio    = $this->getInfoEpisode($i, $html, REGEX_NUMERO_EPISODIO);
        $dataEpisodio      = $this->getInfoEpisode($i, $html, REGEX_DATA_EPISODIO);
        $nomeEpisodio      = $this->getInfoEpisode($i, $html, REGEX_NOME_EPISODIO);

        if ($i == count($this->regexLinks) - 1) {
            $temporada = $this->onGetTemporada($htmlHistory, $this->getRegex(REGEX_TEMPORADA_EPISODIO), $this->getPositionRegex(REGEX_TEMPORADA_EPISODIO));
            $temporadaEpisodio = ($temporada == null)? $temporadaEpisodio : $temporada;

            $imagem = $this->onGetImagem($htmlHistory, $this->getRegex(REGEX_IMAGEM_EPISODIO), $this->getPositionRegex(REGEX_IMAGEM_EPISODIO));
            $imagemEpisodio = ($imagem == null)? $imagemEpisodio : $imagem;

            $numero = $this->onGetNumeroEpisodio($htmlHistory, $temporadaEpisodio, $this->getRegex(REGEX_NUMERO_EPISODIO), $this->getPositionRegex(REGEX_NUMERO_EPISODIO));
            $numeroEpisodio = ($numero == null)? $numeroEpisodio : $numero;

            $data = $this->onGetDataEpisodio($htmlHistory, $this->getRegex(REGEX_DATA_EPISODIO), $numeroEpisodio, $temporadaEpisodio, $this->getPositionRegex(REGEX_DATA_EPISODIO));
            $dataEpisodio = ($data == null)? $dataEpisodio : $data;

            $nome = $this->onGetNomeEpisodio($htmlHistory, $this->getRegex(REGEX_NOME_EPISODIO), $numeroEpisodio, $temporadaEpisodio, $this->getPositionRegex(REGEX_NOME_EPISODIO));
            $nomeEpisodio = ($nome == null)? $nomeEpisodio : $nome;

            $this->media->addEpisodio(
                $this->onAddNomeEpisodio($nomeEpisodio, $numeroEpisodio, $temporadaEpisodio),
                $this->onAddImagemEpisodio($imagemEpisodio, $numeroEpisodio, $temporadaEpisodio),
                $this->onAddDataEpisodio($data, $numeroEpisodio, $temporadaEpisodio),
                $this->onAddNumeroEpisodio($numeroEpisodio, $temporadaEpisodio),
                $this->onAddTemporada($temporadaEpisodio, $numeroEpisodio),
                $this->onAddLink($results, $numeroEpisodio, $temporadaEpisodio)
            );

            return true;
        }

        foreach ($results[0] as $result) {
            $this->extractRecursion($i + 1, $result, $htmlHistory, $imagemEpisodio, $temporadaEpisodio, $numeroEpisodio);
        }
    }

    function getRegex($name){
        foreach($this->regexInfosEpisodio as $regexInfoEpisodio){
            if($regexInfoEpisodio[2] === $name) return $regexInfoEpisodio[0];
        }
    }

    function getPositionRegex($name){
        foreach($this->regexInfosEpisodio as $regexInfoEpisodio){
            if($regexInfoEpisodio[2] === $name) return $regexInfoEpisodio[1];
        }
    }

    function getInfoEpisode($i, $html, $REGEX_INFO){
        foreach($this->regexInfosEpisodio as $regexInfosEpisodio){
            if($regexInfosEpisodio[2] === $REGEX_INFO && $regexInfosEpisodio[1] === $i){
                if(empty($regexInfosEpisodio[0])) return null;

                preg_match_all($regexInfosEpisodio[0], $html, $results, PREG_SET_ORDER, 0);
                return $this->getResult($results, $regexInfosEpisodio);
            }
        }

        return null;
    }

    function getInfosAnime($i/*Número da página*/, $html)
    {
        foreach ($this->regexInfosAnime as $regexInfo) {
            if ($regexInfo[1] === $i) {
                preg_match_all($regexInfo[0], $html, $results, PREG_SET_ORDER, 0);

                if ($regexInfo[2] === REGEX_NOME_MEDIA) {
                    $this->media->nome = $this->getResult($results, $regexInfo);

                } else if ($regexInfo[2] === REGEX_POSTER) {
                    $this->media->poster = $this->getResult($results, $regexInfo);

                } else if ($regexInfo[2] === REGEX_SINOPSE) {
                    $this->media->sinopse = $this->getResult($results, $regexInfo);
                }
            }
        }
    }

    function getResult($results, $regexInfo){
        if(count($regexInfo) >= 4){
            return $results[0][$regexInfo[3]];
        }

        return $results[0][0];
    }


    /*Funcões que podem ser sobrescreitas*/
    function onGetTemporada($history, $regex, $i){
        return null;
    }

    function onGetImagem($history, $regex, $i){
        return null;
    }

    function onGetNumeroEpisodio($history, $regex, $temporada, $i){
        return null;
    }

    function onGetLink($history, $regex, $episodio, $temporada, $i){
        return null;
    }

    function onGetDataEpisodio($history, $regex, $episodio, $temporada, $i){
        return null;
    }

    function onGetNomeEpisodio($history, $regex, $episodio, $temporada, $i){
        return null;
    }




    function onAddLink($links, $numeroEpisodio, $temporada){
        return $links;
    }

    function onAddNomeEpisodio($nome, $numeroEpisodio, $temporada){
        return $nome;
    }
    
    function onAddImagemEpisodio($imagem, $numeroEpisodio, $temporada){
        return $imagem;
    }

    function onAddDataEpisodio($dataEpisodio, $numeroEpisodio, $temporada){
        return $dataEpisodio;
    }

    function onAddNumeroEpisodio($numeroEpisodio, $temporada){
        return $numeroEpisodio;
    }
    
    function onAddTemporada($temporada, $numeroEpisodio){
        return $temporada;
    }

    
}
