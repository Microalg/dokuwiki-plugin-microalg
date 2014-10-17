dokuwiki-plugin-microalg
========================

Pitch
-----

Ce plugin permet à votre installation de [Dokuwiki](http://dokuwiki.org/) :

* d’inclure des échantillons interactifs de code [MicroAlg](http://microalg.info)
  (voir par exemple [la galerie MicroAlg](http://galerie.microalg.info/)) ;
* d’exporter chaque page sous la forme de scripts `.malg` (voir tout en bas
  de chaque page de [la galerie MicroAlg](http://galerie.microalg.info/) le lien
  `Télécharger le .malg`).

Contenu
-------

Le plugin est constitué de :

* `syntax.php`, qui transforme le contenu des balises `(MicroAlg)` en
  échantillon interactif (lors du rendu html) ;
* `render.php`, qui apporte un rendu `microalg` pour l’export des pages vers
  des fichiers `.malg`. Son travail est de mettre la page à plat (voir le
  [plugin:text](https://www.dokuwiki.org/plugin:text)) et d’ajouter un `#`
  devant chaque ligne (commentaire PicoLisp) qui ne serait pas du code MicroAlg.

Installation
------------

En plus d’une installation classique d’un plugin Dokuwiki (renommer si besoin
le répertoire en `microalg`), il vous faudra :

**1)** Ajouter un fichier `meta.html` (fourni) à votre template :

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="emulisp/emulisp_core.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="web/style.css" />
    <script type="text/javascript" src="web/ide_injections.js"></script>
    <script type="text/javascript" src="web/parenedit.js"></script>
    <link type="text/css" href="web/parenedit.css" rel="stylesheet" />
    <script type="text/javascript" src="web/showdown.js"></script>

**2)** Pour l’export des pages en `.malg`, ajouter ceci quelque part :

    <div class="docInfo">
        <a href="<?php echo exportlink($ID, 'microalg')?>">Télécharger le .malg</a>
    </div>

(qui ne fait rien d’autre qu’ajouter `?do=export_microalg` à l’URL).

**3)** Une idée pour `inc/lang/fr/edit.txt` :

    Modifiez cette page puis cliquez sur « Aperçu ». Une fois satisfaite, cliquer sur « Enregistrer ».  
    Pour du code *MicroAlg*, n’oubliez pas les balises:

        (MicroAlg "nom_du_prg")
        ... ici du code MicroAlg ...
        (/MicroAlg)

    Voyez le [[:wiki:syntax|guide de mise en page]] pour une aide à propos du formatage.

Communication
-------------

Merci aux auteurs du [plugin:text](https://www.dokuwiki.org/plugin:text).

Si jamais vous vous servez de ce plugin, merci de faire un signe !
