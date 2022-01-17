<?php


// Configuração para funcionar com o Animes Online CC
require '../extractData/index2.php';

set_time_limit(60 * 10);

$reg_anime = '/<div class="content"><div class="sheader"><div class="poster">\s?<img src="(?\'img_poster\'.+?)".+?<div class="data"><h1>(?\'titulo_anime\'.+?)<\/h1>.+?<div class="resumotemp"><div class="wp-content"><p>.+?<br \/>\s?(?\'resumo\'.+?)<\/p>.+?<\/div><\/div><\/div>/m';
$reg_temp = '/<div class="se-c"><div class="se-q"> <span class="se-t.+?">.+?<\/span>\s?(?:<span class="title">(?\'temporada\'.+?)<\/span><\/div><div  class="se-a" style=\'display:block\'>(?\'html_episodios\'<ul class="episodios"><li>.+?<\/li><\/ul>))+?<\/div><\/div>/';
$reg_ep ='/<li><div class="imagen"><a.+?><img src="(?\'link_img\'.+?)"><\/a><\/div><div class="numerando">(?\'episodio\'.+?)<\/div><div class="episodiotitle">\s?<a href="(?\'link\'.+?)">.+?<\/a>\s?<span class="date">(?\'data\'.+?)<\/span><\/div><\/li>/';

$regexLinks = array(
    '/(?<="imagen"><a href=")https:\/\/animesonline\.cc\/episodio\/.+?(?=.+">\<img)\//m', //Pega os links
    '/https:\/\/www\.blogger\.com\/video\.g.+?(?=")/m' //Pega a url do video
);

$regexInfoAnime = array(
    array($reg_anime, 0, REGEX_NOME_MEDIA, "titulo_anime"),
    array($reg_anime, 0, REGEX_POSTER, "img_poster"),
    array($reg_anime, 0, REGEX_SINOPSE, "resumo"),
);

$regexInfoEpisodios = array(
    array('/(?<=<div class="imgep"><img width="277" height="156" src=").+?(?=")/m', 1, REGEX_IMAGEM_EPISODIO),
    array('/(?<=<div itemprop="description" class="wp-content"><p>)(?:.+?)(?\'temporada\'\d) ep/m', 1, REGEX_TEMPORADA_EPISODIO, "temporada"),
    array('/(?:Episodio|Ova) \d+(?=<\/span><\/span><\/span><\/span><\/p><\/div>)/m', 1, REGEX_NOME_EPISODIO),
    array('/(?<=<div itemprop="description" class="wp-content"><p>).+?ep (?\'episodio\'\d+) HD, Animes Online.<\/p><div class="imgep"/m', 1, REGEX_NUMERO_EPISODIO, "episodio")
);

$info = new ExtractData2("https://animesonline.cc/anime/shingeki-no-kyojin/", $regexLinks, $regexInfoAnime, $regexInfoEpisodios);

echo $info->extract();
