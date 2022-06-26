<?php


// Configuração para funcionar com o Animes Online CC
require '../extractData/index2.php';

set_time_limit(60 * 10);


$regexLinks = array(
    '/(?<="imagen"><a href=")https:\/\/animesonline\.cc\/episodio\/.+?(?=.+">\<img)\//m', //Pega os links
    '/https:\/\/www\.blogger\.com\/video\.g.+?(?=")/m' //Pega a url do video
);

$regexInfoAnime = array(
    array('/(?<=<div class="data"><h1>).+(?= Todos os Episodios Online<\/h1>)/m',  0/*Número da página*/, REGEX_NOME_MEDIA),
    array('/(?<=<div class="poster"> <img src=").+?(?=")/m', 0/*Número da página*/, REGEX_POSTER)
);

$regexInfoEpisodios = array(
    array('/(?<=<div class="imgep"><img width="277" height="156" src=").+?(?=")/m', 1/*Número da página*/, REGEX_IMAGEM_EPISODIO),
    array('/<div class="se-c">.+?<\/li><\/ul><\/div><\/div>/m', 0/*Número da página*/, REGEX_TEMPORADA_EPISODIO),
    array("", 0, REGEX_DATA_EPISODIO),
    array("", 0, REGEX_NOME_EPISODIO),
    array("", 0, REGEX_NUMERO_EPISODIO)
);

$info = new Class("https://animesonline.cc/anime/no-game-no-life/", $regexLinks, $regexInfoAnime, $regexInfoEpisodios) extends ExtractData2{
    function onGetTemporada($history, $regex, $i){
        preg_match_all($regex, $history[$i][1], $results, PREG_SET_ORDER, 0);

        for($j = 0; $j < count($results); $j++){
            if(strpos($results[$j][0], $history[count($history) - 1][0]) !== false){
                return $j + 1;
            }
        }

        echo "Falha ao obter";
        return null;
    }

    function onGetImagem($history, $regex, $i){
        preg_match_all($regex, $history[$i][1], $results, PREG_SET_ORDER, 0);

        return $results[0][0];
    }

    function onGetNumeroEpisodio($history, $regex, $temporada, $i){
        $url = str_replace("/", "\/", $history[1][0]/*Obtem o HTML da página $i*/);
        $url = str_replace(".", "\.", $url);

        $regex = '/(?<=<div class="numerando">Ep - )\d+(?=<\/div><div class="episodiotitle"> <a href="'. $url .')/m';
        preg_match_all($regex, $history[0][1], $results, PREG_SET_ORDER, 0);

        return intval($results[0][0]);
    }

    function onGetDataEpisodio($history, $regex, $episodio, $temporada, $i){
        $url = str_replace("/", "\/", $history[1][0]/*Obtem o HTML da página $i*/);
        $url = str_replace(".", "\.", $url);
        $regex = '/(?<=<div class="episodiotitle"> <a href="'.$url.'">Episodio '.$episodio.'<\/a> <span class="date">)\w\w\w\. \d\d, \d\d\d\d(?=<\/span>)/m';

        preg_match_all($regex, $history[0][1], $results, PREG_SET_ORDER, 0);
        return $results[0][0];
    }

    function onGetNomeEpisodio($history, $regex, $episodio, $temporada, $i){
        $url = str_replace("/", "\/", $history[1][0]/*Obtem o HTML da página $i*/);
        $url = str_replace(".", "\.", $url);
        $regex = '/(?<=<div class="episodiotitle"> <a href="'.$url.'">).+?(?=<\/a>)/m';

        preg_match_all($regex, $history[0][1], $results, PREG_SET_ORDER, 0);
        return $results[0][0];
    }

};

echo $info->extract();






















// $allRegex = array(
//     '/(?<="imagen"><a href=")https:\/\/animesonline\.cc\/episodio\/.+?(?=.+">\<img)\//m', //Pega os links
//     '/https:\/\/www\.blogger\.com\/video\.g.+?(?=")/m' //Pega a url do video
// );

// $regexInfos = array(
//     '/(?<=<h1>).+?(?= [Tt]odos os [Ee]pis[oó]dios [Oo]nline)/m', //pega o nome do anime
//     '/(?<=class="numerando">Ep - )[0-9]+(?=<\/div>)/m', //Pega o numero do episodio
//     '/<div class="se-c">.+?<\/li><\/ul><\/div><\/div>/m' //pega a temporada
// );

// $regexInfos = array(
//     '/(?<=<h1>).+?(?= [Tt]odos os [Ee]pis[oó]dios [Oo]nline)/m', //pega o nome do anime
//     '/(?<=class="numerando">Ep - )[0-9]+(?=<\/div>)/m', //Pega o numero do episodio
//     '/<div class="se-c">.+?<\/li><\/ul><\/div><\/div>/m' //pega a temporada
// );


// $info = new ExtractData("https://animesonline.cc/anime/no-game-no-life/", $allRegex, $regexInfos);
// echo $info->extractWithJSON();



// require 'extractData/index.php';

// $allRegex = array(
//     '/(?<=<a href=\')https:\/\/animesonline\.org\/episodio\/.+?(?=\'>)/m', //Pega os links
//     '/(?<=<iframe class=\'metaframe rptss\' src=\').+(?=\' frameborder=\'0\' scrolling=\'no\' allow=\'autoplay; encrypted-media\' allowfullscreen><\/iframe>)/m',
// );

// $regexInfos = array(
//     '/(?<=<div class="data"> <h1>).+(?=<\/h1>)/m', //pega o nome do anime
//     '/(?<=\'>Episódio )[0-9]+(?=<\/a>)/m', //Pega o numero do episodio
//     '/<div class=\'se-c\'>.+?<\/li><\/ul><\/div><\/div>/m' //pega a temporada
// );

// $info = new ExtractData("https://animesonline.org/animes/no-game-no-life/", $allRegex, $regexInfos);

// $info2 = new Class("https://animesonline.org/animes/no-game-no-life/", $allRegex, $regexInfos) extends ExtractData{
//     function teste($str){
//         echo "Substituido";
//     }
// };

// $info2->teste("ramon");

// echo $info->extractWithJSON();



// $episodios = array();
// $links = array("link1", "link2");
// $temporada = 1;
// $numeroEpisodio = 10;

// $episodios[] = array("link" => $links, "temporada" => $temporada, "numeroEpisodio" => $numeroEpisodio);


// $links = array("link3", "link4");
// $temporada = 2;
// $numeroEpisodio = 11;

// $episodios[] = array("link" => $links, "temporada" => $temporada, "numeroEpisodio" => $numeroEpisodio);

// echo json_encode($episodios);
