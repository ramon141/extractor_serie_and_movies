<?php


// Configuração para funcionar com o Animes Online CC
require '../extractData/index2.php';

set_time_limit(60 * 10);

$reg_anime = '/<div class="content"><div class="sheader"><div class="poster">\s?<img src="(?\'img_poster\'.+?)".+?<div class="data"><h1>(?\'titulo_anime\'.+?) Todos os Episodios Online<\/h1>.+?<div class="resumotemp"><div class="wp-content"><p>.+?<br \/>\s?(?\'resumo\'.+?)<\/p>.+?<\/div><\/div><\/div>/m';
$reg_info = '/<span class="breadcrumb_last">.+? (?\'nomeEpisodio\'(?:Ova|Episodio) \d+)<\/span>.+?<h1 class="epih1">.+? (?\'episodio\'\d+) Online<\/h1>.+?<p>.+? (?\'temporada\'\d+) ep \d+.+?<\/p>.*?<div class="imgep"><.+?src="(?\'imagem\'.+?)"/m';

$regexLinks = array(
    '/(?<="imagen"><a href=")https:\/\/animesonline\.cc\/episodio\/.+?(?=.+">\<img)\//m', //Pega os links
    '/https:\/\/www\.blogger\.com\/video\.g.+?(?=")/m' //Pega a url do video
);

$regexInfoAnime = array(
    array($reg_anime, 0, REGEX_NOME_MEDIA, "titulo_anime"),
    array($reg_anime, 0, REGEX_POSTER, "img_poster")
);

$regexInfoEpisodios = array(
    array($reg_info, 1, REGEX_IMAGEM_EPISODIO, "imagem"),
    array($reg_info, 1, REGEX_TEMPORADA_EPISODIO, "temporada"),
    array($reg_info, 1, REGEX_NOME_EPISODIO, "nomeEpisodio"),
    array($reg_info, 1, REGEX_NUMERO_EPISODIO, "episodio")
);

$info = new ExtractData2("https://animesonline.cc/anime/no-game-no-life/", $regexLinks, $regexInfoAnime, $regexInfoEpisodios);

echo $info->extract();



// $regexInfoEpisodios = array(
//     array('/(?<=<div class="imgep"><img width="277" height="156" src=").+?(?=")/m', 1, REGEX_IMAGEM_EPISODIO),
//     array('/(?<=<div itemprop="description" class="wp-content"><p>)(?:.+?)(?\'temporada\'\d) ep/m', 1, REGEX_TEMPORADA_EPISODIO, "temporada"),
//     array('/(?:Episodio|Ova) \d+(?=<\/span><\/span><\/span><\/span><\/p><\/div>)/m', 1, REGEX_NOME_EPISODIO),
//     array('/(?<=<div itemprop="description" class="wp-content"><p>).+?ep (?\'episodio\'\d+) HD, Animes Online.<\/p><div class="imgep"/m', 1, REGEX_NUMERO_EPISODIO, "episodio")
// );