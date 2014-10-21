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

**1)** Ajouter un fichier `meta.html` (fourni) à votre template (un lien
symbolique suffira) :

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="microalg/emulisp/emulisp_core.js" type="text/javascript"></script>
    <link  href="microalg/web/style.css" rel="stylesheet" type="text/css" />
    <script src="microalg/web/ide_injections.js" type="text/javascript"></script>
    <script src="microalg/web/parenedit.js" type="text/javascript"></script>
    <link  href="microalg/web/parenedit.css" rel="stylesheet" type="text/css" />
    <script src="microalg/web/showdown.js" type="text/javascript"></script>

**2)** Les six derniers fichiers appelés par `meta.html` sont fournis dans
[les distribution de MicroAlg](https://github.com/Microalg/Microalg). L’idée
est d’amener le répertoire `microalg` à côté de `doku.php` via `git` ou en
téléchargeant une [release](https://github.com/Microalg/Microalg/releases).

**3)** Pour l’export des pages en `.malg`, ajouter ceci quelque part :

    <div class="docInfo">
        <a href="<?php echo exportlink($ID, 'microalg')?>">Télécharger le .malg</a>
    </div>

(qui ne fait rien d’autre qu’ajouter `?do=export_microalg` à l’URL).

**4)** Une idée pour ajouter des instructions lors de l’édition d’une page est
de modifier `inc/lang/fr/edit.txt` (ou utiliser celui fourni) :

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
