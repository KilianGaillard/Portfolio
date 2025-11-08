
// la référence pour apprendre Javascript facilement : 
//    http://www.w3schools.com/js/default.asp
// le code ci-dessous contient les références aux pages concernées de ce site
    
// 2D context : objet dans lequel on va réaliser les dessins (en 2D)
// --> voir http://www.w3schools.com/jsref/dom_obj_canvas.asp
//  pour connaître les fonctions offertes par cet objet

// --> voir http://www.html5canvastutorials.com 
//  pour tout savoir de l'utilisation des canvas

// --> voir http://www.html5rocks.com/en/tutorials/canvas/performance/
//  pour tout savoir sur l'optimisation des performances dans les canvas
let context = null;
    
// rectangle dans le jeu :
// utilisation du format JSON pour définir la structure de cet objet 
// --> voir http://www.w3schools.com/js/js_json.asp
// c'est un format clé: valeur, similaire aux types structurés vu au S2
let rectangle = { x: 0, y: 0, hauteur: 64, largeur: 64, couleur: "red" } ;
rectangle.vecY = 0 ;
rectangle.vecX = 0 ;
let tabCouleur = ["red","green","blue"] ;
rectangle.couleurC = 0 ;

// clic sur la zone de jeu (coordonnées relatives au canvas)
let clic = { x: 0, y: 0 };
    


// initialisation (appelée au chargement du corps du document <body onload="init">)    
function init() {
    // instanciation de la variable globale contenant le contexte
    context = document.getElementById("cvs").getContext("2d");
    context.width = document.getElementById("cvs").width;
    context.height = document.getElementById("cvs").height;
    rectangle.x = context.width/2 - rectangle.largeur/2
    rectangle.y = context.height/2 - rectangle.hauteur/2
    
    tabObstacles = creeObstacle()
    // on associe au document des écouteurs d'événements
    // --> voir http://www.w3schools.com/js/js_events.asp
    
    // 2 écouteurs pour le clavier (appui/relâchement d'une touche)
    document.addEventListener("keydown", captureAppuiToucheClavier)
    document.addEventListener("keyup", captureRelacheToucheClavier)
    // on associe au document un écouteur d'événements souris
    document.addEventListener("click", captureClicSouris)
    // --> ces événements vont appeler les fonctions captureXYZ définies après.
                    
    // lancement de la boucle de jeu,
    boucleDeJeu();
}

let Datenew = Date.now()
function boucleDeJeu() {
    // mise à jour de l'état du jeu
    update(Date.now()-Datenew);    
    // affichage de l'état du jeu
    render();
    // rappel de la boucle de jeu 
    // requestAnimationFrame est une fonction JS servant à pré-calculer le prochain affichage
    //  http://www.html5canvastutorials.com/advanced/html5-canvas-animation-stage/
    
    requestAnimationFrame(boucleDeJeu);
    Datenew = Date.now()
}


/**
 *  Mise à jour de l'état du jeu
 *  @param  d   date courante
 */
function update(dt) {

    ylocal = rectangle.y + rectangle.vecY*dt*0.5 ;
    xlocal = rectangle.x + rectangle.vecX*dt*0.5 ;
    
    if (xlocal + rectangle.largeur > context.width){
        xlocal = context.width - rectangle.largeur
    } 
    if (xlocal < 0){
        xlocal = 0
    }

    if (ylocal + rectangle.hauteur > context.height){
        ylocal = context.height - rectangle.hauteur
    } 
    if (ylocal < 0){
        ylocal = 0
    }

    if (collisions({x:xlocal,y:rectangle.y,largeur:rectangle.largeur,hauteur:rectangle.hauteur},tabObstacles)){
        obstacle = collisions({x:xlocal,y:rectangle.y,largeur:rectangle.largeur,hauteur:rectangle.hauteur},tabObstacles)
        if (rectangle.vecX == 1){
            xlocal = obstacle.x - rectangle.largeur
        } else {
            xlocal = obstacle.x + obstacle.largeur
        }
    }

    if (collisions({x:rectangle.x,y:ylocal,largeur:rectangle.largeur,hauteur:rectangle.hauteur},tabObstacles)){
        obstacle = collisions({x:rectangle.x,y:ylocal,largeur:rectangle.largeur,hauteur:rectangle.hauteur},tabObstacles)
        if (rectangle.vecY == 1){
            ylocal = obstacle.y - rectangle.hauteur
        } else {
            ylocal = obstacle.y + obstacle.hauteur
        }
    }

    rectangle.x = xlocal
    rectangle.y = ylocal
}


