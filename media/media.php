<?php

class Media {

    private $id_tmdb; //Id numerico que corresponde a essa serie ou filme no the movie db
    public $nome; //Nome da série/filme
    public $sinopse;
    public $ano_lancamento;
    public $poster;

    function __construct(){
    
    }

    function addIdTMDB($results/*Array somente dos resultados*/){
        $this->id_tmdb = $results[0]["id"];
    }
    
}

?>