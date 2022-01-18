<?php

class Media {

    private $id_tmdb; //Id numerico que corresponde a essa serie ou filme no the movie db
    private $nome; //Nome da série/filme
    public $sinopse;
    public $ano_lancamento;
    public $poster;
    public $tempo_episodio;
    public $generos;
    public $nota;


    function __construct(){
    
    }

    function addIdTMDB($results/*Array somente os resultados*/){
        $this->id_tmdb = $results[0]->id;
        $this->onAddTMDBId($this->id_tmdb);
    }

    public function setNome($nome){
        $this->nome = $nome;
    }

    public function getNome(){
        return $this->nome;
    }

    public function getInfo(){
        $this->search();
        return array(
            "nome" => $this->getNome(),
            "sinopse" => $this->sinopse,
            "ano_lancamento" => $this->ano_lancamento,
            "poster" => $this->poster,
            "tempo_episodio" => $this->tempo_episodio,
            "generos" => $this->generos,
            "nota" => $this->nota
        );
    }

    private function search(){
        $keys = parse_ini_file("../keys.ini");

        $url = "https://api.themoviedb.org/3/search/";

        if($this instanceof Serie){
            $url .= "tv";
        } else {
            $url .= "movie";
        }

        $url .= "?api_key=" . $keys['api_key'];
        $url .= "&language=pt-BR";
        $url .= "&query=" . urlencode($this->nome);
        $url .= "&include_adult=true";

        $results = json_decode(file_get_contents($url));
        
        $this->addIdTMDB($results->results);
    }
    
    function onAddTMDBId($id){
    }

}

?>