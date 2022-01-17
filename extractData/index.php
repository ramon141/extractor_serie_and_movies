<?php

$texto = "Erro";

class ExtractData{

    public $url;
    public $allRegex;
    public $regexInfos;
    public $info = array();

    private $episodios = array();
    private $quantTemporadas = 0;
    private $quantEpisodios = 0;
    private $primeiroHTML;

    function __construct ($url, $allRegex, $regexInfos){
        $this->allRegex = $allRegex;
        $this->url = $url;
        $this->regexInfos = $regexInfos;
    }

    function getPage($url){
        return file_get_contents($url);
    }

    function extract(){
        $this->extractRecursion(0, $this->url);
        $this->info["quantEpisodios"] = $this->quantEpisodios;
        $this->info["quantTemporadas"] = $this->quantTemporadas;

        $this->testLinks();
        
        $vetor = array(
            "info" => $this->info,
            "episodios" => $this->episodios
        );

        return $vetor;
    }

    private function testLinks(){
        for($i = 0; $i < count($this->episodios); $i++){
            for($j = 0; $j < count($this->episodios[$i]["links"]); $j++){
                file_get_contents($this->episodios[$i]["links"][$j][0]);

                $this->episodios[$i]["links"][$j] = array(
                    "link" => $this->episodios[$i]["links"][$j][0],
                    "status" => $http_response_header[0]
                );
            }

        }
    }

    function extractWithJSON(){
        return json_encode($this->extract());
    }

    function extractRecursion($i, $url){
        $html = $this->getPage($url);   
        preg_match_all($this->allRegex[$i], $html, $results, PREG_SET_ORDER, 0);

        if($i == 0){
            $this->getName($html);
            $this->primeiroHTML = $html;
        }

        if($i == count($this->allRegex) - 1){
            $this->episodios[] = array("links" => $this->matrixToVector($results), "temporada" => $this->getTemporada($url), "episodio" => $this->getNumeroEpisodio());
            return true;
        }

        foreach($results as $result){
            $this->extractRecursion($i + 1, $result[0]);
        }
    }

    function getName($html){
        preg_match_all($this->regexInfos[0], $html, $results, PREG_SET_ORDER, 0);
        $this->info["name"] = $results[0][0];
    }

    function getNumeroEpisodio(){
        preg_match_all($this->regexInfos[1], $this->primeiroHTML, $numeroEpisodio, PREG_SET_ORDER, 0);
        $numeroEpisodio = $numeroEpisodio[$this->quantEpisodios][0];
        $this->quantEpisodios++;
        return intval($numeroEpisodio);
    }

    function getTemporada($url){
        preg_match_all($this->regexInfos[2], $this->primeiroHTML, $numeroTemporada, PREG_SET_ORDER, 0);

        $tamanho = count($numeroTemporada);
        for($i = 0; $i < $tamanho; $i++){
            if(strpos($numeroTemporada[$i][0], $url) !== false){
                $numeroTemporada = $i + 1;
                break;
            }
        }

        $numeroTemporada = intval($numeroTemporada);
        $this->quantTemporadas = ($this->quantTemporadas < $numeroTemporada)? $numeroTemporada : $this->quantTemporadas;
        
        return $numeroTemporada;
    }

    private function matrixToVector($results){
        // $ret = array();

        // foreach ($results as $itens) {
        //     foreach ($itens as $item) {
        //         $ret[] = $item;
        //     }
        // }

        return $results;
    }

}


?>