/**
 *  Fonction réalisant le rendu de l'état du jeu
 */
function render() {
    // effacement de l'écran
    context.fillStyle = tabCouleur[rectangle.couleurC] ;
    context.clearRect(0, 0, context.width, context.height);
    context.fillRect(rectangle.x,rectangle.y,rectangle.largeur,rectangle.hauteur);

    context.fillStyle = "black";
    for (i=0;i<tab.length;i++) {
        context.fillRect(tabObstacles[i].x,tabObstacles[i].y,tabObstacles[i].largeur,tabObstacles[i].hauteur);
        
    }
}


/**
 *  Fonction appelée lorsqu'une touche du clavier est appuyée
 *  Associée à l'événement "keyDown"
 */
function captureAppuiToucheClavier(event) {
    // pratique pour connaître les keyCode des touches du clavier :
    //  --> http://www.cambiaresearch.com/articles/15/javascript-key-codes
    if (event.code == "ArrowUp"){
        rectangle.vecY = -1 ;
    }
    if (event.code == "ArrowDown"){
        rectangle.vecY = 1 ;
    }
    if (event.code == "ArrowLeft"){
        rectangle.vecX = -1 ;
    }
    if (event.code == "ArrowRight"){
        rectangle.vecX = 1 ;
    }
}

/**
 *  Fonction appelée lorsqu'une touche du clavier est relâchée
 *  Associée à l'événement "keyUp"
 */
function captureRelacheToucheClavier(event) {
    if (event.code == "ArrowUp"){
        rectangle.vecY = 0 ;
    }
    if (event.code == "ArrowDown"){
        rectangle.vecY = 0 ;
    }
    if (event.code == "ArrowLeft"){
        rectangle.vecX = 0 ;
    }
    if (event.code == "ArrowRight"){
        rectangle.vecX = 0 ;
    }
}

/**
 *  Fonction appelée lorsqu'une touche du clavier est relâchée
 *  Associée à l'événement "click"
 */
function captureClicSouris(event) {
    // calcul des coordonnées de la souris dans le canvas
    if (event.target.id == "cvs") {
        clic.x = event.pageX - event.target.offsetLeft;
        clic.y = event.pageY - event.target.offsetTop;

        if (clic.x >= rectangle.x && clic.x <= rectangle.x + rectangle.largeur && clic.y >= rectangle.y && clic.y <= rectangle.y + rectangle.hauteur){
            rectangle.couleurC = (rectangle.couleurC + 1) % tabCouleur.length
        }
    }
}
    
function Nrandom (a , b){
    return Math.floor(Math.random() * (b-a)+1) + a
}

function creeObstacle () {
    tab = []
    n = 5
    while (tab.length < n) {
        let obst = {}

        obst.largeur = Nrandom (20 , 200)
        obst.hauteur = Nrandom (20 , 200)
        obst.x = Nrandom (0 , context.width - obst.largeur) 
        obst.y = Nrandom (0 , context.height - obst.hauteur)
        
        if ((rectangle.x+rectangle.largeur <= obst.x)||(rectangle.y+rectangle.hauteur <= obst.y)||(rectangle.x >= obst.x+obst.largeur)||(rectangle.x >= obst.x+obst.largeur)){
            tab.push(obst); 
        }
        

    }

    return tab

}

function collisions (rec , tab) {
    for (i=0;i<tab.length;i++){
        if (!((rec.x+rec.largeur <= tab[i].x)||(rec.y+rec.hauteur <= tab[i].y)||(rec.x >= tab[i].x+tab[i].largeur)||(rec.y >= tab[i].y+tab[i].hauteur))){
            return tab[i]
        }
    }
    return null
}


